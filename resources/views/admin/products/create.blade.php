@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit: ' . $product->name : 'Add New Product')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Products
    </a>
</div>

<form action="{{ isset($product) ? route('admin.products.update',$product->id) : route('admin.products.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    <div class="row g-4">
        {{-- Left --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Basic Information</h6>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name',$product->name??'') }}" required>
                        @error('name')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SKU *</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku',$product->sku??'SKU-'.strtoupper(substr(uniqid(),0,8))) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id',$product->category_id??'') == $cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Description</label>
                        <input type="text" name="short_description" class="form-control" value="{{ old('short_description',$product->short_description??'') }}" placeholder="One-line summary...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description',$product->description??'') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Pricing & Inventory</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Regular Price ($) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" value="{{ old('price',$product->price??'') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sale Price ($)</label>
                            <input type="number" name="sale_price" class="form-control" step="0.01" value="{{ old('sale_price',$product->sale_price??'') }}" placeholder="Optional">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Stock Quantity *</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock',$product->stock??0) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Unit</label>
                            <select name="unit" class="form-select">
                                @foreach(['pcs','kg','g','lb','oz','l','ml','pack','box','dozen','bunch'] as $u)
                                <option value="{{ $u }}" {{ old('unit',$product->unit??'pcs')===$u?'selected':'' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sale Ends At</label>
                            <input type="datetime-local" name="sale_ends_at" class="form-control" value="{{ old('sale_ends_at', isset($product) && $product->sale_ends_at ? $product->sale_ends_at->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Product Image</h6>
                    <div class="text-center mb-3">
                        <div id="image-preview" style="width:100%;height:200px;background:#f8f9fa;border-radius:12px;display:flex;align-items:center;justify-content:center;border:2px dashed #ddd;overflow:hidden">
                            @if(isset($product) && $product->image)
                            <img src="{{ $product->image_url }}" style="max-height:100%;object-fit:contain">
                            @else
                            <div class="text-muted"><i class="fas fa-image fa-3x mb-2"></i><div>Upload Image</div></div>
                            @endif
                        </div>
                    </div>
                    <input type="file" name="image" id="image-input" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <small class="text-muted">JPG, PNG, WebP. Max 2MB.</small>
                </div>
            </div>

            <!-- Loyalty & Product Options -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Product Options & Loyalty</h6>

                    <!-- Bonus Point Multiplier -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:13px">Bonus Point Multiplier</label>
                        <select name="point_multiplier" class="form-select form-select-sm">
                            <option value="1" {{ old('point_multiplier', $product->point_multiplier ?? 1) == 1 ? 'selected' : '' }}>1x (Standard)</option>
                            <option value="2" {{ old('point_multiplier', $product->point_multiplier ?? 1) == 2 ? 'selected' : '' }}>2x Points (Double)</option>
                            <option value="3" {{ old('point_multiplier', $product->point_multiplier ?? 1) == 3 ? 'selected' : '' }}>3x Points (Triple)</option>
                        </select>
                    </div>

                    <!-- Reward-Only Product Toggle -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_reward_only" id="is_reward_only" value="1"
                            {{ old('is_reward_only', isset($product) ? $product->is_reward_only : false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-danger" for="is_reward_only" style="font-size:13px">🎁 Reward-Only (Points Only)</label>
                    </div>

                    <hr class="text-muted my-3">

                    @foreach([['name'=>'is_active','label'=>'Active (visible in store)','default'=>true],['name'=>'is_featured','label'=>'Featured Product','default'=>false],['name'=>'is_organic','label'=>'🌿 Organic Badge','default'=>false],['name'=>'is_cold_sale','label'=>'❄️ Cold Sale Badge','default'=>false]] as $opt)
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="{{ $opt['name'] }}" id="{{ $opt['name'] }}" value="1"
                            {{ old($opt['name'], isset($product) ? $product->{$opt['name']} : $opt['default']) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="{{ $opt['name'] }}" style="font-size:13px">{{ $opt['label'] }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary fw-bold py-3">
                    <i class="fas fa-save me-2"></i>{{ isset($product) ? 'Update Product' : 'Create Product' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('image-preview').innerHTML = `<img src="${e.target.result}" style="max-height:100%;object-fit:contain">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush