{{-- resources/views/frontend/checkout/success.blade.php --}}
@extends('layouts.app')
@section('title','Order Placed Successfully!')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div style="width:100px;height:100px;background:#d4edda;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px">
                <i class="fas fa-check fa-3x text-success"></i>
            </div>
            <h2 class="fw-bold mb-2">Order Placed Successfully! 🎉</h2>
            <p class="text-muted mb-1">Thank you for shopping with ShopStore.</p>
            <p class="text-muted">Your order <strong class="text-primary">{{ $order->order_number }}</strong> has been received.</p>
            <div class="card shadow-sm mt-4 text-start">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Order Summary</h6>
                    @foreach($order->items as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                        <strong>${{ number_format($item->subtotal,2) }}</strong>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span class="text-primary">${{ number_format($order->total,2) }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 justify-content-center mt-4">
                <a href="{{ route('orders.payment',$order->id) }}" class="btn btn-warning fw-bold px-4">
                    <i class="fas fa-credit-card me-2"></i>Pay Now
                </a>
                <a href="{{ route('orders.history') }}" class="btn btn-outline-primary fw-bold px-4">
                    My Orders
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
