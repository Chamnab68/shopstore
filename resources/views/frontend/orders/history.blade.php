{{-- resources/views/frontend/orders/history.blade.php --}}
@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Orders</h2>
            <p class="text-muted mb-0">Track and manage all your orders</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Empty State --}}
    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3 d-block"></i>
            <h4 class="text-muted fw-semibold">No orders yet</h4>
            <p class="text-muted">You haven't placed any orders. Start shopping now!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary px-5 mt-2">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>

    @else

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-primary">{{ $orders->total() }}</div>
                    <div class="text-muted small">Total Orders</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-warning">
                        {{ $orders->getCollection()->where('status', 'pending')->count() }}
                    </div>
                    <div class="text-muted small">Pending</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-success">
                        {{ $orders->getCollection()->where('status', 'delivered')->count() }}
                    </div>
                    <div class="text-muted small">Delivered</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-danger">
                        {{ $orders->getCollection()->where('status', 'cancelled')->count() }}
                    </div>
                    <div class="text-muted small">Cancelled</div>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="card border-0 shadow-sm d-none d-md-block">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Order #</th>
                                <th class="py-3">Date</th>
                                <th class="py-3">Items</th>
                                <th class="py-3">Total</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Payment</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>

                                {{-- Order Number --}}
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">
                                        #{{ $order->order_number }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td>
                                    <div class="small">
                                        {{ $order->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-muted" style="font-size:11px">
                                        {{ $order->created_at->format('h:i A') }}
                                    </div>
                                </td>

                                {{-- Items --}}
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $order->items->count() }}
                                        {{ Str::plural('item', $order->items->count()) }}
                                    </span>
                                </td>

                                {{-- Total --}}
                                <td>
                                    <strong>${{ number_format($order->total, 2) }}</strong>
                                    @if($order->shipping_fee == 0)
                                        <div class="text-success" style="font-size:11px">
                                            Free shipping
                                        </div>
                                    @else
                                        <div class="text-muted" style="font-size:11px">
                                            +${{ number_format($order->shipping_fee, 2) }} ship
                                        </div>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>{!! $order->status_badge !!}</td>

                                {{-- Payment --}}
                                <td>
                                    {!! $order->payment_badge !!}
                                    <div class="text-muted text-capitalize mt-1" style="font-size:11px">
                                        {{ $order->payment_method_label }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">

                                        {{-- View --}}
                                        <a href="{{ route('orders.show', $order->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Pay Now (bank transfer + unpaid only) --}}
                                        @if($order->isUnpaid()
                                            && $order->isBankTransfer()
                                            && !in_array($order->status, ['cancelled', 'delivered']))
                                            <a href="{{ route('orders.payment', $order->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Pay Now">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
                                        @endif

                                        {{-- Cancel (POST — no @method PATCH needed) --}}
                                        @if($order->canBeCancelled())
                                            <form action="{{ route('orders.cancel', $order->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Cancel order #{{ $order->order_number }}?')">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Cancel Order">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="d-md-none">
            @foreach($orders as $order)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-bold text-primary">
                                #{{ $order->order_number }}
                            </span>
                            <div class="text-muted small">
                                {{ $order->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>
                        <strong>${{ number_format($order->total, 2) }}</strong>
                    </div>

                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        {!! $order->status_badge !!}
                        {!! $order->payment_badge !!}
                        <span class="badge bg-light text-dark border">
                            {{ $order->items->count() }}
                            {{ Str::plural('item', $order->items->count()) }}
                        </span>
                    </div>

                    <div class="d-flex gap-2">

                        {{-- View --}}
                        <a href="{{ route('orders.show', $order->id) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-eye me-1"></i>View
                        </a>

                        {{-- Pay Now --}}
                        @if($order->isUnpaid()
                            && $order->isBankTransfer()
                            && !in_array($order->status, ['cancelled', 'delivered']))
                            <a href="{{ route('orders.payment', $order->id) }}"
                               class="btn btn-sm btn-warning flex-fill">
                                <i class="fas fa-credit-card me-1"></i>Pay
                            </a>
                        @endif

                        {{-- Cancel (POST — no @method PATCH needed) --}}
                        @if($order->canBeCancelled())
                            <form action="{{ route('orders.cancel', $order->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Cancel order #{{ $order->order_number }}?')"
                                  class="flex-fill">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger w-100">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Clean Pagination --}}
        @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Orders pagination">
                    <ul class="pagination pagination-sm mb-0" style="gap:4px">

                        {{-- Previous --}}
                        @if($orders->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link rounded px-3" style="font-size:13px">
                                    &laquo; Prev
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded px-3"
                                   href="{{ $orders->previousPageUrl() }}"
                                   style="font-size:13px">
                                    &laquo; Prev
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                            @if($page == $orders->currentPage())
                                <li class="page-item active">
                                    <span class="page-link rounded px-3" style="font-size:13px">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link rounded px-3"
                                       href="{{ $url }}"
                                       style="font-size:13px">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($orders->hasMorePages())
                            <li class="page-item">
                                <a class="page-link rounded px-3"
                                   href="{{ $orders->nextPageUrl() }}"
                                   style="font-size:13px">
                                    Next &raquo;
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link rounded px-3" style="font-size:13px">
                                    Next &raquo;
                                </span>
                            </li>
                        @endif

                    </ul>
                </nav>
            </div>

            <div class="text-center text-muted mt-2" style="font-size:13px">
                Showing {{ $orders->firstItem() }} – {{ $orders->lastItem() }}
                of {{ $orders->total() }} orders
            </div>
        @endif

    @endif

</div>
@endsection