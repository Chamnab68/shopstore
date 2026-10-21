<div class="product-card">
    <div class="product-img-wrap">
        {{-- បង្ហាញ Badge តាមប្រភេទផលិតផល (ផ្តល់អាទិភាពជូន Reward-Only មុនគេ) --}}
        @if($product->is_reward_only)
            <span class="product-badge bg-danger text-white">🎁 Reward Only</span>
        @elseif($product->discount_percent > 0)
            <span class="product-badge badge-discount">{{ $product->discount_percent }}%</span>
        @elseif($product->is_organic)
            <span class="product-badge badge-organic">🌿 Organic</span>
        @elseif($product->is_cold_sale)
            <span class="product-badge badge-cold">❄️ Cold Sale</span>
        @endif

        {{-- ប៊ូតុង Link ទៅកាន់រូបភាព (ហៅតាម Model Instance ដោយផ្ទាល់) --}}
        @if($product->slug)
            <a href="{{ route('products.show', $product) }}">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" class="img-fluid">
            </a>
        @else
            <a href="javascript:void(0)" style="cursor: default;">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" class="img-fluid">
            </a>
        @endif

        <div class="product-wishlist" onclick="toggleWishlist(this, {{ $product->id }})">
            <i class="fas fa-heart"></i>
        </div>

        {{-- លាក់ប៊ូតុងដូសហ្វា (+) ຖ້າជា Reward-Only --}}
        @if(!$product->is_reward_only)
        <button class="position-absolute bottom-0 end-0 m-2 btn btn-sm rounded-circle"
                style="background:var(--primary);color:white;width:32px;height:32px;padding:0"
                onclick="addToCart({{ $product->id }})">
            <i class="fas fa-plus" style="font-size:12px"></i>
        </button>
        @endif
    </div>

    <div class="product-body">
        @if($product->category)
            <div class="product-category">{{ $product->category->name }}</div>
        @endif

        <div class="product-name">
            {{-- Link ឈ្មោះផលិតផល --}}
            @if($product->slug)
                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark fw-bold">{{ Str::limit($product->name, 50) }}</a>
            @else
                <span class="text-dark fw-bold">{{ Str::limit($product->name, 50) }}</span>
            @endif
        </div>

        <div class="product-rating mb-1">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa{{ $i <= ($product->rating ?? 0) ? 's' : 'r' }} fa-star text-warning"></i>
            @endfor
            <span class="text-muted ms-1" style="font-size:11px">({{ $product->review_count ?? 0 }})</span>
        </div>

        @if($product->sale_ends_at && \Carbon\Carbon::parse($product->sale_ends_at)->isFuture())
            <div class="countdown-small mb-1 text-danger" style="font-size:11px">
                <span>Remains until the end of the offer</span>
            </div>
        @endif

        {{-- Pricing / Points Display --}}
        <div class="product-price d-flex align-items-center justify-content-between mb-2">
            <div>
                @if($product->is_reward_only)
                    <span class="text-danger fw-bold" style="font-size: 15px;">
                        <i class="fas fa-coins me-1"></i>{{ number_format($product->points_cost ?? ($product->price * 100)) }} Points
                    </span>
                @else
                    <span class="new-p fw-bold text-success">${{ number_format($product->sale_price ?? $product->price, 2) }}</span>
                    @if($product->sale_price)
                        <span class="old-p text-muted text-decoration-line-through ms-1" style="font-size:12px">${{ number_format($product->price, 2) }}</span>
                    @endif
                @endif
            </div>
        </div>

        @if(!$product->is_reward_only)
            @if($product->stock <= 10 && $product->stock > 0)
                <div class="mt-1">
                    <div class="progress" style="height:3px">
                        <div class="progress-bar bg-danger" style="width:{{ ($product->stock/50)*100 }}%"></div>
                    </div>
                    <small class="text-danger" style="font-size:11px">available only: {{ $product->stock }}</small>
                </div>
            @elseif($product->stock == 0)
                <span class="badge bg-danger mt-1" style="font-size:11px">Out of Stock</span>
            @endif
        @endif

        {{-- Dynamic Action Button (Add to Cart vs Redeem) --}}
        @if($product->is_reward_only)
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-danger btn-sm w-100 mt-2 fw-bold">
                <i class="fas fa-gift me-1"></i> View Reward
            </a>
        @else
            <button class="btn-add-cart btn btn-primary btn-sm w-100 mt-2" onclick="addToCart({{ $product->id }})" {{ $product->stock == 0 ? 'disabled' : '' }}>
                <i class="fas fa-shopping-cart me-1"></i> Add to Cart
            </button>
        @endif
    </div>
</div>

@push('scripts-inline')
<script>
// Wishlist toggle
function toggleWishlist(el, productId) {
    el.classList.toggle('active');
}
</script>
@endpush