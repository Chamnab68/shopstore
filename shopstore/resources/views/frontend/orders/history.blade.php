{{-- resources/views/frontend/orders/history.blade.php --}}
@extends('layouts.app')
@section('title','My Orders')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">📦 My Orders</h2>
    @if($orders->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No orders yet</h4>
        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Start Shopping</a>
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td><strong class="text-primary">{{ $order->order_number }}</strong></td>
                        <td style="font-size:13px">{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{ $order->items->count() }} items</td>
                        <td><strong>${{ number_format($order->total,2) }}</strong></td>
                        <td>{!! $order->status_badge !!}</td>
                        <td>{!! $order->payment_badge !!}</td>
                        <td>
                            <a href="{{ route('orders.show',$order->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                            <a href="{{ route('orders.payment',$order->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-credit-card me-1"></i>Pay
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
