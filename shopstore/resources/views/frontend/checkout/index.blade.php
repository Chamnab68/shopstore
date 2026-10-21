{{-- resources/views/frontend/checkout/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Checkout - ShopStore')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Checkout</h2>
    <form action="{{ route('orders.place') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3">Shipping Information</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name *</label>
                        <input type="text" name="shipping_name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number *</label>
                        <input type="text" name="shipping_phone" class="form-control" value="{{ auth()->user()->phone }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Address *</label>
                        <textarea name="shipping_address" class="form-control" rows="3" required>{{ auth()->user()->address }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Order Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
                    </div>
                </div>

                <div class="bg-white rounded-3 shadow-sm p-4">
                    <h5 class="fw-bold mb-3">Payment Method</h5>
                    <div class="form-check mb-3 p-3 border rounded-3">
                        <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" id="bank" checked>
                        <label class="form-check-label fw-bold" for="bank">
                            <i class="fas fa-university me-2 text-primary"></i>Bank Transfer / QR Code
                        </label>
                        <div class="text-muted mt-1" style="font-size:13px">Pay via bank transfer. You will receive bank details after placing your order.</div>
                    </div>
                    <div class="form-check p-3 border rounded-3">
                        <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery" id="cod">
                        <label class="form-check-label fw-bold" for="cod">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i>Cash on Delivery
                        </label>
                        <div class="text-muted mt-1" style="font-size:13px">Pay when your order arrives.</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="bg-white rounded-3 shadow-sm p-4 sticky-top" style="top:100px">
                    <h5 class="fw-bold mb-3">Your Order</h5>
                    @foreach($cartItems as $item)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="position-relative">
                            <img src="{{ $item->product->image_url }}" style="width:50px;height:50px;object-fit:contain">
                            <span class="badge bg-primary position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size:10px">{{ $item->quantity }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-size:13px;font-weight:600">{{ Str::limit($item->product->name,40) }}</div>
                        </div>
                        <strong>${{ number_format($item->product->current_price * $item->quantity, 2) }}</strong>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <strong>{{ $shippingFee > 0 ? '$'.number_format($shippingFee,2) : 'FREE' }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-5">Total</strong>
                        <strong class="fs-5 text-primary">${{ number_format($total, 2) }}</strong>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-3 fs-5">
                        Place Order →
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
