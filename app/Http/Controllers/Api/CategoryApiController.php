<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryApiController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active',true)->withCount('products')->orderBy('sort_order')->get();
        return response()->json(['success'=>true,'data'=>$categories]);
    }
}
