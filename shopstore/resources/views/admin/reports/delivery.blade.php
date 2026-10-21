@extends('layouts.admin')
@section('title','Delivery Performance')
@section('page-title','🚚 Delivery Performance Report')
@section('content')
<div class="mb-4"><a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a></div>
<div class="row g-4 mb-4">
    @foreach($data as $row)
    <div class="col-md-4">
        <div class="card p-4 text-center">
            <div class="fw-bold fs-3">{{ $row->count }}</div>
            <div class="text-muted">{{ ucfirst($row->status) }} Orders</div>
            <div class="mt-2 text-primary">Avg: {{ number_format($row->avg_days,1) }} days</div>
        </div>
    </div>
    @endforeach
</div>
<div class="card"><div class="card-body">
    <h6 class="fw-bold mb-3">Recent Delivered Orders</h6>
    <table class="table table-hover align-middle">
        <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Ordered</th><th>Delivered</th><th>Days</th></tr></thead>
        <tbody>
        @foreach($recentDelivered as $order)
        <tr>
            <td class="text-primary fw-bold">{{ $order->order_number }}</td>
            <td>{{ $order->shipping_name }}</td>
            <td>${{ number_format($order->total,2) }}</td>
            <td>{{ $order->created_at->format('d M Y') }}</td>
            <td>{{ $order->updated_at->format('d M Y') }}</td>
            <td><span class="badge bg-info">{{ $order->created_at->diffInDays($order->updated_at) }}d</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
@endsection
