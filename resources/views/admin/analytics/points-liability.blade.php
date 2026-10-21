@extends('layouts.admin')

@section('title', 'Points Liability')
@section('page-title', 'Points Liability Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Points Liability Report</h4>
        <p class="text-muted mb-0">Outstanding points = future redemption cost for the business</p>
    </div>
    <a href="{{ route('admin.analytics.export', ['type'=>'points_liability']) }}" class="btn btn-outline-success">
        <i class="fas fa-file-csv me-1"></i>Export CSV
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-primary">{{ number_format($totalOutstanding) }}</div>
            <div class="text-muted small">Total Outstanding Points</div>
            <div class="text-muted" style="font-size:11px">~${{ number_format($totalOutstanding * 0.01, 2) }} liability</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-warning">{{ number_format($expiringNext30Days) }}</div>
            <div class="text-muted small">Expiring in 30 Days</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-info">{{ $byTier->count() }}</div>
            <div class="text-muted small">Active Tiers</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold">Breakdown by Tier</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Tier</th>
                        <th>Members</th>
                        <th>Total Points</th>
                        <th>Est. Liability</th>
                        <th>Avg per Member</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byTier as $row)
                    <tr>
                        <td class="ps-3 fw-bold">{{ $row->name }}</td>
                        <td>{{ $row->member_count }}</td>
                        <td>{{ number_format($row->total_points) }}</td>
                        <td class="text-danger">${{ number_format($row->total_points * 0.01, 2) }}</td>
                        <td>{{ $row->member_count > 0 ? number_format($row->total_points / $row->member_count) : 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
