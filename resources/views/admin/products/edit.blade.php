@extends('layouts.app') {{-- ឬ layout របស់ admin តាមដែលបងប្រើ --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Edit Product: {{ $product->name }}</h5>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Basic Information --}}
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Product Name *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">SKU *</label>
                                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Category *</label>
                                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Short Description</label>
                                    <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Description</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                                </div>

                                <div class="card p-3 bg-light border-0">
                                    <h6 class="fw-bold mb-3">Pricing & Inventory</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Regular Price ($) *</label>
                                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sale Price ($)</label>
                                            <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Stock Quantity *</label>
                                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sidebar Options & Image --}}
                            <div class="col-lg-4">
                                <div class="card p-3 mb-3 bg-light border-0">
                                    <label class="form-label fw-bold">Product Image</label>
                                    @if($product->image)
                                        <div class="mb-2 text-center">
                                            <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail" style="max-height: 120px; object-fit: contain;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control">
                                </div>

                                <div class="card p-3 bg-light border-0">
                                    <h6 class="fw-bold mb-3">Product Options & Loyalty</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Bonus Point Multiplier</label>
                                        <select name="point_multiplier" class="form-select">
                                            <option value="1" {{ old('point_multiplier', $product->point_multiplier) == 1 ? 'selected' : '' }}>1x (Standard)</option>
                                            <option value="2" {{ old('point_multiplier', $product->point_multiplier) == 2 ? 'selected' : '' }}>2x Points</option>
                                            <option value="3" {{ old('point_multiplier', $product->point_multiplier) == 3 ? 'selected' : '' }}>3x Points</option>
                                        </select>
                                    </div>

                                    {{-- Reward Only Checkbox --}}
                                   <div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="is_reward_only" name="is_reward_only" value="1" 
        {{ old('is_reward_only', $product->is_reward_only ?? 0) ? 'checked' : '' }}>
    <label class="form-check-label fw-bold text-danger" for="is_reward_only">🎁 Reward-Only (Points Only)</label>
</div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" 
                                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active (visible in store)</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" 
                                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Featured Product</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_organic" name="is_organic" value="1" 
                                            {{ old('is_organic', $product->is_organic) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_organic">Organic Badge</label>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-15 py-2 fw-bold text-white">
                                        <i class="fas fa-save me-1"></i> Update Product
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection