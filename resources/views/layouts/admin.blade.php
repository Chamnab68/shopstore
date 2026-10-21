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
        
        .sidebar { 
            width: var(--sidebar-w); 
            height: 100vh; 
            background: #1a1a2e; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 1000; 
            transition: transform .3s; 
            overflow-y: auto; 
            scrollbar-width: thin; 
        }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }
        .sidebar-brand { padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-brand .logo { font-size:22px; font-weight:800; color:white; text-decoration:none; }
        .sidebar-nav { padding:16px 0; }
        .sidebar-section { padding:8px 24px 4px; font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:rgba(255,255,255,.4); font-weight:700; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,.7); padding:10px 24px; display:flex; align-items:center; gap:12px; font-weight:600; font-size:14px; border-radius:0; transition:all .2s; text-decoration:none; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color:white; background:rgba(108,63,214,.4); border-right:3px solid var(--primary); }
        .sidebar-nav .nav-link i { width:18px; text-align:center; }
        .sidebar-nav .badge { margin-left:auto; }
        
        .sidebar-nav .collapse .nav-link {
            padding: 8px 16px; font-size: 13px; color: rgba(255, 255, 255, 0.6);
            border-radius: 6px; margin: 2px 12px; border-right: none;
        }
        .sidebar-nav .collapse .nav-link:hover, 
        .sidebar-nav .collapse .nav-link.active {
            color: white; background: rgba(255, 255, 255, 0.1); border-right: none;
        }
        .main-content { margin-left:var(--sidebar-w); padding:0; min-height:100vh; }
        .top-bar { background:white; padding:12px 24px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 1px 10px rgba(0,0,0,.05); position:sticky; top:0; z-index:100; }
        .page-content { padding:24px; }
        .card { border-radius:14px; border:none; box-shadow:0 2px 10px rgba(0,0,0,.05); }
        @media(max-width:768px) { .sidebar{transform:translateX(-100%);} .main-content{margin-left:0;} }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="logo"><i class="fas fa-store me-1"></i> ShopStore</a>
        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-top:4px">Admin Panel</div>
    </div>
    
    @php
        $user = Auth::user();
        $userRole = '';
        if ($user) {
            if (isset($user->role_id) && $user->role_id) {
                $roleRecord = \App\Models\Role::find($user->role_id);
                $userRole = $roleRecord ? trim($roleRecord->name) : '';
            } elseif (isset($user->roleModel)) {
                $userRole = trim($user->roleModel->name ?? '');
            } elseif (isset($user->role)) {
                $userRole = trim($user->role);
            }
        }
    @endphp

    <nav class="sidebar-nav">
        <!-- MAIN -->
        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <!-- CATALOG (Admin និង Marketing Manager ឃើញ) -->
        @if(in_array($userRole, ['admin', 'marketing_manager', 'Super Admin']))
        <div class="sidebar-section mt-2">Catalog</div>
        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Categories
        </a>
        @endif

        <!-- ORDERS (សម្រាប់ Admin) -->
        @if(in_array($userRole, ['admin', 'Super Admin']))
        <div class="sidebar-section mt-2">Orders</div>
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->is('admin/orders*') && !request()->has('status') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i> All Orders
            @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
            <span class="badge bg-danger">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.orders.index', ['status'=>'pending']) }}" class="nav-link {{ request()->is('admin/orders*') && request('status') == 'pending' ? 'active' : '' }}">
            <i class="fas fa-clock"></i> Pending Orders
        </a>
        @endif

        <div class="sidebar-section mt-2">ACTIVITY LOG</div>
        <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
        <i class="fas fa-history me-2"></i> Activity Logs
    </a>
</li>

        <!-- ROLE & USER (សម្រាប់ Admin) -->
        @if(in_array($userRole, ['admin', 'Super Admin']))
        <div class="sidebar-section mt-2">ROLE & USER</div>
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i> User Management
        </a>
        @endif

        <!-- REPORTS (Admin និង Marketing Manager ឃើញ) -->
        @if(in_array($userRole, ['admin', 'marketing_manager', 'Super Admin']))
        <div class="sidebar-section mt-2">Reports</div>
        <div class="nav-item">
             <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/reports*') ? 'active' : '' }}" 
                data-bs-toggle="collapse" href="#reportsDropdown" role="button" 
                aria-expanded="{{ request()->is('admin/reports*') ? 'true' : 'false' }}" aria-controls="reportsDropdown">
                <span><i class="fas fa-chart-bar"></i> All Reports</span>
                <i class="fas fa-chevron-down small"></i>
             </a>
             <div class="collapse {{ request()->is('admin/reports*') ? 'show' : '' }}" id="reportsDropdown">
                 <ul class="nav flex-column ps-2 my-1">
                     <li class="nav-item"><a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">Sales Summary</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.revenue-category') }}" class="nav-link {{ request()->routeIs('admin.reports.revenue-category') ? 'active' : '' }}">Revenue by Category</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.products') }}" class="nav-link {{ request()->routeIs('admin.reports.products') ? 'active' : '' }}">Top Products</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.customers') }}" class="nav-link {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}">Customer Report</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.order-status') }}" class="nav-link {{ request()->routeIs('admin.reports.order-status') ? 'active' : '' }}">Order Status Report</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.payment') }}" class="nav-link {{ request()->routeIs('admin.reports.payment') ? 'active' : '' }}">Payment Report</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.stock') }}" class="nav-link {{ request()->routeIs('admin.reports.stock') ? 'active' : '' }}">Stock Report</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.monthly-revenue') }}" class="nav-link {{ request()->routeIs('admin.reports.monthly-revenue') ? 'active' : '' }}">Monthly Revenue Trend</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.customer-retention') }}" class="nav-link {{ request()->routeIs('admin.reports.customer-retention') ? 'active' : '' }}">Customer Retention</a></li>
                     <li class="nav-item"><a href="{{ route('admin.reports.delivery') }}" class="nav-link {{ request()->routeIs('admin.reports.delivery') ? 'active' : '' }}">Delivery Performance</a></li>
                 </ul>
             </div>
        </div>
        @endif

        <!-- LOYALTY PROGRAM (សម្រាប់ Admin និង Loyalty Program Manager ឃើញ) -->
        @if(in_array($userRole, ['admin', 'Super Admin', 'Loyalty Program Manager']))
        <div class="sidebar-section mt-2">Loyalty Program</div>
        <div class="nav-item">
             <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.loyalty.*') ? 'active' : '' }}" 
                data-bs-toggle="collapse" href="#loyaltyDropdown" role="button" 
                aria-expanded="{{ request()->routeIs('admin.loyalty.*') ? 'true' : 'false' }}" aria-controls="loyaltyDropdown">
                <span><i class="fas fa-crown"></i> Loyalty Program</span>
                <i class="fas fa-chevron-down small"></i>
             </a>
             <div class="collapse {{ request()->routeIs('admin.loyalty.*') ? 'show' : '' }}" id="loyaltyDropdown">
                 <ul class="nav flex-column ps-2 my-1">
                     <li class="nav-item"><a href="{{ route('admin.loyalty.tiers.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty.tiers.*') ? 'active' : '' }}">Tiers</a></li>
                     <li class="nav-item"><a href="{{ route('admin.loyalty.rules.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty.rules.*') ? 'active' : '' }}">Points Rules</a></li>
                     <li class="nav-item"><a href="{{ route('admin.loyalty.campaigns.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty.campaigns.*') ? 'active' : '' }}">Bonus Campaigns</a></li>
                     <li class="nav-item"><a href="{{ route('admin.loyalty.rewards.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty.rewards.*') ? 'active' : '' }}">Rewards Catalog</a></li>
                     <li class="nav-item"><a href="{{ route('admin.loyalty.redemptions.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty.redemptions.*') ? 'active' : '' }}">Redemptions</a></li>
                 </ul>
             </div>
        </div>
        @endif

        <!-- SYSTEM -->
        <div class="sidebar-section mt-3">System</div>
        <a href="{{ route('home') }}" class="nav-link" target="_blank">
            <i class="fas fa-store"></i> View Store
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link text-danger w-100 border-0 bg-transparent text-start">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>
</div>

<div class="main-content">
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('active')">
                <i class="fas fa-bars"></i>
            </button>
            <h6 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h6>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>{{ auth()->user()->name ?? 'Admin' }}
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
</script>
@stack('scripts')
</body>
</html>