@extends('layouts.admin')

@section('title', 'Campaign ROI')
@section('page-title', 'Campaign ROI Analysis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Campaign ROI — {{ $campaign->name }}</h4>
        <p class="text-muted mb-0">
            {{ $campaign->starts_at->format('d M Y') }} → {{ $campaign->ends_at->format('d M Y') }}
            · <span class="badge bg-primary">{{ $campaign->multiplier }}x Multiplier</span>
        </p>
    </div>
    <a href="{{ route('admin.analytics.points-liability') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Analytics
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-success">${{ number_format($revenue, 2) }}</div>
            <div class="text-muted small">Revenue During Campaign</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-primary">{{ number_format($orderCount) }}</div>
            <div class="text-muted small">Orders Placed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-warning">{{ number_format($pointsAwarded) }}</div>
            <div class="text-muted small">Points Awarded</div>
            <div style="font-size:11px" class="text-muted">~${{ number_format($pointsAwarded * 0.01, 2) }} liability</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 {{ $roi >= 0 ? 'text-success' : 'text-danger' }}">{{ $roi }}%</div>
            <div class="text-muted small">Estimated ROI</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">ROI Breakdown</h6>
        <table class="table table-borderless mb-0">
            <tr>
                <td>Revenue during campaign</td>
                <td class="text-end text-success fw-bold">${{ number_format($revenue, 2) }}</td>
            </tr>
            <tr>
                <td>Est. points cost (points × $0.01)</td>
                <td class="text-end text-danger">-${{ number_format($pointsAwarded * 0.01, 2) }}</td>
            </tr>
            <tr class="border-top">
                <td class="fw-bold">Net Benefit</td>
                @php $net = $revenue - ($pointsAwarded * 0.01); @endphp
                <td class="text-end fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($net, 2) }}</td>
            </tr>
            <tr>
                <td>ROI</td>
                <td class="text-end fw-bold {{ $roi >= 0 ? 'text-success' : 'text-danger' }}">{{ $roi }}%</td>
            </tr>
        </table>
    </div>
</div>

<div class="alert alert-info mt-3 small">
    <i class="fas fa-info-circle me-2"></i>
    ROI is estimated based on a redemption value of $0.01 per point. Actual cost depends on which rewards are redeemed.
    Avg order during campaign: <strong>${{ $orderCount > 0 ? number_format($revenue / $orderCount, 2) : '—' }}</strong>
</div>
@endsection
