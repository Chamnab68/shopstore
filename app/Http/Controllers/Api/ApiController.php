<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Cart, Order, OrderItem, ApiKey};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Validator};
use Laravel\Sanctum\PersonalAccessToken;

// ─── Auth API ─────────────────────────────────────────────
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'customer',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['success'=>true,'token'=>$token,'user'=>$user], 201);
    }

    public function login(Request $request)
    {
        $v = Validator::make($request->all(), ['email'=>'required|email','password'=>'required']);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success'=>false,'message'=>'Invalid credentials.'], 401);
        }
        if (!$user->is_active) {
            return response()->json(['success'=>false,'message'=>'Account is disabled.'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['success'=>true,'token'=>$token,'user'=>$user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success'=>true,'message'=>'Logged out.']);
    }

    public function me(Request $request)
    {
        return response()->json(['success'=>true,'user'=>$request->user()]);
    }
}

// ─── Product API ───────────────────────────────────────────
class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        if ($request->category)    $query->whereHas('category', fn($q)=>$q->where('slug',$request->category));
        if ($request->search)      $query->where('name','like','%'.$request->search.'%');
        if ($request->featured)    $query->where('is_featured',true);
        if ($request->min_price)   $query->where('price','>=',$request->min_price);
        if ($request->max_price)   $query->where('price','<=',$request->max_price);
        if ($request->sort === 'price_asc')  $query->orderBy('price');
        elseif ($request->sort === 'price_desc') $query->orderByDesc('price');
        elseif ($request->sort === 'newest') $query->latest();
        elseif ($request->sort === 'rating') $query->orderByDesc('rating');
        else $query->orderByDesc('sold_count');

        $perPage = min($request->per_page ?? 12, 100);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $products->items(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ]
        ]);
    }

    public function show($slug)
    {
        $product = Product::where('is_active', true)->where('slug',$slug)->with('category')->firstOrFail();
        return response()->json(['success'=>true,'data'=>$product]);
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug',$slug)->where('is_active',true)->firstOrFail();
        $products = Product::where('is_active', true)->where('category_id',$category->id)->with('category')->paginate(12);
        return response()->json(['success'=>true,'category'=>$category,'data'=>$products->items(),'meta'=>['total'=>$products->total()]]);
    }
}

// ─── Category API ──────────────────────────────────────────
class CategoryApiController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        return response()->json(['success'=>true,'data'=>$categories]);
    }
}

// ─── Cart API ──────────────────────────────────────────────
class CartApiController extends Controller
{
    public function index(Request $request)
    {
        $items = Cart::where('user_id',$request->user()->id)->with('product')->get();
        $total = $items->sum(fn($i)=>$i->product->current_price * $i->quantity);
        return response()->json(['success'=>true,'data'=>$items,'total'=>$total,'count'=>$items->sum('quantity')]);
    }

    public function add(Request $request)
    {
        $v = Validator::make($request->all(),['product_id'=>'required|exists:products,id','quantity'=>'integer|min:1']);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $product = Product::findOrFail($request->product_id);
        if ($product->stock < ($request->quantity ?? 1)) {
            return response()->json(['success'=>false,'message'=>'Not enough stock.'],422);
        }

        $cart = Cart::where('user_id',$request->user()->id)->where('product_id',$product->id)->first();
        if ($cart) {
            $cart->increment('quantity', $request->quantity ?? 1);
        } else {
            Cart::create(['user_id'=>$request->user()->id,'product_id'=>$product->id,'quantity'=>$request->quantity??1]);
        }

        return response()->json(['success'=>true,'message'=>'Added to cart.']);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::where('user_id',$request->user()->id)->findOrFail($id);
        $cart->update(['quantity'=>$request->quantity]);
        return response()->json(['success'=>true,'message'=>'Cart updated.']);
    }

    public function remove(Request $request, $id)
    {
        Cart::where('user_id',$request->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'Item removed.']);
    }
}

// ─── Order API ─────────────────────────────────────────────
class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id',$request->user()->id)->with('items')->latest()->paginate(10);
        return response()->json(['success'=>true,'data'=>$orders->items(),'meta'=>['total'=>$orders->total()]]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'shipping_name'    => 'required|string',
            'shipping_phone'   => 'required|string',
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|in:bank_transfer,cash_on_delivery',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $cartItems = Cart::where('user_id',$request->user()->id)->with('product')->get();
        if ($cartItems->isEmpty()) return response()->json(['success'=>false,'message'=>'Cart is empty.'],422);

        $subtotal    = $cartItems->sum(fn($i)=>$i->product->current_price * $i->quantity);
        $shippingFee = $subtotal > 50 ? 0 : 3.99;
        $total       = $subtotal + $shippingFee;

        \DB::transaction(function() use ($request,$cartItems,$subtotal,$shippingFee,$total,&$order) {
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
                    'order_id'     => $order->id, 'product_id' => $item->product_id,
                    'product_name' => $item->product->name, 'product_image'=>$item->product->image,
                    'price'        => $item->product->price, 'sale_price'=>$item->product->sale_price,
                    'quantity'     => $item->quantity, 'subtotal'=>$item->product->current_price * $item->quantity,
                ]);
                $item->product->decrement('stock',$item->quantity);
            }
            Cart::where('user_id',$request->user()->id)->delete();
        });

        return response()->json(['success'=>true,'message'=>'Order placed!','order'=>$order->load('items')],201);
    }

    public function show(Request $request, $id)
    {
        $order = Order::where('user_id',$request->user()->id)->with('items.product')->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$order]);
    }

    public function uploadReceipt(Request $request, $id)
    {
        $v = Validator::make($request->all(),['receipt'=>'required|image|max:5120']);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $order = Order::where('user_id',$request->user()->id)->findOrFail($id);
        $path  = $request->file('receipt')->store('receipts','public');
        $order->update(['receipt_image'=>$path,'receipt_uploaded_at'=>now(),'payment_status'=>'paid']);

        return response()->json(['success'=>true,'message'=>'Receipt uploaded.']);
    }
}

// ─── API Key Management ────────────────────────────────────
class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $keys = ApiKey::where('user_id',$request->user()->id)->get();
        return response()->json(['success'=>true,'data'=>$keys]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'name'        => 'required|string|max:100',
            'permissions' => 'array',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $key = ApiKey::generate($request->name, $request->user()->id, $request->permissions ?? ['read']);
        return response()->json(['success'=>true,'data'=>$key,'key'=>$key->key,'message'=>'Save this key, it will not be shown again!'],201);
    }

    public function destroy(Request $request, $id)
    {
        ApiKey::where('user_id',$request->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'API key deleted.']);
    }
}
