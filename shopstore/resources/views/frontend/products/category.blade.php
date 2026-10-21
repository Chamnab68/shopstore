@extends('layouts.app')
@section('title', $category->name . ' - ShopStore')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $category->name }}</h2>
            <span class="text-muted">{{ $products->total() }} products</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="bg-white rounded-3 p-3 shadow-sm sticky-top" style="top:100px">
                <h6 class="fw-bold mb-3">All Categories</h6>
                <div class="list-group list-group-flush">
                    <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action">All Products</a>
                    @foreach($categories as $cat)
                    <a href="{{ route('products.category',$cat->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between {{ $cat->id === $category->id ? 'active' : '' }}">
                        {{ $cat->name }}
                        <span class="badge bg-light text-dark">{{ $cat->products_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No products in this category yet</h5>
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
