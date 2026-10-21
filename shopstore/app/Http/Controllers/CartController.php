<?php

namespace App\Http\Controllers;

use App\Models\{Cart, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getIdentifier(Request $request)
    {
        return Auth::check()
            ? ['user_id' => Auth::id()]
            : ['session_id' => $request->session()->getId()];
    }

    public function index(Request $request)
    {
        $cartItems = Cart::where($this->getIdentifier($request))
            ->with('product.category')
            ->get();

        $subtotal = $cartItems->sum(fn($item) => $item->product->current_price * $item->quantity);
        $shippingFee = $subtotal > 50 ? 0 : 3.99;
        $total = $subtotal + $shippingFee;

        return view('frontend.cart.index', compact('cartItems','subtotal','shippingFee','total'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'integer|min:1']);
        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Not enough stock.'], 422);
        }

        $identifier = $this->getIdentifier($request);
        $cart = Cart::where($identifier)->where('product_id', $product->id)->first();

        if ($cart) {
            $newQty = $cart->quantity + ($request->quantity ?? 1);
            if ($newQty > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Not enough stock.'], 422);
            }
            $cart->update(['quantity' => $newQty]);
        } else {
            Cart::create(array_merge($identifier, [
                'product_id' => $product->id,
                'quantity' => $request->quantity ?? 1
            ]));
        }

        $count = Cart::where($identifier)->sum('quantity');
        return response()->json(['success' => true, 'message' => 'Added to cart!', 'count' => $count]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = Cart::where($this->getIdentifier($request))->findOrFail($id);
        $cart->update(['quantity' => $request->quantity]);
        $subtotal = $cart->product->current_price * $cart->quantity;
        return response()->json(['success' => true, 'subtotal' => number_format($subtotal, 2)]);
    }

    public function remove(Request $request, $id)
    {
        Cart::where($this->getIdentifier($request))->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Removed from cart.']);
    }

    public function count(Request $request)
    {
        $count = Cart::where($this->getIdentifier($request))->sum('quantity');
        return response()->json(['count' => $count]);
    }
}
