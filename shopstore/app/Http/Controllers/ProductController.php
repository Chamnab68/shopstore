<?php

namespace App\Http\Controllers;

use App\Models\{Category, Product};
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->sort === 'price_asc') $query->orderBy('price');
        elseif ($request->sort === 'price_desc') $query->orderByDesc('price');
        elseif ($request->sort === 'newest') $query->latest();
        elseif ($request->sort === 'rating') $query->orderByDesc('rating');
        else $query->orderByDesc('sold_count');

        $products = $query->paginate(12)->appends($request->all());
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();

        return view('frontend.products.index', compact('products','categories'));
    }

    public function show($slug)
    {
        $product = Product::where('is_active', true)->where('slug', $slug)->with('category')->firstOrFail();
        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();
        return view('frontend.products.show', compact('product','related'));
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products = Product::where('is_active', true)->where('category_id', $category->id)->with('category')->paginate(12);
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        return view('frontend.products.category', compact('category','products','categories'));
    }
}
