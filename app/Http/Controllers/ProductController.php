<?php

namespace App\Http\Controllers;

use App\Models\{Category, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products on the frontend.
     */
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

    /**
     * Display the specified product on the frontend.
     */
    public function show($slugOrId)
{
    // ឆែកមើលថាតើវាជា Slug (អក្សរ) ឬជា ID (លេខ)
    $product = Product::where('slug', $slugOrId)
        ->orWhere('id', $slugOrId)
        ->with('category')
        ->firstOrFail();
    
    $related = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)->get();
        
    return view('frontend.products.show', compact('product', 'related'));
}

    /**
     * Display products by category.
     */
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products = Product::where('is_active', true)->where('category_id', $category->id)->with('category')->paginate(12);
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        
        return view('frontend.products.category', compact('category','products','categories'));
    }

    /**
     * Store a newly created product in storage (Admin Panel).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'unit' => 'nullable|string',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sale_ends_at' => 'nullable|date',
            'point_multiplier' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($request->name);
        
        // Handling Checkboxes properly
        $validated['is_reward_only'] = $request->has('is_reward_only') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;
        $validated['is_organic'] = $request->has('is_organic') ? 1 : 0;
        $validated['is_cold_sale'] = $request->has('is_cold_sale') ? 1 : 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Update the specified product in storage (Admin Panel).
     */
    public function update(Request $request, Product $product)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'unit' => 'nullable|string',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sale_ends_at' => 'nullable|date',
            'point_multiplier' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($request->name);

        // Handling Checkboxes properly (Fixing the issue where they turn off automatically)
        $validated['is_reward_only'] = $request->has('is_reward_only') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;
        $validated['is_organic'] = $request->has('is_organic') ? 1 : 0;
        $validated['is_cold_sale'] = $request->has('is_cold_sale') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }
}