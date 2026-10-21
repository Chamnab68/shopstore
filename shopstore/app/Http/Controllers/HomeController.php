<?php

namespace App\Http\Controllers;

use App\Models\{Category, Product, Banner};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        $featuredProducts = Product::where('is_active', true)->featured()->with('category')->take(8)->get();
        $editorPicks = Product::where('is_active', true)->with('category')->orderBy('rating','desc')->take(8)->get();
        $bestSellers = Product::where('is_active', true)->with('category')->orderBy('sold_count','desc')->take(6)->get();
        $saleProducts = Product::where('is_active', true)->whereNotNull('sale_price')->with('category')->take(6)->get();

        return view('frontend.home', compact(
            'banners','categories','featuredProducts','editorPicks','bestSellers','saleProducts'
        ));
    }

    public function about() { return view('frontend.about'); }
    public function contact() { return view('frontend.contact'); }
}
