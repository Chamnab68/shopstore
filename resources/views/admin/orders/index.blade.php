@extends('layouts.admin')
@section('title','Manage Orders')
@section('page-title','Orders Management')

@section('content')
{{-- Stats Bar --}}
<div class="row g-3 mb-4">
    @foreach([['label'=>'Total','value'=>$stats['total'],'color'=>'primary','icon'=>'shopping-bag'],['label'=>'Pending','value'=>$stats['pending'],'color'=>'warning','icon'=>'clock'],['label'=>'Confirmed','value'=>$stats['confirmed'],'color'=>'info','icon'=>'check-circle'],['label'=>'Delivered','value'=>$stats['delivered'],'color'=>'success','icon'=>'truck']] as $s)
    <div class="col-6 col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(108,63,214,.1)">
                    <i class="fas fa-{{ $s['icon'] }} text-{{ $s['color'] }}"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px">{{ $s['label'] }}</div>
                    <div class="fw-bold fs-5">{{ $s['value'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold" style="font-size:12px">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Order #, Name, Phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold" style="font-size:12px">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold" style="font-size:12px">Payment</label>
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="">All Payments</option>
                    <option value="unpaid" {{ request('payment_status')==='unpaid'?'selected':'' }}>Unpaid</option>
                    <option value="paid" {{ request('payment_status')==='paid'?'selected':'' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold" style="font-size:12px">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold" style="font-size:12px">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Bulk Actions --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <select id="bulk-action" class="form-select form-select-sm" style="width:160px">
                    <option value="">Bulk Action</option>
                    <option value="confirm">Confirm</option>
                    <option value="deliver">Mark Delivered</option>
                    <option value="cancel">Cancel</option>
                </select>
                <button class="btn btn-sm btn-outline-primary" onclick="applyBulk()">Apply</button>
            </div>
            <div class="text-muted" style="font-size:13px">{{ $orders->total() }} orders found</div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="check-all" onchange="toggleAll(this)"></th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Receipt</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><input type="checkbox" class="order-check" value="{{ $order->id }}"></td>
                    <td><a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary fw-bold text-decoration-none">{{ $order->order_number }}</a></td>
                    <td>
                        <div class="fw-bold" style="font-size:13px">{{ $order->shipping_name }}</div>
                        <div class="text-muted" style="font-size:11px">{{ $order->shipping_phone }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $order->items->count() }} items</span></td>
                    <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                    <td>{!! $order->status_badge !!}</td>
                    <td>{!! $order->payment_badge !!}</td>
                    <td>
                        @if($order->receipt_image)
                        <a href="{{ asset('storage/'.$order->receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-image me-1"></i>View
                        </a>
                        @else
                        <span class="text-muted" style="font-size:12px">No receipt</span>
                        @endif
                    </td>
                    <td style="font-size:12px;white-space:nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-5">No orders found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" style="font-size:13px">Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}</div>
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAll(el) {
    document.querySelectorAll('.order-check').forEach(c => c.checked = el.checked);
}
function applyBulk() {
    const action = document.getElementById('bulk-action').value;
    const ids = [...document.querySelectorAll('.order-check:checked')].map(c => c.value);
    if (!action || !ids.length) { alert('Select action and orders.'); return; }
    if (!confirm(`Apply "${action}" to ${ids.length} order(s)?`)) return;

    fetch('{{ route("admin.orders.bulk") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ action, order_ids: ids })
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast('✅ ' + d.message); setTimeout(() => location.reload(), 1000); }
    });
}
</script>
@endpush
