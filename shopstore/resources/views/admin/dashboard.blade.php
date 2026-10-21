@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
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
    <div class="col-6 col-md-3">
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
    <div class="col-6 col-md-3">
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
    <div class="col-6 col-md-3">
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

<div class="row g-4 mb-4">
    {{-- Sales Chart --}}
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Monthly Revenue (Last 12 Months)</h6>
                <a href="{{ route('admin.reports.monthly') }}" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <canvas id="salesChart" height="90"></canvas>
        </div>
    </div>

    {{-- Category Revenue Pie --}}
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Revenue by Category</h6>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Recent Orders --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
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
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
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

{{-- Quick Stats Row --}}
<div class="row g-3">
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Total Products</div>
            <div class="fw-bold fs-4 text-primary">{{ $stats['total_products'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Low Stock Items</div>
            <div class="fw-bold fs-4 text-warning">{{ $stats['low_stock'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Today's Orders</div>
            <div class="fw-bold fs-4 text-success">{{ $stats['today_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted" style="font-size:12px">Today's Revenue</div>
            <div class="fw-bold fs-4 text-info">${{ number_format($stats['today_revenue'],2) }}</div>
        </div>
    </div>
</div>
@endsection

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
        datasets: [{
            label: 'Revenue ($)',
            data: salesData.map(d => d.total),
            borderColor: '#6c3fd6',
            backgroundColor: 'rgba(108,63,214,.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6c3fd6',
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0f0f0' },
                ticks: { callback: v => '$' + v.toLocaleString() } },
            x: { grid: { display: false } }
        }
    }
});

// Category Chart
const catCtx = document.getElementById('categoryChart').getContext('2d');
const catData = @json($categoryRevenue);
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: catData.map(d => d.name),
        datasets: [{
            data: catData.map(d => d.revenue),
            backgroundColor: ['#6c3fd6','#ff6b35','#28a745','#17a2b8','#ffc107','#dc3545'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 12 } } }
        }
    }
});
</script>
@endpush
