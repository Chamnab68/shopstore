{{-- Best Sellers Section - 3 Column Layout --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="font-size:1.3rem">BEST SELLERS</h2>
            <span class="text-muted" style="font-size:13px">Don't miss this opportunity at a special discount just for
                this week.</span>
        </div>
        <a href="{{ route('products.index', ['sort' => 'sold']) }}" class="text-decoration-none fw-bold"
            style="font-size:13px;color:#1a1a2e">
            View All →
        </a>
    </div>

    {{-- Promo Banner --}}
    <div class="rounded-3 mb-4 p-4 d-flex align-items-center justify-content-between"
        style="background:linear-gradient(135deg,#fff8e1,#fffde7);border:1px solid #ffc107;overflow:hidden;position:relative">
        <div>
            <div style="color:#ff6b35; font-size:13px; font-weight:700">
                <i class="bi bi-fire" style="margin-right: 4px;"></i> Only This Week
            </div>
            <h4 class="fw-bold mb-1">In store or online your health & safety is our top priority</h4>
            <p class="text-muted mb-0" style="font-size:13px">The only supermarket that makes your life easier</p>
        </div>
        <div
            style="font-size:100px;font-weight:900;color:rgba(255,107,53,.1);line-height:1;position:absolute;right:20px">
            %50</div>
    </div>

    <div class="row g-3">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-3">
            @foreach ($bestSellers->take(3) as $product)
                <div class="bg-white rounded-3 p-3 mb-3 d-flex gap-3 align-items-start"
                    style="border:1px solid #f0f0f0;cursor:pointer;transition:all .2s"
                    onmouseover="this.style.boxShadow='0 4px 20px rgba(108,63,214,.12)';this.style.borderColor='#6c3fd6'"
                    onmouseout="this.style.boxShadow='none';this.style.borderColor='#f0f0f0'"
                    onclick="setFeatured('{{ $product->id }}')">

                    <div class="position-relative flex-shrink-0">
                        @if ($product->discount_percent > 0)
                            <span
                                style="position:absolute;top:-6px;left:-6px;background:#ff6b35;color:white;font-size:10px;font-weight:800;padding:2px 6px;border-radius:4px;z-index:1">{{ $product->discount_percent }}%</span>
                        @endif
                        <img src="{{ $product->image_url }}"
                            style="width:80px;height:80px;object-fit:contain;background:#f8f9fa;border-radius:8px"
                            onerror="this.src='https://via.placeholder.com/80'">
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div style="font-size:13px;font-weight:600;color:#1a1a2e;line-height:1.3;margin-bottom:4px">
                            {{ Str::limit($product->name, 40) }}</div>
                        <div style="color:#f59e0b;font-size:11px;margin-bottom:4px">
                            @for ($s = 1; $s <= 5; $s++)
                                <i class="fa{{ $s <= $product->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                            <span class="text-muted ms-1" style="font-size:10px">{{ $product->review_count }}</span>
                        </div>
                        <div>
                            <span
                                style="font-size:16px;font-weight:800;color:#6c3fd6">${{ number_format($product->current_price, 2) }}</span>
                            @if ($product->sale_price)
                                <span
                                    style="font-size:12px;color:#aaa;text-decoration:line-through;margin-left:4px">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        <button onclick="event.stopPropagation();addToCart({{ $product->id }})"
                            class="btn btn-sm w-100 mt-2 fw-bold"
                            style="border:1.5px solid #6c3fd6;color:#6c3fd6;background:white;border-radius:8px;font-size:12px;padding:4px"
                            onmouseover="this.style.background='#6c3fd6';this.style.color='white'"
                            onmouseout="this.style.background='white';this.style.color='#6c3fd6'">
                            Add to cart +
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CENTER FEATURED --}}
        <div class="col-lg-6">
            @php $featured = $bestSellers->first(); @endphp
            <div class="bg-white rounded-3 h-100" style="border:2px solid #dc3545;overflow:hidden">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span id="featured-badge"
                            style="background:#dc3545;color:white;font-size:13px;font-weight:800;padding:4px 12px;border-radius:6px">
                            {{ $featured && $featured->discount_percent > 0 ? $featured->discount_percent . '%' : 'HOT' }}
                        </span>
                        <button onclick="toggleWishlistFeatured()"
                            style="background:white;border:1px solid #eee;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#ccc"
                            id="featured-wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <div class="text-center mb-3">
                        <img src="{{ $featured?->image_url }}" id="featured-img"
                            style="max-height:280px;max-width:100%;object-fit:contain"
                            onerror="this.src='https://via.placeholder.com/280'">
                    </div>

                    <div style="color:#f59e0b;font-size:14px;margin-bottom:8px" id="featured-rating">
                        @if ($featured)
                            @for ($s = 1; $s <= 5; $s++)
                                <i class="fa{{ $s <= $featured->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                            <span class="text-muted ms-1" style="font-size:12px">{{ $featured->review_count }}</span>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-2" style="font-size:16px;line-height:1.4" id="featured-name">
                        {{ $featured?->name }}</h5>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="font-size:22px;font-weight:900;color:#6c3fd6"
                            id="featured-price">${{ number_format($featured?->current_price ?? 0, 2) }}</span>
                        <span style="font-size:14px;color:#aaa;text-decoration:line-through" id="featured-old-price">
                            @if ($featured?->sale_price)
                                ${{ number_format($featured->price, 2) }}
                            @endif
                        </span>
                    </div>

                    <p style="font-size:13px;color:#888;line-height:1.6;margin-bottom:16px" id="featured-desc">
                        {{ Str::limit($featured?->description ?? 'Premium quality product at the best price.', 120) }}
                    </p>

                    @if ($featured && $featured->stock <= 20)
                        <div class="mb-3" id="featured-stock-wrap">
                            <div style="font-size:12px;color:#ff6b35;font-weight:600;margin-bottom:4px">This product is
                                about to run out</div>
                            <div style="height:4px;background:#eee;border-radius:2px;overflow:hidden">
                                <div
                                    style="height:100%;background:linear-gradient(90deg,#ff6b35,#ffc107);width:{{ min(($featured->stock / 50) * 100, 100) }}%">
                                </div>
                            </div>
                            <div style="font-size:12px;color:#888;margin-top:4px">available only: <strong
                                    id="featured-stock">{{ $featured->stock }}</strong></div>
                        </div>
                    @endif

                    <button id="featured-btn" onclick="addToCart({{ $featured?->id }})" class="btn w-100 fw-bold py-3"
                        style="background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:10px;font-size:15px">
                        <i class="fas fa-shopping-cart me-2"></i>Add to cart
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-3">
            @foreach ($bestSellers->skip(3)->take(3) as $product)
                <div class="bg-white rounded-3 p-3 mb-3 d-flex gap-3 align-items-start"
                    style="border:1px solid #f0f0f0;cursor:pointer;transition:all .2s"
                    onmouseover="this.style.boxShadow='0 4px 20px rgba(108,63,214,.12)';this.style.borderColor='#6c3fd6'"
                    onmouseout="this.style.boxShadow='none';this.style.borderColor='#f0f0f0'"
                    onclick="setFeatured('{{ $product->id }}')">

                    <div class="position-relative flex-shrink-0">
                        @if ($product->discount_percent > 0)
                            <span
                                style="position:absolute;top:-6px;left:-6px;background:#ff6b35;color:white;font-size:10px;font-weight:800;padding:2px 6px;border-radius:4px;z-index:1">{{ $product->discount_percent }}%</span>
                        @endif
                        <img src="{{ $product->image_url }}"
                            style="width:80px;height:80px;object-fit:contain;background:#f8f9fa;border-radius:8px"
                            onerror="this.src='https://via.placeholder.com/80'">
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div style="font-size:13px;font-weight:600;color:#1a1a2e;line-height:1.3;margin-bottom:4px">
                            {{ Str::limit($product->name, 40) }}</div>
                        <div style="color:#f59e0b;font-size:11px;margin-bottom:4px">
                            @for ($s = 1; $s <= 5; $s++)
                                <i class="fa{{ $s <= $product->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                            <span class="text-muted ms-1" style="font-size:10px">{{ $product->review_count }}</span>
                        </div>
                        <div>
                            <span
                                style="font-size:16px;font-weight:800;color:#6c3fd6">${{ number_format($product->current_price, 2) }}</span>
                            @if ($product->sale_price)
                                <span
                                    style="font-size:12px;color:#aaa;text-decoration:line-through;margin-left:4px">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        <button onclick="event.stopPropagation();addToCart({{ $product->id }})"
                            class="btn btn-sm w-100 mt-2 fw-bold"
                            style="border:1.5px solid #6c3fd6;color:#6c3fd6;background:white;border-radius:8px;font-size:12px;padding:4px"
                            onmouseover="this.style.background='#6c3fd6';this.style.color='white'"
                            onmouseout="this.style.background='white';this.style.color='#6c3fd6'">
                            Add to cart +
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
    <script>
        var bsProducts = {
            @foreach ($bestSellers as $p)
                '{{ $p->id }}': {
                    id: {{ $p->id }},
                    name: '{{ addslashes($p->name) }}',
                    img: '{{ $p->image_url }}',
                    price: '{{ number_format($p->current_price, 2) }}',
                    oldPrice: '{{ $p->sale_price ? number_format($p->price, 2) : '' }}',
                    rating: {{ $p->rating }},
                    reviews: {{ $p->review_count }},
                    desc: '{{ addslashes(Str::limit($p->description ?? 'Premium quality product.', 120)) }}',
                    stock: {{ $p->stock }},
                    discount: {{ $p->discount_percent }},
                },
            @endforeach
        };

        function setFeatured(id) {
            var p = bsProducts[id];
            if (!p) return;
            document.getElementById('featured-img').src = p.img;
            document.getElementById('featured-name').textContent = p.name;
            document.getElementById('featured-price').textContent = '$' + p.price;
            document.getElementById('featured-old-price').textContent = p.oldPrice ? '$' + p.oldPrice : '';
            document.getElementById('featured-desc').textContent = p.desc;
            document.getElementById('featured-btn').onclick = function() {
                addToCart(p.id);
            };
            document.getElementById('featured-badge').textContent = p.discount > 0 ? p.discount + '%' : 'HOT';

            var stars = '';
            for (var i = 1; i <= 5; i++) {
                stars += '<i class="fa' + (i <= p.rating ? 's' : 'r') + ' fa-star"></i>';
            }
            stars += '<span class="text-muted ms-1" style="font-size:12px">' + p.reviews + '</span>';
            document.getElementById('featured-rating').innerHTML = stars;

            var sw = document.getElementById('featured-stock-wrap');
            if (sw) {
                document.getElementById('featured-stock').textContent = p.stock;
            }
        }

        function toggleWishlistFeatured() {
            var icon = document.querySelector('#featured-wishlist i');
            icon.style.color = icon.style.color === 'rgb(231, 76, 60)' ? '#ccc' : '#e74c3c';
        }
    </script>
@endpush
