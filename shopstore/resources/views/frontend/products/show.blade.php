@extends('layouts.app')
@section('title', $product->name . ' - ShopStore')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.category',$product->category->slug) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($product->name,30) }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Product Image --}}
        <div class="col-lg-5">
            <div class="bg-white rounded-3 p-4 text-center" style="border:1px solid #eee">
                @if($product->discount_percent > 0)
                <div class="badge text-white mb-2" style="background:var(--secondary);font-size:14px">-{{ $product->discount_percent }}% OFF</div>
                @endif
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-height:400px;max-width:100%;object-fit:contain" id="main-img">

                {{-- Thumbnail Gallery --}}
                @if($product->images)
                <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                    <img src="{{ $product->image_url }}" onclick="document.getElementById('main-img').src=this.src"
                         style="width:60px;height:60px;object-fit:contain;border:2px solid #eee;border-radius:8px;cursor:pointer">
                    @foreach($product->images as $img)
                    <img src="{{ asset('storage/'.$img) }}" onclick="document.getElementById('main-img').src=this.src"
                         style="width:60px;height:60px;object-fit:contain;border:2px solid #eee;border-radius:8px;cursor:pointer">
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-7">
            <div class="d-flex gap-2 mb-2">
                @if($product->category)<a href="{{ route('products.category',$product->category->slug) }}" class="badge bg-light text-primary text-decoration-none">{{ $product->category->name }}</a>@endif
                @if($product->is_organic)<span class="badge bg-success">🌿 Organic</span>@endif
                @if($product->is_cold_sale)<span class="badge bg-info">❄️ Cold Sale</span>@endif
                @if($product->is_featured)<span class="badge bg-warning text-dark">⭐ Featured</span>@endif
            </div>

            <h1 class="fw-bold mb-3" style="font-size:1.8rem">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="text-warning">
                    @for($i=1;$i<=5;$i++)
                    <i class="fa{{ $i<=$product->rating?'s':'r' }} fa-star"></i>
                    @endfor
                    <span class="text-muted ms-1" style="font-size:13px">({{ $product->review_count }} reviews)</span>
                </div>
                <span class="text-muted" style="font-size:13px">{{ number_format($product->sold_count) }} sold</span>
            </div>

            <div class="mb-4">
                <span class="text-primary fw-bold" style="font-size:2.2rem">${{ number_format($product->current_price,2) }}</span>
                @if($product->sale_price)
                <span class="text-muted ms-2" style="font-size:1.1rem;text-decoration:line-through">${{ number_format($product->price,2) }}</span>
                <span class="badge bg-danger ms-2">Save ${{ number_format($product->price - $product->sale_price,2) }}</span>
                @endif
            </div>

            @if($product->short_description)
            <p class="text-muted mb-4">{{ $product->short_description }}</p>
            @endif

            {{-- Stock --}}
            <div class="mb-4">
                @if($product->stock > 0)
                <span class="badge bg-success"><i class="fas fa-check me-1"></i>In Stock ({{ $product->stock }} {{ $product->unit }} available)</span>
                @else
                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Out of Stock</span>
                @endif
            </div>

            {{-- Quantity + Add to Cart --}}
            @if($product->stock > 0)
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-flex align-items-center border rounded-3 overflow-hidden">
                    <button class="btn btn-light px-3 py-2 fw-bold" onclick="changeQty(-1)">−</button>
                    <input type="number" id="qty" value="1" min="1" max="{{ $product->stock }}" class="form-control border-0 text-center fw-bold" style="width:60px">
                    <button class="btn btn-light px-3 py-2 fw-bold" onclick="changeQty(1)">+</button>
                </div>
                <button class="btn btn-primary fw-bold py-2 px-4 flex-grow-1" onclick="addToCartWithQty({{ $product->id }})">
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
            </div>
            @endif

            {{-- Product Meta --}}
            <div class="border-top pt-3" style="font-size:13px">
                <div class="mb-1"><span class="text-muted">SKU:</span> <strong>{{ $product->sku }}</strong></div>
                <div class="mb-1"><span class="text-muted">Unit:</span> <strong>{{ $product->unit }}</strong></div>
                @if($product->category)<div class="mb-1"><span class="text-muted">Category:</span> <a href="{{ route('products.category',$product->category->slug) }}" class="text-primary">{{ $product->category->name }}</a></div>@endif
            </div>
        </div>
    </div>

    {{-- Description Tab --}}
    @if($product->description)
    <div class="mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Product Description</h5>
                <div class="text-muted">{{ $product->description }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Related Products --}}
    @if($related->count())
    <div class="mt-5">
        <h4 class="fw-bold mb-4">Related Products</h4>
        <div class="row g-3">
            @foreach($related as $rp)
            <div class="col-6 col-md-3">
                @include('partials.product-card', ['product' => $rp])
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function changeQty(delta) {
    const el = document.getElementById('qty');
    const max = {{ $product->stock }};
    el.value = Math.max(1, Math.min(max, parseInt(el.value) + delta));
}

function addToCartWithQty(productId) {
    const qty = parseInt(document.getElementById('qty').value);
    addToCart(productId, qty);
}
</script>
@endpush
