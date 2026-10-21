@extends('layouts.admin')

@section('title', 'Referral Programs')
@section('page-title', 'Referral & Affiliate Programs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Referral Programs</h4>
        <p class="text-muted mb-0">Configure commission rules and multi-level referral rewards</p>
    </div>
    <a href="{{ route('admin.referral-programs.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Program
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-primary">{{ $stats['total_referrals'] }}</div>
            <div class="text-muted small">Total Referrals</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-success">{{ $stats['converted'] }}</div>
            <div class="text-muted small">Converted</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-warning">{{ $stats['conversion_rate'] }}%</div>
            <div class="text-muted small">Conversion Rate</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-3 text-danger">{{ $stats['flagged_fraud'] }}</div>
            <div class="text-muted small">Flagged Fraud</div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('admin.referral-programs.stats') }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-chart-bar me-1"></i>Full Stats
    </a>
    <a href="{{ route('admin.referral-programs.conversions') }}" class="btn btn-outline-success btn-sm">
        <i class="fas fa-check-circle me-1"></i>Conversions
    </a>
    <a href="{{ route('admin.referral-programs.fraud') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa-exclamation-triangle me-1"></i>Fraud Review
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Name</th>
                        <th>Referrer Reward</th>
                        <th>Referee Reward</th>
                        <th>Levels</th>
                        <th>Referrals</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $p)
                    <tr>
                        <td class="ps-3 fw-bold">{{ $p->name }}</td>
                        <td>{{ $p->referrer_reward_points }} pts</td>
                        <td>{{ $p->referee_reward_points }} pts</td>
                        <td>{{ $p->max_levels }}</td>
                        <td>{{ $p->referrals_count }}</td>
                        <td>
                            @if($p->is_active)<span class="badge bg-success">Active</span>
                            @else<span class="badge bg-secondary">Inactive</span>@endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.referral-programs.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.referral-programs.destroy', $p->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete program?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No referral programs configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $programs->links() }}</div>
@endsection
