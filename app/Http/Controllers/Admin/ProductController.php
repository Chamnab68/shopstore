<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->search) $query->where('name','like','%'.$request->search.'%');
        if ($request->category_id) $query->where('category_id',$request->category_id);
        if ($request->status === 'active') $query->where('is_active',true);
        elseif ($request->status === 'inactive') $query->where('is_active',false);

        $products = $query->latest()->paginate(20)->appends($request->all());
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.index', compact('products','categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:200',
            'sku'               => 'required|string|unique:products',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:300',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'unit'              => 'required|string|max:20',
            'image'             => 'nullable|image|max:2048',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'is_organic'        => 'boolean',
            'is_cold_sale'      => 'boolean',
            'is_reward_only'    => 'boolean', // <--- បន្ថែមទីនេះ
            'sale_ends_at'      => 'nullable|date',
        ]);

        $data['slug'] = Str::slug($data['name']);
        // Make slug unique
        $count = 1;
        $baseSlug = $data['slug'];
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $count++;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active']      = $request->boolean('is_active');
        $data['is_featured']    = $request->boolean('is_featured');
        $data['is_organic']     = $request->boolean('is_organic');
        $data['is_cold_sale']   = $request->boolean('is_cold_sale');
        $data['is_reward_only'] = $request->boolean('is_reward_only'); // <--- បន្ថែមទីនេះ

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $product    = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:200',
            'sku'               => 'required|string|unique:products,sku,'.$id,
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:300',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'unit'              => 'required|string|max:20',
            'image'             => 'nullable|image|max:2048',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'is_organic'        => 'boolean',
            'is_cold_sale'      => 'boolean',
            'is_reward_only'    => 'boolean',
            'sale_ends_at'      => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // កំណត់តម្លៃ Boolean ឱ្យបានច្បាស់លាស់មុនពេល Update
        $data['is_active']      = $request->boolean('is_active');
        $data['is_featured']    = $request->boolean('is_featured');
        $data['is_organic']     = $request->boolean('is_organic');
        $data['is_cold_sale']   = $request->boolean('is_cold_sale');
        $data['is_reward_only'] = $request->boolean('is_reward_only'); // <--- ចំណុចនេះដែលខ្វះកន្លែង Update

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return response()->json(['success' => true]);
    }
}