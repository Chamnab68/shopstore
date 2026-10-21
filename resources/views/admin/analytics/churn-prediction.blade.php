@extends('layouts.admin')

@section('title', 'Churn Prediction')
@section('page-title', 'Churn Prediction Score')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Churn Prediction Score</h4>
        <p class="text-muted mb-0">AI-driven engagement scoring — lower score = higher churn risk</p>
    </div>
</div>

{{-- Risk Summary --}}
@php
    $high   = $users->where('risk_level', 'High')->count();
    $medium = $users->where('risk_level', 'Medium')->count();
    $low    = $users->where('risk_level', 'Low')->count();
    $total  = $users->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-danger text-center py-3">
            <div class="fw-bold fs-2 text-danger">{{ $high }}</div>
            <div class="text-muted small">High Risk (Score &lt; 30)</div>
            <small class="text-muted">Needs immediate re-engagement</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning text-center py-3">
            <div class="fw-bold fs-2 text-warning">{{ $medium }}</div>
            <div class="text-muted small">Medium Risk (Score 30–60)</div>
            <small class="text-muted">Watch closely</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success text-center py-3">
            <div class="fw-bold fs-2 text-success">{{ $low }}</div>
            <div class="text-muted small">Low Risk (Score &gt; 60)</div>
            <small class="text-muted">Engaged & healthy</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Members Sorted by Churn Risk (Highest First)</span>
            <span class="badge bg-secondary">{{ $total }} members</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Member</th>
                        <th>Tier</th>
                        <th>Churn Score</th>
                        <th>Risk Level</th>
                        <th>Days Since Last Order</th>
                        <th>Total Orders</th>
                        <th>Points Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $row)
                    @php
                        $riskColor = match($row['risk_level']) {
                            'High'   => 'danger',
                            'Medium' => 'warning',
                            default  => 'success',
                        };
                    @endphp
                    <tr class="{{ $row['risk_level'] === 'High' ? 'table-danger' : '' }}">
                        <td class="ps-3">
                            <div class="fw-bold">{{ $row['user']->name }}</div>
                            <div class="text-muted small">{{ $row['user']->email }}</div>
                        </td>
                        <td>{{ $row['user']->tier->name ?? '—' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-fill" style="height:8px;min-width:80px">
                                    <div class="progress-bar bg-{{ $riskColor }}" style="width:{{ $row['churn_score'] }}%"></div>
                                </div>
                                <span class="fw-bold small">{{ $row['churn_score'] }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $riskColor }}">{{ $row['risk_level'] }}</span>
                        </td>
                        <td class="text-center">
                            @if($row['days_since_last_order'] >= 999)
                                <span class="text-muted">Never ordered</span>
                            @else
                                <span class="{{ $row['days_since_last_order'] > 60 ? 'text-danger fw-bold' : '' }}">
                                    {{ $row['days_since_last_order'] }} days
                                </span>
                            @endif
                        </td>
                        <td class="text-center">{{ $row['user']->orders_count }}</td>
                        <td class="text-center">{{ number_format($row['user']->points_balance) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No customer data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-info mt-4 small">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Score formula:</strong> Starts at 100 · Recency penalty (last order > 30/60/90 days) ·
    Frequency bonus (10+ orders) · Points activity bonus (active in last 30 days).
    Score range: 0 (highest risk) → 100 (most engaged).
</div>
@endsection
