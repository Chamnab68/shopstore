@extends('layouts.app')
@section('title', 'ShopStore - Shopping with us for better quality and the best price')

@section('content')
<div class="container py-4">

    {{-- ① Hero Banner + Side Promos --}}
    <div class="row g-3 mb-5">
        <div class="col-lg-8">
            <div class="swiper hero-swiper" style="border-radius:14px;overflow:hidden">
                <div class="swiper-wrapper">
                    @forelse($banners as $banner)
                    <div class="swiper-slide">
                        <div style="background:linear-gradient(135deg,#ede7ff 0%,#d4e8ff 100%);border-radius:14px;padding:50px 50px;min-height:360px;display:flex;align-items:center;justify-content:space-between;overflow:hidden">
                            <div style="flex:1">
                                @if($banner->badge)
                                <div style="display:inline-block;background:white;color:#6c3fd6;font-size:12px;font-weight:800;padding:5px 16px;border-radius:20px;margin-bottom:16px">{{ $banner->badge }}</div>
                                @endif
                                <h1 style="font-size:2.4rem;font-weight:900;color:#1a1a2e;line-height:1.2;margin-bottom:16px">{{ $banner->title }}</h1>
                                <p style="color:#888;margin-bottom:20px;font-size:14px">{{ $banner->subtitle }}</p>
                                @if($banner->price)
                                <div style="margin-bottom:20px">
                                    <span style="font-size:2rem;font-weight:900;color:#ff6b35">${{ $banner->price }}</span>
                                    @if($banner->original_price)
                                    <span style="font-size:1.1rem;color:#aaa;text-decoration:line-through;margin-left:8px">${{ $banner->original_price }}</span>
                                    <span style="font-size:12px;color:#888;margin-left:6px">Don't miss this limited time offer.</span>
                                    @endif
                                </div>
                                @endif
                                <a href="{{ $banner->link ?? route('products.index') }}"
                                   style="background:#6c3fd6;color:white;padding:14px 28px;border-radius:10px;text-decoration:none;font-weight:800;font-size:15px;display:inline-block">
                                    Shop Now →
                                </a>
                            </div>
                            @if($banner->image)
                            <div style="flex-shrink:0;margin-left:20px">
                                <img src="{{ asset('storage/'.$banner->image) }}" style="max-height:280px;object-fit:contain">
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="swiper-slide">
                        <div style="background:linear-gradient(135deg,#ede7ff 0%,#d4e8ff 100%);border-radius:14px;padding:50px;min-height:360px;display:flex;align-items:center">
                            <div>
                                <div style="display:inline-block;background:white;color:#6c3fd6;font-size:12px;font-weight:800;padding:5px 16px;border-radius:20px;margin-bottom:16px">Weekend Discount</div>
                                <h1 style="font-size:2.4rem;font-weight:900;color:#1a1a2e;line-height:1.2;margin-bottom:16px">Shopping with us for<br>better quality and the<br>best price</h1>
                                <p style="color:#888;margin-bottom:20px;font-size:14px">We have prepared special discounts for you on grocery products.<br>Don't miss these opportunities...</p>
                                <div style="margin-bottom:20px">
                                    <span style="font-size:2rem;font-weight:900;color:#ff6b35">$21.67</span>
                                    <span style="font-size:1.1rem;color:#aaa;text-decoration:line-through;margin-left:8px">$26.67</span>
                                    <span style="font-size:12px;color:#888;margin-left:6px">Don't miss this limited time offer.</span>
                                </div>
                                <a href="{{ route('products.index') }}" style="background:#6c3fd6;color:white;padding:14px 28px;border-radius:10px;text-decoration:none;font-weight:800;font-size:15px;display:inline-block">Shop Now →</a>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-flex flex-column gap-3">
            <div style="background:linear-gradient(135deg,#ff6b35,#f7971e);border-radius:14px;padding:24px;color:white;flex:1;display:flex;flex-direction:column;justify-content:center">
                <div style="font-size:11px;font-weight:700;background:rgba(255,255,255,.2);display:inline-block;padding:3px 10px;border-radius:20px;margin-bottom:10px">Only This Week</div>
                <h4 style="font-weight:900;margin-bottom:6px;line-height:1.3">Make your grocery<br>shopping easy with us</h4>
                <p style="font-size:13px;opacity:.85;margin-bottom:12px">Feed your family the best</p>
                <a href="{{ route('products.index') }}" style="background:white;color:#ff6b35;padding:8px 18px;border-radius:6px;text-decoration:none;font-weight:800;font-size:13px;display:inline-block">Shop Now →</a>
            </div>
            <div style="background:linear-gradient(135deg,#6c3fd6,#a855f7);border-radius:14px;padding:24px;color:white;flex:1;display:flex;flex-direction:column;justify-content:center">
                <div style="font-size:11px;font-weight:700;background:rgba(255,255,255,.2);display:inline-block;padding:3px 10px;border-radius:20px;margin-bottom:10px">Only This Week</div>
                <h4 style="font-weight:900;margin-bottom:6px;line-height:1.3">Get your everyday<br>needs here with us</h4>
                <p style="font-size:13px;opacity:.85;margin-bottom:12px">A different kind of grocery store</p>
                <a href="{{ route('products.index') }}" style="background:white;color:#6c3fd6;padding:8px 18px;border-radius:6px;text-decoration:none;font-weight:800;font-size:13px;display:inline-block">Shop Now →</a>
            </div>
        </div>
    </div>

    {{-- New: Gamification Badges Section --}}
   
    {{-- ② Categories --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 style="font-size:1.2rem;font-weight:900;color:#1a1a2e;margin:0">Shop by Category</h2>
            <a href="{{ route('products.index') }}" style="font-size:13px;font-weight:700;color:#6c3fd6;text-decoration:none">View All →</a>
        </div>
        <div class="row g-3">
            @foreach($categories as $cat)
            <div class="col-4 col-md-2 col-lg-auto" style="min-width:100px">
                <a href="{{ route('products.category', $cat->slug) }}" class="text-decoration-none">
                    <div style="text-align:center;cursor:pointer;transition:transform .2s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                        <div style="width:90px;height:90px;border-radius:50%;overflow:hidden;margin:0 auto 8px;background:white;box-shadow:0 4px 15px rgba(0,0,0,.1);display:flex;align-items:center;justify-content:center">
                            @if($cat->image)
                            <img src="{{ asset('storage/'.$cat->image) }}" style="width:70px;height:70px;object-fit:contain">
                            @else
                            <i class="fas fa-shopping-basket fa-2x text-muted"></i>
                            @endif
                        </div>
                        <div style="font-size:13px;font-weight:700;color:#1a1a2e">{{ $cat->name }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ③ Editor's Pick --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 style="font-size:1.2rem;font-weight:900;color:#1a1a2e;margin:0">Editor's Pick</h2>
                <span style="font-size:13px;color:#888">New products with updated stocks.</span>
            </div>
            <a href="{{ route('products.index') }}" style="font-size:13px;font-weight:700;color:#6c3fd6;text-decoration:none">View All →</a>
        </div>
        <div class="row g-3">
            @foreach($editorPicks as $product)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>

    {{-- ④ Best Sellers --}}
    @include('partials.best-sellers', ['bestSellers' => $bestSellers])

    {{-- ⑤ Promo Banners Row --}}
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div style="background:linear-gradient(135deg,#f7971e,#ffd200);border-radius:14px;padding:28px;position:relative;overflow:hidden;min-height:160px">
                <div style="font-size:11px;font-weight:700;background:rgba(255,255,255,.3);display:inline-block;padding:3px 10px;border-radius:20px;margin-bottom:8px;color:white">Only This Week</div>
                <h4 style="font-weight:900;color:white;line-height:1.3;margin-bottom:6px">We provide you the<br>best quality products</h4>
                <p style="font-size:12px;color:rgba(255,255,255,.85);margin-bottom:10px">A family place for grocery</p>
                <a href="{{ route('products.index') }}" style="background:white;color:#f7971e;padding:7px 16px;border-radius:6px;text-decoration:none;font-weight:800;font-size:13px;display:inline-block">Shop Now →</a>
            </div>
        </div>
        <div class="col-md-4">
            <div style="background:linear-gradient(135deg,#6c3fd6,#a855f7);border-radius:14px;padding:28px;min-height:160px">
                <div style="font-size:11px;font-weight:700;background:rgba(255,255,255,.2);display:inline-block;padding:3px 10px;border-radius:20px;margin-bottom:8px;color:white">Only This Week</div>
                <h4 style="font-weight:900;color:white;line-height:1.3;margin-bottom:6px">We make your grocery<br>shopping more exciting</h4>
                <p style="font-size:12px;color:rgba(255,255,255,.85);margin-bottom:10px">Shine the morning...</p>
                <a href="{{ route('products.index') }}" style="background:white;color:#6c3fd6;padding:7px 16px;border-radius:6px;text-decoration:none;font-weight:800;font-size:13px;display:inline-block">Shop Now →</a>
            </div>
        </div>
        <div class="col-md-4">
            <div style="background:linear-gradient(135deg,#ff6b35,#ff4757);border-radius:14px;padding:28px;min-height:160px">
                <div style="font-size:11px;font-weight:700;background:rgba(255,255,255,.2);display:inline-block;padding:3px 10px;border-radius:20px;margin-bottom:8px;color:white">Only This Week</div>
                <h4 style="font-weight:900;color:white;line-height:1.3;margin-bottom:6px">The one supermarket<br>that saves your money</h4>
                <p style="font-size:12px;color:rgba(255,255,255,.85);margin-bottom:10px">Breakfast made better</p>
                <a href="{{ route('products.index') }}" style="background:white;color:#ff4757;padding:7px 16px;border-radius:6px;text-decoration:none;font-weight:800;font-size:13px;display:inline-block">Shop Now →</a>
            </div>
        </div>
    </div>

    {{-- ⑥ Category Products --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 style="font-size:1.2rem;font-weight:900;color:#1a1a2e;margin:0">Category Products</h2>
                <span style="font-size:13px;color:#888">Do not miss the current offers until the end of March.</span>
            </div>
            <a href="{{ route('products.index') }}" style="font-size:13px;font-weight:700;color:#6c3fd6;text-decoration:none">View All →</a>
        </div>
        <div class="row g-3">
            @foreach($saleProducts as $product)
            <div class="col-6 col-md-4 col-lg-2">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>

    {{-- ⑦ Blog Posts --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 style="font-size:1.2rem;font-weight:900;color:#1a1a2e;margin:0">Our News</h2>
                <span style="font-size:13px;color:#888">Some of the new posts this week</span>
            </div>
            <a href="#" style="font-size:13px;font-weight:700;color:#6c3fd6;text-decoration:none">View All →</a>
        </div>
        <div class="row g-3">
            @php
            $posts = [
                ['title'=>'How grocers are approaching delivery as the market evolves','img'=>'https://images.unsplash.com/photo-1534723452862-4c874018d66d?w=400&auto=format'],
                ['title'=>'The Friday Checkout: Food insecurity keeps retailers off balance','img'=>'https://imgproxy.divecdn.com/RXwgGEYOb9WBA4fikUcYYWjbcQFq2_Dz0GeQRQUSIq0/crop:4032:2277/background:0:0:0/g:nowe:0:746/rs:fill:0:860:0/Z3M6Ly9kaXZlc2l0ZS1zdG9yYWdlL2RpdmVpbWFnZS9JTUdfMzEzNC5qcGc=.webp'],
                ['title'=>'Consumer want grocer to use AI to help them save money','img'=>'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&auto=format'],
                ['title'=>'Order up! How grocers are replicating the restaurant experience','img'=>'https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=400&auto=format'],
            ];
            @endphp
            @foreach($posts as $post)
            <div class="col-6 col-md-3">
                <div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.06);transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
                    <img src="{{ $post['img'] }}" style="width:100%;height:160px;object-fit:cover">
                    <div style="padding:14px">
                        <span style="background:#f0f0f0;padding:2px 10px;border-radius:4px;font-size:10px;font-weight:700;color:#888">UNCATEGORIZED</span>
                        <h6 style="margin:8px 0 4px;font-weight:700;font-size:14px;line-height:1.4;color:#1a1a2e">{{ $post['title'] }}</h6>
                        <small style="color:#aaa">by nab — 30 Mar 2026</small>
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
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    loop: true,
    effect: 'slide',
});
</script>
@endpush

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        transition: all 0.2s ease-in-out;
    }
</style>