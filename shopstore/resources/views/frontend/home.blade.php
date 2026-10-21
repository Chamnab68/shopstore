@extends('layouts.app')
@section('title', 'ShopStore - Shopping with us for better quality and the best price')

@section('content')
<div class="container py-4">

    {{-- Hero + Banners --}}
    <div class="row g-3 mb-5">
        <div class="col-lg-8">
            <div class="swiper hero-swiper">
                <div class="swiper-wrapper">
                    @forelse($banners as $banner)
                    <div class="swiper-slide">
                        <div class="hero-slide" style="background: linear-gradient(135deg, #ede7ff 0%, #d4e8ff 100%);">
                            <div class="flex-grow-1">
                                @if($banner->badge)
                                <div class="hero-badge">{{ $banner->badge }}</div>
                                @endif
                                <h1 class="hero-title">{{ $banner->title }}</h1>
                                <p class="text-muted mb-3">{{ $banner->subtitle }}</p>
                                @if($banner->price)
                                <div class="hero-price mb-4">
                                    <span class="new-price">${{ $banner->price }}</span>
                                    @if($banner->original_price)
                                    <span class="old-price">${{ $banner->original_price }}</span>
                                    @endif
                                </div>
                                @endif
                                <a href="{{ $banner->link ?? route('products.index') }}" class="btn btn-shop">Shop Now →</a>
                            </div>
                            @if($banner->image)
                            <div class="hero-img">
                                <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" style="max-height:300px;object-fit:contain">
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="swiper-slide">
                        <div class="hero-slide">
                            <div class="flex-grow-1">
                                <div class="hero-badge">Weekend Discount</div>
                                <h1 class="hero-title">Shopping with us for<br>better quality and the<br>best price</h1>
                                <p class="text-muted mb-3">We have prepared special discounts for you on grocery products.</p>
                                <div class="hero-price mb-4">
                                    <span class="new-price">$21.67</span>
                                    <span class="old-price">$26.67</span>
                                </div>
                                <a href="{{ route('products.index') }}" class="btn btn-shop">Shop Now →</a>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block">
            <div class="promo-card h-100 mb-3" style="background:linear-gradient(135deg,#ff6b35,#f7971e);min-height:180px">
                <div>
                    <span class="badge-week">Only This Week</span>
                    <h4>Make your grocery<br>shopping easy with us</h4>
                    <p>Feed your family the best</p>
                    <a href="{{ route('products.index') }}" class="btn-promo btn">Shop Now →</a>
                </div>
                <img src="https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=200&auto=format" alt="" style="height:140px;object-fit:contain;border-radius:10px">
            </div>
            <div class="promo-card" style="background:linear-gradient(135deg,#6c3fd6,#a855f7);min-height:100px;padding:20px">
                <div>
                    <span class="badge-week">Only This Week</span>
                    <h4 style="font-size:1.1rem">Get your everyday<br>needs here with us</h4>
                    <a href="{{ route('products.index') }}" class="btn-promo btn btn-sm">Shop Now →</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Categories --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title mb-0">Shop by Category</h2>
            </div>
            <a href="{{ route('products.index') }}" class="view-all">View All →</a>
        </div>
        <div class="row g-3">
            @foreach($categories as $cat)
            <div class="col-4 col-md-2 col-lg-auto">
                <a href="{{ route('products.category', $cat->slug) }}" class="text-decoration-none">
                    <div class="category-card">
                        <div class="category-img-wrap">
                            @if($cat->image)
                            <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}">
                            @else
                            <i class="fas fa-shopping-basket fa-2x text-muted"></i>
                            @endif
                        </div>
                        <div class="category-name">{{ $cat->name }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Editor's Pick --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title mb-0">Editor's Pick</h2>
                <span class="section-sub">New products with updated stocks.</span>
            </div>
            <a href="{{ route('products.index') }}" class="view-all">View All →</a>
        </div>
        <div class="row g-3">
            @foreach($editorPicks as $product)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>

    {{-- Promo Banners Row --}}
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="promo-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
                <div>
                    <span class="badge-week">Only This Week</span>
                    <h4>We provide you the<br>best quality products</h4>
                    <p>A family place for grocery</p>
                    <a href="{{ route('products.index') }}" class="btn-promo btn btn-sm" style="color:var(--primary)">Shop Now →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="promo-card" style="background:linear-gradient(135deg,#6c3fd6,#a855f7)">
                <div>
                    <span class="badge-week">Only This Week</span>
                    <h4>We make your grocery<br>shopping more exciting</h4>
                    <p>Shine the morning...</p>
                    <a href="{{ route('products.index') }}" class="btn-promo btn btn-sm">Shop Now →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="promo-card" style="background:linear-gradient(135deg,#ff6b35,#ff4757)">
                <div>
                    <span class="badge-week">Only This Week</span>
                    <h4>The one supermarket<br>that saves your money</h4>
                    <p>Breakfast made better</p>
                    <a href="{{ route('products.index') }}" class="btn-promo btn btn-sm">Shop Now →</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Best Sellers --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title mb-0">Best Sellers</h2>
                <span class="section-sub">Don't miss this opportunity at a special discount just for this week.</span>
            </div>
            <a href="{{ route('products.index', ['sort'=>'sold']) }}" class="view-all">View All →</a>
        </div>
        <div class="row g-3">
            @foreach($bestSellers as $product)
            <div class="col-6 col-md-4 col-lg-2">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>

    {{-- Our News --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title mb-0">Our News</h2>
                <span class="section-sub">Some of the new posts this week</span>
            </div>
            <a href="#" class="view-all">View All →</a>
        </div>
        <div class="row g-3">
            @php
            $blogPosts = [
                ['title'=>'How grocers are approaching delivery as the market evolves','img'=>'https://images.unsplash.com/photo-1534723452862-4c874018d66d?w=400&auto=format'],
                ['title'=>'The Friday Checkout: Food insecurity keeps retailers off balance','img'=>'https://images.unsplash.com/photo-1604719312566-8912e9667d9f?w=400&auto=format'],
                ['title'=>'Consumer want grocer to use AI to help them save money','img'=>'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&auto=format'],
                ['title'=>'Order up! How grocers are replicating the restaurant experience','img'=>'https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=400&auto=format'],
            ];
            @endphp
            @foreach($blogPosts as $post)
            <div class="col-6 col-md-3">
                <div class="bg-white rounded-3 overflow-hidden shadow-sm">
                    <img src="{{ $post['img'] }}" alt="{{ $post['title'] }}" style="width:100%;height:160px;object-fit:cover">
                    <div class="p-3">
                        <span class="badge bg-secondary" style="font-size:10px">UNCATEGORIZED</span>
                        <h6 class="mt-2 fw-bold" style="font-size:14px;line-height:1.4">{{ $post['title'] }}</h6>
                        <small class="text-muted">by sinan — 3 Nov 2023</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Swiper('.hero-swiper', {
    autoplay: { delay: 5000 },
    pagination: { el: '.swiper-pagination', clickable: true },
    loop: true
});
</script>
@endpush
