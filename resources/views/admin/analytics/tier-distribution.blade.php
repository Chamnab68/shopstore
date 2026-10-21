@extends('layouts.admin')

@section('title', 'Tier Distribution')
@section('page-title', 'Tier Distribution Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tier Distribution</h4>
        <p class="text-muted mb-0">Member count and percentage across Silver / Gold / Platinum</p>
    </div>
    <a href="{{ route('admin.analytics.export', ['type'=>'tier_distribution']) }}" class="btn btn-outline-success">
        <i class="fas fa-file-csv me-1"></i>Export CSV
    </a>
</div>

<div class="row g-4 mb-4">
    @foreach($distribution as $item)
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="fw-bold fs-2" style="color:{{ $item['tier']->badge_color }}">
                {{ number_format($item['count']) }}
            </div>
            <div class="fw-bold">{{ $item['tier']->name }}</div>
            <div class="text-muted small">{{ $item['percentage'] }}% of all members</div>
            <div class="px-4 mt-2">
                <div class="progress" style="height:8px">
                    <div class="progress-bar" style="width:{{ $item['percentage'] }}%; background:{{ $item['tier']->badge_color }}"></div>
                </div>
            </div>
            <div class="text-muted mt-2" style="font-size:11px">
                Min Spend: ${{ number_format($item['tier']->min_spend) }} ·
                Earn: {{ $item['tier']->point_earn_multiplier }}x
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header bg-white fw-bold">Tier Summary Table</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Tier</th>
                        <th>Level</th>
                        <th>Members</th>
                        <th>Share</th>
                        <th>Earn Multiplier</th>
                        <th>Free Shipping</th>
                        <th>Exclusive Access</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distribution as $item)
                    <tr>
                        <td class="ps-3 fw-bold" style="color:{{ $item['tier']->badge_color }}">
                            {{ $item['tier']->name }}
                        </td>
                        <td>{{ $item['tier']->level }}</td>
                        <td>{{ number_format($item['count']) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-fill" style="height:6px">
                                    <div class="progress-bar" style="width:{{ $item['percentage'] }}%; background:{{ $item['tier']->badge_color }}"></div>
                                </div>
                                <span class="small">{{ $item['percentage'] }}%</span>
                            </div>
                        </td>
                        <td>{{ $item['tier']->point_earn_multiplier }}x</td>
                        <td>
                            @if($item['tier']->free_shipping)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($item['tier']->exclusive_access)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
