@extends('layouts.admin')
@section('title','Products')
@section('page-title','Product Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-muted" style="font-size:13px">{{ $products->total() }} products total</div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary fw-bold">
        <i class="fas fa-plus me-2"></i>Add New Product
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search product name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary btn-sm w-100"><i class="fas fa-search"></i></button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Sold</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $product->image_url }}" style="width:50px;height:50px;object-fit:contain;border-radius:8px;background:#f8f9fa;border:1px solid #eee">
                            <div>
                                <div class="fw-bold" style="font-size:13px">{{ Str::limit($product->name,40) }}</div>
                                <div class="d-flex gap-1 mt-1">
                                    @if($product->is_featured) <span class="badge bg-warning" style="font-size:10px">Featured</span> @endif
                                    @if($product->is_organic) <span class="badge bg-success" style="font-size:10px">Organic</span> @endif
                                    @if($product->is_cold_sale) <span class="badge bg-info" style="font-size:10px">Cold Sale</span> @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $product->category->name ?? '—' }}</span></td>
                    <td style="font-size:12px;color:#888">{{ $product->sku }}</td>
                    <td>
                        <div class="fw-bold text-primary">${{ number_format($product->sale_price ?? $product->price, 2) }}</div>
                        @if($product->sale_price)
                        <del class="text-muted" style="font-size:11px">${{ number_format($product->price, 2) }}</del>
                        @endif
                    </td>
                    <td>
                        @if($product->stock == 0)
                        <span class="badge bg-danger">Out of Stock</span>
                        @elseif($product->stock < 10)
                        <span class="badge bg-warning text-dark">{{ $product->stock }} (Low)</span>
                        @else
                        <span class="badge bg-success">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ number_format($product->sold_count) }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.products.destroy',$product->id) }}','Delete product?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No products found. <a href="{{ route('admin.products.create') }}">Add one</a></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $products->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
