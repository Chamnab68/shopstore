<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $cartItems = collect();
        $subtotal = 0;

        foreach ($cart as $id => $details) {
            $product = \App\Models\Product::find($id);
            if ($product) {
                // បង្កើត object ស្រដៀងនឹង collection ដើម្បីឱ្យ Blade អាចហៅប្រើបាន
                $cartItems->put($id, (object)[
                    'product' => $product,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price']
                ]);
                $subtotal += $details['price'] * $details['quantity'];
            }
        }

        $shippingFee = $subtotal > 50 ? 0 : 0.99;

        return view('frontend.cart.index', compact('cartItems', 'subtotal', 'shippingFee'));
    }

    /**
     * Add a product to the cart (AJAX & Standard).
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        $product = Product::findOrFail($productId);

        if ($product->is_reward_only) {
            return response()->json([
                'success' => false,
                'message' => 'This item is a reward-only product and cannot be added to the cart.'
            ], 422);
        }

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this product is out of stock or insufficient stock available.'
            ], 422);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
                'sku' => $product->sku
            ];
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = max(1, (int) $request->quantity);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in cart.'
        ], 404);
    }

    /**
     * Remove item from cart.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found.'
        ], 404);
    }

    /**
     * Get cart count for header badge.
     */
    public function count()
    {
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json(['count' => $cartCount]);
    }
}