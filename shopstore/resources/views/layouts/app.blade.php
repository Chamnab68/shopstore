<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopStore - Best Grocery Online')</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>

    <style>
        :root {
            --primary: #6c3fd6;
            --primary-light: #f0ebff;
            --secondary: #ff6b35;
            --success: #28a745;
            --dark: #1a1a2e;
            --gray: #6c757d;
            --light-bg: #f8f9fa;
        }
        * { font-family: 'Nunito', sans-serif; }
        body { background: #f8f9fa; color: #1a1a2e; }

        /* Announcement Bar */
        .announcement-bar {
            background: var(--primary);
            color: white;
            font-size: 13px;
            padding: 8px 0;
        }
        .countdown-timer span { font-weight: 800; }

        /* Navbar */
        .top-bar { background: white; border-bottom: 1px solid #eee; font-size: 13px; }
        .main-navbar { background: white; box-shadow: 0 2px 10px rgba(0,0,0,.05); position: sticky; top: 0; z-index: 1000; }
        .navbar-brand img { height: 40px; }
        .search-bar { flex: 1; max-width: 500px; }
        .search-bar .form-control { border-radius: 8px 0 0 8px; border: 2px solid var(--primary); border-right: none; }
        .search-bar .btn { border-radius: 0 8px 8px 0; background: var(--primary); color: white; border: 2px solid var(--primary); }
        .nav-icon { font-size: 20px; color: var(--dark); position: relative; }
        .badge-cart { position: absolute; top: -8px; right: -8px; background: var(--secondary); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .main-nav .nav-link { font-weight: 600; color: var(--dark) !important; padding: 8px 14px !important; }
        .main-nav .nav-link:hover, .main-nav .nav-link.active { color: var(--primary) !important; }
        .btn-sale { background: var(--secondary); color: white; border-radius: 20px; font-size: 12px; font-weight: 700; padding: 2px 10px; }

        /* Hero Banner */
        .hero-swiper { border-radius: 12px; overflow: hidden; }
        .hero-slide { background: #ede7ff; border-radius: 12px; padding: 60px 50px; min-height: 380px; display: flex; align-items: center; }
        .hero-badge { background: white; color: var(--primary); font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 20px; display: inline-block; margin-bottom: 14px; }
        .hero-title { font-size: 2.8rem; font-weight: 800; color: var(--dark); line-height: 1.2; }
        .hero-price .new-price { font-size: 2rem; font-weight: 800; color: var(--secondary); }
        .hero-price .old-price { font-size: 1.2rem; color: var(--gray); text-decoration: line-through; margin-left: 10px; }
        .btn-shop { background: var(--primary); color: white; border-radius: 8px; font-weight: 700; padding: 12px 28px; font-size: 16px; }
        .btn-shop:hover { background: #5930b8; color: white; }

        /* Categories */
        .category-card { text-align: center; cursor: pointer; transition: transform .2s; }
        .category-card:hover { transform: translateY(-4px); }
        .category-img-wrap { width: 90px; height: 90px; border-radius: 50%; overflow: hidden; margin: 0 auto 10px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,.1); display: flex; align-items: center; justify-content: center; }
        .category-img-wrap img { width: 70px; height: 70px; object-fit: contain; }
        .category-name { font-size: 13px; font-weight: 700; color: var(--dark); }

        /* Product Cards */
        .product-card { background: white; border-radius: 14px; overflow: hidden; transition: all .25s; border: 1px solid #f0f0f0; height: 100%; }
        .product-card:hover { box-shadow: 0 10px 30px rgba(108,63,214,.15); transform: translateY(-4px); }
        .product-img-wrap { position: relative; overflow: hidden; background: #fafafa; height: 200px; display: flex; align-items: center; justify-content: center; }
        .product-img-wrap img { max-height: 160px; max-width: 100%; object-fit: contain; transition: transform .3s; }
        .product-card:hover .product-img-wrap img { transform: scale(1.08); }
        .product-badge { position: absolute; top: 10px; left: 10px; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; }
        .badge-discount { background: var(--secondary); color: white; }
        .badge-organic { background: #28a745; color: white; }
        .badge-cold { background: #17a2b8; color: white; }
        .product-wishlist { position: absolute; top: 10px; right: 10px; background: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #ccc; transition: color .2s; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .product-wishlist:hover, .product-wishlist.active { color: #e74c3c; }
        .product-body { padding: 14px; }
        .product-category { font-size: 11px; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .product-name { font-size: 14px; font-weight: 700; color: var(--dark); margin: 4px 0; line-height: 1.4; }
        .product-name a { color: inherit; text-decoration: none; }
        .product-rating { color: #f59e0b; font-size: 12px; }
        .product-price .new-p { font-size: 18px; font-weight: 800; color: var(--primary); }
        .product-price .old-p { font-size: 13px; color: var(--gray); text-decoration: line-through; margin-left: 4px; }
        .countdown-small { font-size: 11px; color: var(--gray); }
        .countdown-small span { color: var(--dark); font-weight: 700; background: #f0f0f0; padding: 1px 5px; border-radius: 4px; }
        .btn-add-cart { background: white; border: 2px solid var(--primary); color: var(--primary); border-radius: 8px; font-weight: 700; font-size: 13px; width: 100%; margin-top: 10px; transition: all .2s; }
        .btn-add-cart:hover { background: var(--primary); color: white; }

        /* Promo Banner */
        .promo-card { border-radius: 14px; overflow: hidden; padding: 28px 30px; display: flex; align-items: center; justify-content: space-between; position: relative; min-height: 160px; }
        .promo-card .badge-week { background: rgba(255,255,255,.25); color: white; font-size: 11px; border-radius: 4px; padding: 2px 8px; margin-bottom: 8px; display: inline-block; }
        .promo-card h4 { font-size: 1.4rem; font-weight: 800; color: white; line-height: 1.3; }
        .promo-card p { font-size: 13px; color: rgba(255,255,255,.85); }
        .btn-promo { background: white; border-radius: 6px; font-weight: 700; font-size: 13px; padding: 8px 18px; text-decoration: none; }

        /* Section */
        .section-title { font-size: 1.3rem; font-weight: 800; color: var(--dark); }
        .section-sub { font-size: 13px; color: var(--gray); }
        .view-all { font-size: 13px; font-weight: 700; color: var(--primary); text-decoration: none; }
        .view-all:hover { text-decoration: underline; }

        /* Footer */
        footer { background: #1a1a2e; color: #aaa; }
        footer h6 { color: white; font-weight: 700; margin-bottom: 16px; }
        footer a { color: #aaa; text-decoration: none; font-size: 14px; }
        footer a:hover { color: white; }
        .footer-bottom { background: #111; border-top: 1px solid #333; }
        .social-icons a { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.1); display: inline-flex; align-items: center; justify-content: center; color: white; margin-right: 8px; transition: background .2s; }
        .social-icons a:hover { background: var(--primary); }
        .app-badge img { height: 36px; }
        .payment-icons img { height: 24px; margin-right: 8px; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 1.8rem; }
            .hero-slide { padding: 30px 20px; min-height: 250px; }
            .hero-img { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Announcement Bar -->
<div class="announcement-bar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>🚀 FREE delivery & 40% Discount for next 3 orders! Place your 1st order in.</span>
        <div class="d-flex align-items-center gap-2">
            <span>Until the end of the sale:</span>
            <span class="countdown-timer" id="countdown">
                <span id="c-days">47</span> days
                <span id="c-hours">06</span> hours
                <span id="c-mins">57</span> min
                <span id="c-secs">08</span> sec
            </span>
        </div>
    </div>
</div>

<!-- Top Bar -->
<div class="top-bar py-1">
    <div class="container d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex gap-3">
            <a href="/about" class="text-muted">About Us</a>
            <a href="#" class="text-muted">My Account</a>
            <a href="#" class="text-muted">Wishlist</a>
            <span class="text-muted">We deliver every day <strong class="text-success">7:00 to 23:00</strong></span>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <a href="#" class="text-muted">English</a>
            <a href="#" class="text-muted">USD</a>
            <a href="#" class="text-muted">Order Tracking</a>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="main-navbar py-2">
    <div class="container d-flex align-items-center gap-3">
        <!-- Logo -->
        <a class="navbar-brand me-2" href="/">
            <span style="font-size:24px;font-weight:900;color:var(--primary)">🛒 ShopStore</span>
        </a>

        <!-- Location -->
        <div class="d-none d-md-flex align-items-center gap-1 text-muted" style="font-size:13px;white-space:nowrap">
            <i class="fas fa-map-marker-alt text-primary"></i>
            <div>
                <div style="font-size:10px">Deliver to</div>
                <strong style="color:#000">All</strong>
            </div>
        </div>

        <!-- Search -->
        <div class="search-bar d-flex flex-grow-1">
            <input type="text" class="form-control" placeholder="Search for products, categories or brands..." id="searchInput">
            <button class="btn" onclick="doSearch()"><i class="fas fa-search"></i></button>
        </div>

        <!-- Icons -->
        <div class="d-flex align-items-center gap-3">
            @auth
            <a href="{{ route('orders.history') }}" class="nav-icon text-decoration-none">
                <i class="fas fa-box"></i>
            </a>
            @endauth

            @guest
            <a href="{{ route('login') }}" class="nav-icon text-decoration-none">
                <i class="fas fa-user"></i>
            </a>
            @endguest

            @auth
            <div class="dropdown">
                <a class="nav-icon text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('orders.history') }}"><i class="fas fa-box me-2"></i>My Orders</a></li>
                    @if(auth()->user()->isAdmin())
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Admin Panel</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth

            <a href="{{ route('cart.index') }}" class="nav-icon text-decoration-none position-relative">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge-cart" id="cart-count">0</span>
            </a>
        </div>
    </div>
</nav>

<!-- Category Nav -->
<div class="bg-white border-bottom">
    <div class="container">
        <nav class="d-flex align-items-center gap-1 main-nav py-1 flex-wrap">
            <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->is('products') ? 'active' : '' }}">Shop</a>
            @foreach(\App\Models\Category::where('is_active', true)->orderBy('sort_order')->take(5)->get() as $cat)
            <a href="{{ route('products.category', $cat->slug) }}" class="nav-link">{{ $cat->name }}</a>
            @endforeach
            <a href="{{ route('about') }}" class="nav-link">Blog</a>
            <a href="{{ route('contact') }}" class="nav-link">Contact</a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="{{ route('products.index', ['sort'=>'newest']) }}" class="nav-link">Trending Products</a>
                <a href="{{ route('products.index', ['sale'=>1]) }}" class="btn btn-sale">Almost Finished SALE</a>
            </div>
        </nav>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-0 rounded-0" role="alert">
    <div class="container">{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show m-0 rounded-0" role="alert">
    <div class="container">{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="mt-5 pt-5">
    <!-- Newsletter -->
    <div class="border-bottom border-secondary mb-5">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <h4 class="text-white mb-1">Join our newsletter for $10 off</h4>
                    <p class="mb-0" style="font-size:13px">Register now to get latest updates on promotions & coupons. Don't worry, we not spam!</p>
                </div>
                <div class="col-md-7">
                    <div class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Enter your email address" id="newsletterEmail">
                        <button class="btn btn-primary px-4 fw-bold">SEND</button>
                    </div>
                    <small class="text-muted mt-1 d-block">By subscribing you agree to our Terms & Conditions and Privacy & Cookies Policy.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">
            <!-- About -->
            <div class="col-md-3">
                <h6>Do You Need Help?</h6>
                <p style="font-size:13px">Autoseligen syr. Nek diarask fröbomba. Nör antipol kynoda nynat. Pressa fåmoska.</p>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-phone-alt text-primary"></i>
                    <div>
                        <div style="font-size:11px">Monday-Friday: 08am-9pm</div>
                        <div class="fw-bold text-white">0 800 300-353</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-envelope text-primary"></i>
                    <div>
                        <div style="font-size:11px">Need help with your order?</div>
                        <div class="fw-bold text-white">info@shopstore.com</div>
                    </div>
                </div>
            </div>

            <!-- Make Money -->
            <div class="col-md-2">
                <h6>Make Money with Us</h6>
                <ul class="list-unstyled">
                    @foreach(['Sell on ShopStore','Sell Your Services','Sell on Grogin Business','Sell Your Apps','Become an Affiliate','Advertise Your Products','Sell-Publish with Us','Become a Blowwe Vendor'] as $link)
                    <li class="mb-1"><a href="#">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Let Us Help You -->
            <div class="col-md-2">
                <h6>Let Us Help You</h6>
                <ul class="list-unstyled">
                    @foreach(['Accessibility Statement','Your Orders','Returns & Replacements','Shipping Rates & Policies','Refund and Returns Policy','Privacy Policy','Terms and Conditions','Cookie Settings','Help Center'] as $link)
                    <li class="mb-1"><a href="#">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Get to Know -->
            <div class="col-md-2">
                <h6>Get to Know Us</h6>
                <ul class="list-unstyled">
                    @foreach(['Careers for Grogin','About Grogin','Investtor Relations','Grogin Devices','Customer reviews','Social Responsibility','Store Locations'] as $link)
                    <li class="mb-1"><a href="#">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Download App -->
            <div class="col-md-3">
                <h6>Download our app</h6>
                <a href="#" class="d-block mb-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" style="height:36px">
                    <span class="d-block" style="font-size:11px;color:#aaa">Download App Get -10% Discount</span>
                </a>
                <a href="#" class="d-block mb-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store" style="height:36px">
                    <span class="d-block" style="font-size:11px;color:#aaa">Download App Get -20% Discount</span>
                </a>
                <h6>Follow us on social media:</h6>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom py-3">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span style="font-size:13px">Copyright 2025 © ShopStore. All rights reserved.</span>
            <div class="d-flex align-items-center gap-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" style="height:20px">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height:20px">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" style="height:20px">
            </div>
            <div class="d-flex gap-3">
                <a href="#" class="text-muted" style="font-size:13px">Terms and Conditions</a>
                <a href="#" class="text-muted" style="font-size:13px">Privacy Policy</a>
                <a href="#" class="text-muted" style="font-size:13px">Order Tracking</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
// CSRF token for AJAX
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Cart count
function updateCartCount() {
    fetch('/cart/count')
        .then(r => r.json())
        .then(d => { document.getElementById('cart-count').textContent = d.count || 0; });
}
updateCartCount();

// Add to cart
function addToCart(productId, qty = 1) {
    fetch('/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ product_id: productId, quantity: qty })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById('cart-count').textContent = d.count;
            showToast('✅ ' + d.message, 'success');
        } else {
            showToast('❌ ' + d.message, 'danger');
        }
    });
}

// Toast notification
function showToast(msg, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed shadow-lg`;
    toast.style.cssText = 'bottom:20px;right:20px;z-index:9999;min-width:280px;animation:slideUp .3s ease';
    toast.innerHTML = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Countdown timer
function startCountdown(days, hrs, mins, secs) {
    let total = days*86400 + hrs*3600 + mins*60 + secs;
    setInterval(() => {
        if (total <= 0) return;
        total--;
        document.getElementById('c-days').textContent = Math.floor(total/86400);
        document.getElementById('c-hours').textContent = String(Math.floor((total%86400)/3600)).padStart(2,'0');
        document.getElementById('c-mins').textContent = String(Math.floor((total%3600)/60)).padStart(2,'0');
        document.getElementById('c-secs').textContent = String(total%60).padStart(2,'0');
    }, 1000);
}
startCountdown(47, 6, 57, 8);

// Search
function doSearch() {
    const q = document.getElementById('searchInput').value.trim();
    if (q) window.location.href = '/products?search=' + encodeURIComponent(q);
}
document.getElementById('searchInput')?.addEventListener('keydown', e => { if (e.key === 'Enter') doSearch(); });

@stack('scripts-inline')
</script>
@stack('scripts')
</body>
</html>
