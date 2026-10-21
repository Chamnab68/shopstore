@extends('layouts.admin')
@section('title','Order ' . $order->order_number)
@section('page-title','Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
        <strong class="fs-5">{{ $order->order_number }}</strong>
    </div>
    <div class="d-flex gap-2">
        {!! $order->status_badge !!}
        {!! $order->payment_badge !!}
    </div>
</div>

<div class="row g-4">
    {{-- Left Column --}}
    <div class="col-lg-8">
        {{-- Order Items --}}
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
                                    <img src="{{ $item->product ? $item->product->image_url : asset('images/no-image.png') }}" style="width:50px;height:50px;object-fit:contain;border-radius:8px;background:#f8f9fa">
                                    <div>
                                        <div class="fw-bold" style="font-size:13px">{{ $item->product_name }}</div>
                                        @if($item->product)
                                        <a href="{{ route('products.show',$item->product->slug) }}" class="text-muted" style="font-size:11px" target="_blank">View product</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->sale_price)
                                <del class="text-muted">${{ number_format($item->price,2) }}</del>
                                <span class="text-primary fw-bold">${{ number_format($item->sale_price,2) }}</span>
                                @else
                                <span>${{ number_format($item->price,2) }}</span>
                                @endif
                            </td>
                            <td>× {{ $item->quantity }}</td>
                            <td class="text-end fw-bold">${{ number_format($item->subtotal,2) }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" class="text-end text-muted">Subtotal</td><td class="text-end">${{ number_format($order->subtotal,2) }}</td></tr>
                            <tr><td colspan="3" class="text-end text-muted">Shipping</td><td class="text-end">${{ number_format($order->shipping_fee,2) }}</td></tr>
                            @if($order->discount > 0)
                            <tr><td colspan="3" class="text-end text-success">Discount</td><td class="text-end text-success">-${{ number_format($order->discount,2) }}</td></tr>
                            @endif
                            <tr><td colspan="3" class="text-end fw-bold fs-5">Total</td><td class="text-end fw-bold fs-5 text-primary">${{ number_format($order->total,2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Receipt Image --}}
        @if($order->receipt_image)
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">💳 Payment Receipt</h6>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="{{ asset('storage/'.$order->receipt_image) }}" alt="Receipt" class="img-fluid rounded-3 border">
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2"><span class="text-muted">Uploaded:</span> <strong>{{ $order->receipt_uploaded_at?->format('d M Y H:i') }}</strong></div>
                        <div class="alert alert-info" style="font-size:13px">
                            <i class="fas fa-info-circle me-2"></i>Customer has uploaded a payment receipt. Please verify and confirm the payment.
                        </div>
                        <button class="btn btn-success fw-bold" onclick="quickApprove('confirmed','paid')">
                            <i class="fas fa-check me-2"></i>Verify & Confirm Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Admin Approval --}}
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">🔧 Update Order Status</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:13px">Order Status</label>
                        <select id="approve-status" class="form-select">
                            @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:13px">Payment Status</label>
                        <select id="approve-payment" class="form-select">
                            <option value="">No change</option>
                            <option value="unpaid" {{ $order->payment_status==='unpaid'?'selected':'' }}>Unpaid</option>
                            <option value="paid" {{ $order->payment_status==='paid'?'selected':'' }}>Paid</option>
                            <option value="refunded" {{ $order->payment_status==='refunded'?'selected':'' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size:13px">Admin Notes</label>
                        <textarea id="approve-notes" class="form-control" rows="3" placeholder="Internal notes about this order...">{{ $order->admin_notes }}</textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary fw-bold px-4" onclick="approveOrder()">
                            <i class="fas fa-save me-2"></i>Update Order
                        </button>
                    </div>
                </div>

                @if($order->approvedBy)
                <hr>
                <div class="text-muted" style="font-size:12px">
                    Last updated by <strong>{{ $order->approvedBy->name }}</strong> at {{ $order->approved_at?->format('d M Y H:i') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">
        {{-- Customer Info --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">👤 Customer Info</h6>
                <div class="mb-2"><i class="fas fa-user me-2 text-muted"></i><strong>{{ $order->user->name ?? $order->shipping_name }}</strong></div>
                <div class="mb-2"><i class="fas fa-envelope me-2 text-muted"></i>{{ $order->user->email ?? '—' }}</div>
                <div class="mb-2"><i class="fas fa-phone me-2 text-muted"></i>{{ $order->shipping_phone }}</div>
                @if($order->user)
                <div class="mt-3 p-2 bg-light rounded-3">
                    <div class="text-muted" style="font-size:11px">Total orders by this customer</div>
                    <strong>{{ $order->user->orders()->count() }} orders</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Shipping --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">📍 Shipping Address</h6>
                <div class="mb-1"><strong>{{ $order->shipping_name }}</strong></div>
                <div class="text-muted">{{ $order->shipping_phone }}</div>
                <div class="mt-2">{{ $order->shipping_address }}</div>
                @if($order->notes)
                <hr>
                <div class="text-muted" style="font-size:12px">Notes:</div>
                <div style="font-size:13px">{{ $order->notes }}</div>
                @endif
            </div>
        </div>

        {{-- Payment --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">💳 Payment Info</h6>
                <div class="mb-2">
                    <span class="text-muted" style="font-size:12px">Method:</span>
                    <strong class="d-block">{{ str_replace('_',' ',ucfirst($order->payment_method)) }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted" style="font-size:12px">Status:</span>
                    <div>{!! $order->payment_badge !!}</div>
                </div>
                @if($order->receipt_uploaded_at)
                <div>
                    <span class="text-muted" style="font-size:12px">Receipt uploaded:</span>
                    <strong class="d-block">{{ $order->receipt_uploaded_at->format('d M Y H:i') }}</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">⚡ Quick Actions</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-success btn-sm" onclick="quickApprove('confirmed','paid')">
                        <i class="fas fa-check-circle me-2"></i>Confirm & Mark Paid
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="quickApprove('shipped',null)">
                        <i class="fas fa-truck me-2"></i>Mark as Shipped
                    </button>
                    <button class="btn btn-info btn-sm" onclick="quickApprove('delivered','paid')">
                        <i class="fas fa-home me-2"></i>Mark as Delivered
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="quickApprove('cancelled',null)">
                        <i class="fas fa-times-circle me-2"></i>Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const orderId = {{ $order->id }};

function approveOrder() {
    const status  = document.getElementById('approve-status').value;
    const payment = document.getElementById('approve-payment').value;
    const notes   = document.getElementById('approve-notes').value;

    fetch(`/admin/orders/${orderId}/approve`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ status, payment_status: payment || undefined, admin_notes: notes })
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast('✅ Order updated!'); setTimeout(() => location.reload(), 1000); }
        else showToast('❌ ' + (d.message || 'Error'), 'danger');
    });
}

function quickApprove(status, payment) {
    if (!confirm(`Update order to "${status}"?`)) return;
    const notes = document.getElementById('approve-notes').value;
    fetch(`/admin/orders/${orderId}/approve`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ status, payment_status: payment, admin_notes: notes })
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast('✅ ' + d.message); setTimeout(() => location.reload(), 1000); }
    });
}
</script>
@endpush
