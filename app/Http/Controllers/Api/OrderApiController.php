<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Cart, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator};

class OrderApiController extends Controller
{
    // ── Public (no auth) ─────────────────────────────────────
    public function publicIndex(Request $request)
    {
        $query = Order::with('items','user')->latest();
        if ($request->status)         $query->where('status',         $request->status);
        if ($request->payment_status) $query->where('payment_status', $request->payment_status);
        if ($request->search)         $query->where('order_number',   'like', '%'.$request->search.'%');

        $perPage = min($request->per_page ?? 20, 100);
        $orders  = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $orders->items(),
            'meta'    => [
                'total'        => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
            ]
        ]);
    }

    public function publicShow($id)
    {
        $order = Order::with('items.product','user')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $order]);
    }

    // ── Authenticated ─────────────────────────────────────────
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items')->latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data'    => $orders->items(),
            'meta'    => ['total' => $orders->total()]
        ]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'shipping_name'    => 'required|string',
            'shipping_phone'   => 'required|string',
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|in:bank_transfer,cash_on_delivery',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $cartItems = Cart::where('user_id', $request->user()->id)->with('product')->get();
        if ($cartItems->isEmpty()) return response()->json(['success'=>false,'message'=>'Cart is empty.'], 422);

        $subtotal    = $cartItems->sum(fn($i) => ($i->product->sale_price ?? $i->product->price) * $i->quantity);
        $shippingFee = $subtotal > 50 ? 0 : 3.99;
        $total       = $subtotal + $shippingFee;
        $order       = null;

        DB::transaction(function() use ($request,$cartItems,$subtotal,$shippingFee,$total,&$order) {
            $order = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'user_id'          => $request->user()->id,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'payment_method'   => $request->payment_method,
                'subtotal'         => $subtotal,
                'discount'         => 0,
                'shipping_fee'     => $shippingFee,
                'total'            => $total,
                'shipping_name'    => $request->shipping_name,
                'shipping_phone'   => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes'            => $request->notes,
            ]);
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product->name,
                    'product_image' => $item->product->image,
                    'price'         => $item->product->price,
                    'sale_price'    => $item->product->sale_price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => ($item->product->sale_price ?? $item->product->price) * $item->quantity,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }
            Cart::where('user_id', $request->user()->id)->delete();
        });

        return response()->json(['success'=>true,'message'=>'Order placed!','order'=>$order->load('items')], 201);
    }

    public function show(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->with('items.product')->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$order]);
    }

    public function uploadReceipt(Request $request, $id)
    {
        $v = Validator::make($request->all(), ['receipt'=>'required|image|max:5120']);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);
        $path  = $request->file('receipt')->store('receipts', 'public');
        $order->update(['receipt_image'=>$path,'receipt_uploaded_at'=>now(),'payment_status'=>'paid']);
        return response()->json(['success'=>true,'message'=>'Receipt uploaded.']);
    }
}
