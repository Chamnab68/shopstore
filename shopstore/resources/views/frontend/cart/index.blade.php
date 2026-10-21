{{-- resources/views/frontend/cart/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Shopping Cart - ShopStore')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">🛒 Shopping Cart</h2>
    @if($cartItems->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Your cart is empty</h4>
        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
    @else
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white rounded-3 shadow-sm p-3">
                <table class="table align-middle">
                    <thead><tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr></thead>
                    <tbody id="cart-tbody">
                    @foreach($cartItems as $item)
                    <tr id="cart-row-{{ $item->id }}">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $item->product->image_url }}" style="width:60px;height:60px;object-fit:contain">
                                <div>
                                    <div class="fw-bold" style="font-size:14px">{{ $item->product->name }}</div>
                                    <small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>${{ number_format($item->product->current_price, 2) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeQty({{ $item->id }}, -1)">-</button>
                                <span id="qty-{{ $item->id }}" class="fw-bold">{{ $item->quantity }}</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeQty({{ $item->id }}, 1)">+</button>
                            </div>
                        </td>
                        <td><strong id="sub-{{ $item->id }}">${{ number_format($item->product->current_price * $item->quantity, 2) }}</strong></td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeItem({{ $item->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Order Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <strong id="summary-subtotal">${{ number_format($subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping</span>
                    <strong>{{ $shippingFee > 0 ? '$'.number_format($shippingFee,2) : '<span class="text-success">FREE</span>' }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <strong>Total</strong>
                    <strong class="text-primary fs-5" id="summary-total">${{ number_format($total, 2) }}</strong>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-primary w-100 fw-bold py-3">
                    Proceed to Checkout →
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function changeQty(id, delta) {
    const qtyEl = document.getElementById('qty-' + id);
    const newQty = Math.max(1, parseInt(qtyEl.textContent) + delta);
    fetch('/cart/' + id, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ quantity: newQty })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            qtyEl.textContent = newQty;
            document.getElementById('sub-' + id).textContent = '$' + d.subtotal;
            location.reload();
        }
    });
}

function removeItem(id) {
    if (!confirm('Remove this item?')) return;
    fetch('/cart/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            document.getElementById('cart-row-' + id).remove();
            updateCartCount();
        }
    });
}
</script>
@endpush
