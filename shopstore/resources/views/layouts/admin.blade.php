<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - ShopStore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#6c3fd6; --sidebar-w:260px; }
        * { font-family:'Nunito',sans-serif; }
        body { background:#f0f2f5; }
        .sidebar { width:var(--sidebar-w); min-height:100vh; background:#1a1a2e; position:fixed; top:0; left:0; z-index:1000; transition:transform .3s; overflow-y:auto; }
        .sidebar-brand { padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-brand .logo { font-size:22px; font-weight:800; color:white; text-decoration:none; }
        .sidebar-nav { padding:16px 0; }
        .sidebar-section { padding:8px 24px 4px; font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:rgba(255,255,255,.4); font-weight:700; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,.7); padding:10px 24px; display:flex; align-items:center; gap:12px; font-weight:600; font-size:14px; border-radius:0; transition:all .2s; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color:white; background:rgba(108,63,214,.4); border-right:3px solid var(--primary); }
        .sidebar-nav .nav-link i { width:18px; text-align:center; }
        .sidebar-nav .badge { margin-left:auto; }
        .main-content { margin-left:var(--sidebar-w); padding:0; min-height:100vh; }
        .top-bar { background:white; padding:12px 24px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 1px 10px rgba(0,0,0,.05); position:sticky; top:0; z-index:100; }
        .page-content { padding:24px; }
        .stat-card { background:white; border-radius:14px; padding:20px; position:relative; overflow:hidden; }
        .stat-card .stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; }
        .stat-card .stat-value { font-size:2rem; font-weight:800; }
        .stat-card .stat-label { color:#888; font-size:13px; font-weight:600; }
        .stat-card .stat-change { font-size:12px; font-weight:700; }
        .stat-card::after { content:''; position:absolute; top:-20px; right:-20px; width:100px; height:100px; border-radius:50%; opacity:.06; }
        .stat-card.primary::after { background:var(--primary); }
        .stat-card.success::after { background:#28a745; }
        .stat-card.warning::after { background:#ffc107; }
        .stat-card.danger::after { background:#dc3545; }
        .card { border-radius:14px; border:none; box-shadow:0 2px 10px rgba(0,0,0,.05); }
        .table thead th { background:#f8f9fa; font-weight:700; font-size:13px; border-bottom:2px solid #eee; }
        .btn-primary { background:var(--primary); border-color:var(--primary); }
        .btn-primary:hover { background:#5930b8; border-color:#5930b8; }
        @media(max-width:768px) { .sidebar{transform:translateX(-100%);} .main-content{margin-left:0;} }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="logo">🛒 ShopStore</a>
        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-top:4px">Admin Panel</div>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="sidebar-section mt-2">Catalog</div>
        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Categories
        </a>

        <div class="sidebar-section mt-2">Orders</div>
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i> All Orders
            @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
            <span class="badge bg-danger">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.orders.index', ['status'=>'pending']) }}" class="nav-link">
            <i class="fas fa-clock"></i> Pending Orders
        </a>

        <div class="sidebar-section mt-2">Reports</div>
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> All Reports
        </a>
        <a href="{{ route('admin.reports.sales') }}" class="nav-link">
            <i class="fas fa-chart-line"></i> Sales Report
        </a>
        <a href="{{ route('admin.reports.products') }}" class="nav-link">
            <i class="fas fa-star"></i> Top Products
        </a>
        <a href="{{ route('admin.reports.customers') }}" class="nav-link">
            <i class="fas fa-users"></i> Customers
        </a>
        <a href="{{ route('admin.reports.stock') }}" class="nav-link">
            <i class="fas fa-warehouse"></i> Stock Report
        </a>

        <div class="sidebar-section mt-2">System</div>
        <a href="{{ route('home') }}" class="nav-link" target="_blank">
            <i class="fas fa-store"></i> View Store
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="nav-link w-100 border-0 bg-transparent text-start">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('active')">
                <i class="fas fa-bars"></i>
            </button>
            <h6 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h6>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.orders.index', ['status'=>'pending']) }}" class="btn btn-sm btn-warning position-relative">
                <i class="fas fa-bell"></i>
                @if($pendingCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $pendingCount }}</span>
                @endif
            </a>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank"><i class="fas fa-store me-2"></i>View Store</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.0/dist/chart.umd.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function showToast(msg, type='success') {
    const t = document.createElement('div');
    t.className = `alert alert-${type} position-fixed shadow-lg`;
    t.style.cssText = 'bottom:20px;right:20px;z-index:9999;min-width:280px;';
    t.innerHTML = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

function confirmDelete(url, msg='Are you sure?') {
    if (!confirm(msg)) return;
    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast('✅ Deleted successfully.'); setTimeout(() => location.reload(), 1000); }
        else showToast('❌ ' + d.message, 'danger');
    });
}
</script>
@stack('scripts')
</body>
</html>
