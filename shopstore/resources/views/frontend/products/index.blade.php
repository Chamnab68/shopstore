@extends('layouts.app')
@section('title','All Products - ShopStore')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="bg-white rounded-3 p-3 shadow-sm sticky-top" style="top:100px">
                <h6 class="fw-bold mb-3">Categories</h6>
                <div class="list-group list-group-flush">
                    <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">
                        All Products
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('products.index',['category'=>$cat->slug]) }}" class="list-group-item list-group-item-action d-flex justify-content-between {{ request('category')===$cat->slug?'active':'' }}">
                        {{ $cat->name }}
                        <span class="badge bg-light text-dark">{{ $cat->products_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="col-lg-9">
            {{-- Toolbar --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="text-muted" style="font-size:13px">{{ $products->total() }} products found</div>
                <form method="GET" class="d-flex gap-2">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <select name="sort" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
                        <option value="">Most Popular</option>
                        <option value="newest" {{ request('sort')==='newest'?'selected':'' }}>Newest</option>
                        <option value="price_asc" {{ request('sort')==='price_asc'?'selected':'' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort')==='price_desc'?'selected':'' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort')==='rating'?'selected':'' }}>Top Rated</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-2">Browse All Products</a>
            </div>
            @else
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-6 col-md-4">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
