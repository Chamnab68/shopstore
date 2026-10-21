<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Cart, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartApiController extends Controller
{
    public function index(Request $request)
    {
        $items = Cart::where('user_id',$request->user()->id)->with('product')->get();
        $total = $items->sum(fn($i)=>($i->product->sale_price ?? $i->product->price) * $i->quantity);
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
