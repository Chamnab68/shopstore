<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Product, Category};
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        if ($request->category)  $query->whereHas('category', fn($q)=>$q->where('slug',$request->category));
        if ($request->search)    $query->where('name','like','%'.$request->search.'%');
        if ($request->featured)  $query->where('is_featured',true);
        if ($request->min_price) $query->where('price','>=',$request->min_price);
        if ($request->max_price) $query->where('price','<=',$request->max_price);

        if ($request->sort === 'price_asc')       $query->orderBy('price');
        elseif ($request->sort === 'price_desc')  $query->orderByDesc('price');
        elseif ($request->sort === 'newest')      $query->latest();
        elseif ($request->sort === 'rating')      $query->orderByDesc('rating');
        else $query->orderByDesc('sold_count');

        $perPage  = min($request->per_page ?? 12, 100);
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
        $product = Product::where('is_active',true)->where('slug',$slug)->with('category')->firstOrFail();
        return response()->json(['success'=>true,'data'=>$product]);
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug',$slug)->where('is_active',true)->firstOrFail();
        $products = Product::where('is_active',true)->where('category_id',$category->id)->with('category')->paginate(12);
        return response()->json([
            'success'  => true,
            'category' => $category,
            'data'     => $products->items(),
            'meta'     => ['total'=>$products->total()]
        ]);
    }
}
