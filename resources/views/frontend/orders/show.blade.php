{{-- resources/views/frontend/orders/show.blade.php --}}
@extends('layouts.app')
@section('title','Order ' . $order->order_number)
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('orders.history') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-arrow-left me-1"></i>My Orders</a>
            <strong class="fs-5">{{ $order->order_number }}</strong>
        </div>
        <div class="d-flex gap-2">
            {!! $order->status_badge !!}
            {!! $order->payment_badge !!}
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-end">Subtotal</th></tr></thead>
                            <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $item->product ? $item->product->image_url : asset('images/no-image.png') }}" style="width:50px;height:50px;object-fit:contain">
                                        <div class="fw-bold" style="font-size:13px">{{ $item->product_name }}</div>
                                    </div>
                                </td>
                                <td>${{ number_format($item->sale_price ?? $item->price,2) }}</td>
                                <td>× {{ $item->quantity }}</td>
                                <td class="text-end fw-bold">${{ number_format($item->subtotal,2) }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">${{ number_format($order->subtotal,2) }}</td></tr>
                                <tr><td colspan="3" class="text-end">Shipping</td><td class="text-end">${{ number_format($order->shipping_fee,2) }}</td></tr>
                                <tr><td colspan="3" class="text-end fw-bold fs-5">Total</td><td class="text-end fw-bold fs-5 text-primary">${{ number_format($order->total,2) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Shipping Address</h6>
                    <p>{{ $order->shipping_name }}<br>{{ $order->shipping_phone }}<br>{{ $order->shipping_address }}</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Payment</h6>
                    <div>Method: <strong>{{ ucwords(str_replace('_',' ',$order->payment_method)) }}</strong></div>
                    <div class="mt-1">Status: {!! $order->payment_badge !!}</div>
                </div>
            </div>
            @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
            <a href="{{ route('orders.payment',$order->id) }}" class="btn btn-warning w-100 fw-bold">
                <i class="fas fa-credit-card me-2"></i>Complete Payment
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
