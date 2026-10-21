<div class="product-card">
    <div class="product-img-wrap">
        @if($product->discount_percent > 0)
        <span class="product-badge badge-discount">{{ $product->discount_percent }}%</span>
        @elseif($product->is_organic)
        <span class="product-badge badge-organic">🌿 Organic</span>
        @elseif($product->is_cold_sale)
        <span class="product-badge badge-cold">❄️ Cold Sale</span>
        @endif

        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
        </a>

        <div class="product-wishlist" onclick="toggleWishlist(this, {{ $product->id }})">
            <i class="fas fa-heart"></i>
        </div>

        <button class="position-absolute bottom-0 end-0 m-2 btn btn-sm rounded-circle"
                style="background:var(--primary);color:white;width:32px;height:32px;padding:0"
                onclick="addToCart({{ $product->id }})">
            <i class="fas fa-plus" style="font-size:12px"></i>
        </button>
    </div>

    <div class="product-body">
        @if($product->category)
        <div class="product-category">{{ $product->category->name }}</div>
        @endif

        <div class="product-name">
            <a href="{{ route('products.show', $product->slug) }}">{{ Str::limit($product->name, 50) }}</a>
        </div>

        <div class="product-rating mb-1">
            @for($i = 1; $i <= 5; $i++)
            <i class="fa{{ $i <= $product->rating ? 's' : 'r' }} fa-star"></i>
            @endfor
            <span class="text-muted ms-1" style="font-size:11px">({{ $product->review_count }})</span>
        </div>

        @if($product->sale_ends_at && $product->sale_ends_at->isFuture())
        <div class="countdown-small mb-1">
            <span class="cd-days">00</span>d
            <span class="cd-hours">00</span>h
            <span class="cd-mins">00</span>m
            <span>Remains until the end of the offer</span>
        </div>
        @endif

        <div class="product-price d-flex align-items-center justify-content-between">
            <div>
                <span class="new-p">${{ number_format($product->current_price, 2) }}</span>
                @if($product->sale_price)
                <span class="old-p">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
        </div>

        @if($product->stock <= 10 && $product->stock > 0)
        <div class="mt-1">
            <div class="progress" style="height:3px">
                <div class="progress-bar bg-danger" style="width:{{ ($product->stock/50)*100 }}%"></div>
            </div>
            <small class="text-danger" style="font-size:11px">available only: {{ $product->stock }}</small>
        </div>
        @elseif($product->stock == 0)
        <span class="badge bg-danger" style="font-size:11px">Out of Stock</span>
        @endif

        <button class="btn-add-cart btn" onclick="addToCart({{ $product->id }})" {{ $product->stock == 0 ? 'disabled' : '' }}>
            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
        </button>
    </div>
</div>

@push('scripts-inline')
// Wishlist toggle
function toggleWishlist(el, productId) {
    el.classList.toggle('active');
}
@endpush
