@extends('layouts.admin')
@section('title','Reports')
@section('page-title','Reports & Analytics')

@section('content')
<div class="row g-4">
    @php
    $reports = [
        ['title'=>'Sales Summary','icon'=>'chart-line','color'=>'primary','desc'=>'Daily sales breakdown by date range','route'=>'admin.reports.sales'],
        ['title'=>'Revenue by Category','icon'=>'tags','color'=>'success','desc'=>'Which categories generate the most revenue','route'=>'admin.reports.category'],
        ['title'=>'Top Products','icon'=>'star','color'=>'warning','desc'=>'Best-selling products ranked by quantity sold','route'=>'admin.reports.products'],
        ['title'=>'Customer Report','icon'=>'users','color'=>'info','desc'=>'Customer lifetime value and order frequency','route'=>'admin.reports.customers'],
        ['title'=>'Order Status Report','icon'=>'tasks','color'=>'secondary','desc'=>'Breakdown of orders by current status','route'=>'admin.reports.order-status'],
        ['title'=>'Payment Report','icon'=>'credit-card','color'=>'primary','desc'=>'Payment methods and status analysis','route'=>'admin.reports.payment'],
        ['title'=>'Stock Report','icon'=>'warehouse','color'=>'danger','desc'=>'Current inventory levels and low stock alerts','route'=>'admin.reports.stock'],
        ['title'=>'Monthly Revenue Trend','icon'=>'chart-area','color'=>'success','desc'=>'Revenue growth over the past 12 months','route'=>'admin.reports.monthly'],
        ['title'=>'Customer Retention','icon'=>'sync','color'=>'info','desc'=>'New vs returning customers per month','route'=>'admin.reports.retention'],
        ['title'=>'Delivery Performance','icon'=>'truck','color'=>'warning','desc'=>'Delivery times and fulfillment rates','route'=>'admin.reports.delivery'],
    ];
    @endphp

    @foreach($reports as $i => $r)
    <div class="col-md-4 col-lg-3">
        <a href="{{ route($r['route']) }}" class="text-decoration-none">
            <div class="card h-100 p-4 text-center" style="transition:transform .2s;cursor:pointer" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                <div class="rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px;height:60px;background:rgba(108,63,214,.1)">
                    <i class="fas fa-{{ $r['icon'] }} fa-2x text-{{ $r['color'] }}"></i>
                </div>
                <div class="badge bg-light text-muted mb-2" style="font-size:10px">Report #{{ $i+1 }}</div>
                <h6 class="fw-bold mb-2">{{ $r['title'] }}</h6>
                <p class="text-muted mb-0" style="font-size:12px">{{ $r['desc'] }}</p>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
