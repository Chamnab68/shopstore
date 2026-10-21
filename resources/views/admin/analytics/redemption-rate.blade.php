@extends('layouts.admin')

@section('title', 'Redemption Rate')
@section('page-title', 'Redemption Rate Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Redemption Rate</h4>
        <p class="text-muted mb-0">% of earned points that have been redeemed</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-success">{{ number_format($totalEarned) }}</div>
            <div class="text-muted small">Total Points Earned</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-warning">{{ number_format($totalBurned) }}</div>
            <div class="text-muted small">Total Points Redeemed</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-2 text-primary">{{ $rate }}%</div>
            <div class="text-muted small">Redemption Rate</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Overall Redemption Progress</h6>
        <div class="progress" style="height:20px">
            <div class="progress-bar bg-primary" style="width:{{ $rate }}%">
                {{ $rate }}%
            </div>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <small class="text-muted">0%</small>
            <small class="text-muted">100%</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold">Top Redeemed Rewards</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Reward</th>
                        <th>Type</th>
                        <th>Point Cost</th>
                        <th>Times Redeemed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topRewards as $i => $row)
                    <tr>
                        <td class="ps-3">{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $row->reward->name ?? '—' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $row->reward->type ?? '—' }}</span></td>
                        <td><span class="text-warning fw-bold">{{ $row->reward->point_cost ?? '—' }} pts</span></td>
                        <td>
                            <span class="badge bg-primary">{{ $row->total }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No redemptions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
