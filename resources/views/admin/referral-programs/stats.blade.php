@extends('layouts.admin')

@section('title', 'Referral Stats')
@section('page-title', 'Referral Statistics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Referral Statistics</h4>
    <a href="{{ route('admin.referral-programs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-primary">{{ $stats['total_referrals'] }}</div>
            <div class="text-muted small">Total</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-secondary">{{ $stats['pending'] }}</div>
            <div class="text-muted small">Pending</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-info">{{ $stats['registered'] }}</div>
            <div class="text-muted small">Registered</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-success">{{ $stats['converted'] }}</div>
            <div class="text-muted small">Converted</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-warning">{{ $stats['conversion_rate'] }}%</div>
            <div class="text-muted small">Conv. Rate</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-danger">{{ $stats['flagged_fraud'] }}</div>
            <div class="text-muted small">Fraud Flags</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold">Top 10 Referrers</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Referrer</th>
                        <th>Conversions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topReferrers as $i => $ref)
                    <tr>
                        <td class="ps-3">{{ $i + 1 }}</td>
                        <td>{{ $ref->referrer->name ?? '—' }}</td>
                        <td><span class="badge bg-success">{{ $ref->total }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">No conversions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
