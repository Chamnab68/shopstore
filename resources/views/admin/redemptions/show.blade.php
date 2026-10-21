@extends('layouts.admin')

@section('title', 'Redemption Detail')
@section('page-title', 'Redemption Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Redemption #{{ $redemption->redemption_code }}</h4>
    <a href="{{ route('admin.redemptions.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th width="200">Redemption Code</th><td><code>{{ $redemption->redemption_code }}</code></td></tr>
                    <tr><th>Customer</th><td>{{ $redemption->user->name }} ({{ $redemption->user->email }})</td></tr>
                    <tr><th>Reward</th><td>{{ $redemption->reward->name }}</td></tr>
                    <tr><th>Points Spent</th><td><span class="text-warning fw-bold">{{ $redemption->points_spent }}</span></td></tr>
                    <tr><th>Status</th><td>
                        @php $colors = ['pending'=>'warning text-dark','fulfilled'=>'success','expired'=>'secondary','cancelled'=>'danger']; @endphp
                        <span class="badge bg-{{ $colors[$redemption->status] ?? 'secondary' }}">{{ ucfirst($redemption->status) }}</span>
                    </td></tr>
                    <tr><th>Redeemed At</th><td>{{ $redemption->created_at->format('d M Y, h:i A') }}</td></tr>
                    @if($redemption->fulfilled_at)
                    <tr><th>Fulfilled At</th><td>{{ $redemption->fulfilled_at->format('d M Y, h:i A') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Actions</h6>

                @if($redemption->status === 'pending')
                <form action="{{ route('admin.redemptions.fulfill', $redemption->id) }}" method="POST" class="mb-2">
                    @csrf @method('PATCH')
                    <button class="btn btn-success w-100"><i class="fas fa-check me-1"></i>Mark Fulfilled</button>
                </form>

                <form action="{{ route('admin.redemptions.cancel', $redemption->id) }}" method="POST"
                      onsubmit="return confirm('Cancel and refund points?')">
                    @csrf @method('PATCH')
                    <div class="mb-2">
                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="Cancellation reason" required>
                    </div>
                    <button class="btn btn-outline-danger w-100"><i class="fas fa-times me-1"></i>Cancel & Refund</button>
                </form>
                @else
                <p class="text-muted small">No further actions available for this status.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
