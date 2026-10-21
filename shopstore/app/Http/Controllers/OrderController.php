<?php

namespace App\Http\Controllers;

use App\Models\{Cart, Order, OrderItem, Product};
use App\Services\{TelegramService, NotificationService};
use App\Notifications\OrderPlacedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Mail, Storage};

class OrderController extends Controller
{
    public function __construct(
        private TelegramService $telegram,
        private NotificationService $notification
    ) {}

    public function checkout(Request $request)
    {
        $this->middleware('auth');
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(fn($i) => $i->product->current_price * $i->quantity);
        $shippingFee = $subtotal > 50 ? 0 : 3.99;
        $total = $subtotal + $shippingFee;

        $bankInfo = [
            'name' => config('app.bank_name', 'ABA Bank'),
            'account_name' => config('app.bank_account_name', 'ShopStore'),
            'account_number' => config('app.bank_account_number', '000123456'),
        ];

        return view('frontend.checkout.index', compact('cartItems','subtotal','shippingFee','total','bankInfo'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_name'    => 'required|string|max:100',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'notes'            => 'nullable|string|max:500',
            'payment_method'   => 'required|in:bank_transfer,cash_on_delivery',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Sorry, {$item->product->name} is out of stock.");
            }
        }

        $subtotal = $cartItems->sum(fn($i) => $i->product->current_price * $i->quantity);
        $shippingFee = $subtotal > 50 ? 0 : 3.99;
        $total = $subtotal + $shippingFee;

        DB::transaction(function () use ($request, $cartItems, $subtotal, $shippingFee, $total) {
            $order = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'user_id'          => Auth::id(),
                'status'           => Order::STATUS_PENDING,
                'payment_status'   => Order::PAYMENT_UNPAID,
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
                    'subtotal'      => $item->product->current_price * $item->quantity,
                ]);
                // Decrease stock
                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('sold_count', $item->quantity);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();

            // Send notifications
            try {
                $this->notification->orderPlaced($order);
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }

            session(['last_order_id' => $order->id]);
        });

        $orderId = session('last_order_id');
        return redirect()->route('orders.payment', $orderId);
    }

    public function payment($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $bankInfo = [
            'name'           => env('BANK_NAME', 'ABA Bank'),
            'account_name'   => env('BANK_ACCOUNT_NAME', 'ShopStore Co., Ltd'),
            'account_number' => env('BANK_ACCOUNT_NUMBER', '000123456'),
        ];
        return view('frontend.checkout.payment', compact('order','bankInfo'));
    }

    public function uploadReceipt(Request $request, $id)
    {
        $request->validate(['receipt' => 'required|image|mimes:jpg,jpeg,png|max:5120']);
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        $path = $request->file('receipt')->store('receipts', 'public');
        $order->update([
            'receipt_image'       => $path,
            'receipt_uploaded_at' => now(),
            'payment_status'      => 'paid',
        ]);

        try {
            $this->notification->receiptUploaded($order);
        } catch (\Exception $e) {
            \Log::error('Receipt notification failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Receipt uploaded successfully!']);
    }

    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);
        return view('frontend.orders.history', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())->with('items.product')->findOrFail($id);
        return view('frontend.orders.show', compact('order'));
    }

    public function success($id)
    {
        $order = Order::where('user_id', Auth::id())->with('items')->findOrFail($id);
        return view('frontend.checkout.success', compact('order'));
    }
}
