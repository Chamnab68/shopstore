@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard Overview')

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="stat-change text-success mt-1"><i class="fas fa-arrow-up me-1"></i>Today: ${{ number_format($stats['today_revenue'], 0) }}</div>
                </div>
                <div class="stat-icon" style="background:#f0ebff;color:var(--primary)"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
                    <div class="stat-change text-success mt-1"><i class="fas fa-arrow-up me-1"></i>Today: {{ $stats['today_orders'] }}</div>
                </div>
                <div class="stat-icon" style="background:#d4edda;color:#28a745"><i class="fas fa-shopping-bag"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Customers</div>
                    <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
                    <div class="stat-change text-warning mt-1"><i class="fas fa-users me-1"></i>Registered</div>
                </div>
                <div class="stat-icon" style="background:#fff3cd;color:#ffc107"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    {{-- បន្ថែមថ្មី៖ ស្ថិតិនៃការណែនាំសមាជិក (Total Referrals) --}}
    <div class="col-6 col-md">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Referrals</div>
                    <div class="stat-value text-info">{{ number_format($stats['total_referrals'] ?? 0) }}</div>
                    <div class="stat-change text-muted mt-1">
                        <a href="{{ route('admin.referral-programs.sync-old') }}" class="text-decoration-none text-info small">
                            <i class="fas fa-sync me-1"></i>Sync Old
                        </a>
                    </div>
                </div>
                <div class="stat-icon" style="background:#e0f2fe;color:#0ea5e9"><i class="fas fa-share-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Pending Orders</div>
                    <div class="stat-value text-danger">{{ $stats['pending_orders'] }}</div>
                    <div class="stat-change text-danger mt-1"><i class="fas fa-exclamation me-1"></i>Needs action</div>
                </div>
                <div class="stat-icon" style="background:#f8d7da;color:#dc3545"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- ══ PUBLIC API DROPDOWN BAR ══════════════════════════════════ --}}
<div class="card mb-4" style="border:2px solid #6c3fd6;border-radius:14px;overflow:visible">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#6c3fd6,#a855f7);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <i class="fas fa-globe" style="color:white;font-size:16px"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:15px">Public API Endpoints</div>
                    <div class="text-muted" style="font-size:12px">Click any API to view data directly in browser — no token required</div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Products Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle fw-bold" data-bs-toggle="dropdown" style="border-radius:10px;font-size:13px">
                        <i class="fas fa-box me-2"></i>Products API
                    </button>
                    <ul class="dropdown-menu shadow-lg" style="border-radius:12px;border:1px solid #eee;min-width:320px;padding:8px">
                        <li><div class="px-3 py-1 text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700">Products</div></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> All Products
                            <span class="float-end text-muted" style="font-size:10px">/public/products</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?search=juice') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Search "juice"
                            <span class="float-end text-muted" style="font-size:10px">?search=juice</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?sort=price_asc') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Sort Price ↑
                            <span class="float-end text-muted" style="font-size:10px">?sort=price_asc</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?sort=price_desc') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Sort Price ↓
                            <span class="float-end text-muted" style="font-size:10px">?sort=price_desc</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?sort=newest') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Newest First
                            <span class="float-end text-muted" style="font-size:10px">?sort=newest</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?sort=rating') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Top Rated
                            <span class="float-end text-muted" style="font-size:10px">?sort=rating</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?featured=1') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Featured Only
                            <span class="float-end text-muted" style="font-size:10px">?featured=1</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?min_price=5&max_price=20') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Price $5–$20
                            <span class="float-end text-muted" style="font-size:10px">?min_price=5&max_price=20</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products?per_page=5') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Per Page 5
                            <span class="float-end text-muted" style="font-size:10px">?per_page=5</span>
                        </a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><div class="px-3 py-1 text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700">Product Detail</div></li>
                        @foreach(\App\Models\Product::where('is_active',true)->take(4)->get() as $p)
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/products/'.$p->slug) }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> {{ Str::limit($p->name, 28) }}
                        </a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Categories Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle fw-bold" data-bs-toggle="dropdown" style="border-radius:10px;font-size:13px">
                        <i class="fas fa-tags me-2"></i>Categories API
                    </button>
                    <ul class="dropdown-menu shadow-lg" style="border-radius:12px;border:1px solid #eee;min-width:300px;padding:8px">
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/categories') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> All Categories
                            <span class="float-end text-muted" style="font-size:10px">/public/categories</span>
                        </a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><div class="px-3 py-1 text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700">Products by Category</div></li>
                        @foreach(\App\Models\Category::where('is_active',true)->withCount('products')->orderBy('sort_order')->get() as $cat)
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/categories/'.$cat->slug.'/products') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> {{ $cat->name }}
                            <span class="badge bg-light text-dark ms-1" style="font-size:10px">{{ $cat->products_count }}</span>
                        </a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Orders Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-outline-warning dropdown-toggle fw-bold" data-bs-toggle="dropdown" style="border-radius:10px;font-size:13px">
                        <i class="fas fa-shopping-bag me-2"></i>Orders API
                    </button>
                    <ul class="dropdown-menu shadow-lg" style="border-radius:12px;border:1px solid #eee;min-width:320px;padding:8px">
                        <li><div class="px-3 py-1 text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700">All Orders</div></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> All Orders
                            <span class="float-end text-muted" style="font-size:10px">/public/orders</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?status=pending') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Pending Orders
                            <span class="float-end text-muted" style="font-size:10px">?status=pending</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?status=confirmed') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Confirmed Orders
                            <span class="float-end text-muted" style="font-size:10px">?status=confirmed</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?status=delivered') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Delivered Orders
                            <span class="float-end text-muted" style="font-size:10px">?status=delivered</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?status=cancelled') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Cancelled Orders
                            <span class="float-end text-muted" style="font-size:10px">?status=cancelled</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?payment_status=paid') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Paid Orders
                            <span class="float-end text-muted" style="font-size:10px">?payment_status=paid</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?payment_status=unpaid') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Unpaid Orders
                            <span class="float-end text-muted" style="font-size:10px">?payment_status=unpaid</span>
                        </a></li>
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders?per_page=5') }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span> Per Page 5
                            <span class="float-end text-muted" style="font-size:10px">?per_page=5</span>
                        </a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><div class="px-3 py-1 text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700">Recent Orders</div></li>
                        @foreach(\App\Models\Order::latest()->take(5)->get() as $o)
                        <li><a class="dropdown-item api-item rounded-2" href="{{ url('api/v1/public/orders/'.$o->id) }}" target="_blank">
                            <span class="badge bg-success me-2">GET</span>
                            {{ $o->order_number }}
                            <span class="badge ms-1 {{ $o->status === 'pending' ? 'bg-warning text-dark' : ($o->status === 'delivered' ? 'bg-success' : 'bg-info') }}" style="font-size:10px">{{ $o->status }}</span>
                        </a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Auth API (token required) --}}
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle fw-bold" data-bs-toggle="dropdown" style="border-radius:10px;font-size:13px">
                        <i class="fas fa-lock me-2"></i>Auth API
                    </button>
                    <ul class="dropdown-menu shadow-lg" style="border-radius:12px;border:1px solid #eee;min-width:340px;padding:8px">
                        <li>
                            <div class="px-3 py-2 rounded-2 mb-1" style="background:#fff3cd;font-size:12px">
                                <i class="fas fa-info-circle me-1 text-warning"></i>
                                These require <strong>Bearer Token</strong> — use Postman
                            </div>
                        </li>
                        @foreach([
                            ['POST','Login → Get Token','/api/v1/auth/login','{"email":"...","password":"..."}'],
                            ['POST','Register Account','/api/v1/auth/register',''],
                            ['GET','My Profile','/api/v1/auth/me',''],
                            ['GET','My Orders','/api/v1/orders',''],
                            ['POST','Place Order','/api/v1/orders',''],
                            ['GET','My Cart','/api/v1/cart',''],
                            ['POST','Add to Cart','/api/v1/cart','{"product_id":1,"quantity":1}'],
                            ['GET','My API Keys','/api/v1/api-keys',''],
                            ['POST','Create API Key','/api/v1/api-keys',''],
                        ] as $ep)
                        <li>
                            <div class="dropdown-item api-item rounded-2" style="cursor:default">
                                <span class="badge {{ $ep[0]==='GET'?'bg-success':'bg-primary' }} me-2">{{ $ep[0] }}</span>
                                {{ $ep[1] }}
                                <div class="text-muted" style="font-size:10px;font-family:monospace;margin-top:2px">{{ $ep[2] }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Monthly Revenue (Last 12 Months)</h6>
                <a href="{{ route('admin.reports.monthly-revenue') }}" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <canvas id="salesChart" height="90"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Revenue by Category</h6>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>
</div>

{{-- Recent Orders + Top Products --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Order</th><th>Customer</th><th>Amount</th><th>Status</th><th>Payment</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td><strong class="text-primary">{{ $order->order_number }}</strong></td>
                            <td>
                                <div class="fw-bold" style="font-size:13px">{{ $order->shipping_name }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $order->user->email ?? '' }}</div>
                            </td>
                            <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                            <td>{!! $order->status_badge !!}</td>
                            <td>{!! $order->payment_badge !!}</td>
                            <td style="font-size:12px">{{ $order->created_at->format('d M Y') }}</td>
                            <td><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Top Products</h6>
                <a href="{{ route('admin.reports.products') }}" class="btn btn-sm btn-outline-primary">All</a>
            </div>
            @foreach($topProducts as $i => $product)
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="fw-bold text-muted" style="width:20px">{{ $i+1 }}</div>
                <img src="{{ $product->image_url }}" style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:#f8f9fa;border:1px solid #eee">
                <div class="flex-grow-1">
                    <div style="font-size:13px;font-weight:600">{{ Str::limit($product->name,30) }}</div>
                    <div class="text-muted" style="font-size:11px">{{ number_format($product->total_sold) }} sold</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-3">
    <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted" style="font-size:12px">Total Products</div><div class="fw-bold fs-4 text-primary">{{ $stats['total_products'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted" style="font-size:12px">Low Stock Items</div><div class="fw-bold fs-4 text-warning">{{ $stats['low_stock'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted" style="font-size:12px">Today's Orders</div><div class="fw-bold fs-4 text-success">{{ $stats['today_orders'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted" style="font-size:12px">Today's Revenue</div><div class="fw-bold fs-4 text-info">${{ number_format($stats['today_revenue'],2) }}</div></div></div>
</div>

@endsection

@push('styles')
<style>
/* Stat Cards Design */
.stat-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    border: 1px solid #f0f0f0;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.05);
}
.stat-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 6px;
}
.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #2b2f38;
    line-height: 1.2;
}
.stat-change {
    font-size: 12px;
    font-weight: 500;
}
.stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* API Dropdown Styles */
.api-item { font-size: 13px; padding: 8px 12px; transition: all .15s; }
.api-item:hover { background: #f0ebff !important; color: #6c3fd6 !important; }
.dropdown-menu { max-height: 420px; overflow-y: auto; }
</style>
@endpush

@push('scripts')
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesData = @json($monthlySales);
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesData.map(d => months[d.month-1] + ' ' + d.year),
        datasets: [{ label: 'Revenue ($)', data: salesData.map(d => d.total), borderColor: '#6c3fd6', backgroundColor: 'rgba(108,63,214,.1)', fill: true, tension: 0.4, pointBackgroundColor: '#6c3fd6', pointRadius: 5 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } }, x: { grid: { display: false } } } }
});

// Category Chart
const catCtx = document.getElementById('categoryChart').getContext('2d');
const catData = @json($categoryRevenue);
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: catData.map(d => d.name),
        datasets: [{ data: catData.map(d => d.revenue), backgroundColor: ['#6c3fd6','#ff6b35','#28a745','#17a2b8','#ffc107','#dc3545'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } } }
});
</script>
@endpush