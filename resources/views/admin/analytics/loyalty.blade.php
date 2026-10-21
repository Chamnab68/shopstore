@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark font-weight-bold" style="font-size: 24px;">Loyalty & Retention Analytics</h2>
            <p class="text-muted small mb-0">Real-time insight on your point economy, user distributions, and retention health.</p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="/admin/tiers" class="btn btn-white text-dark shadow-sm border btn-sm px-3 py-2 rounded-lg font-weight-bold" style="background: #fff;">
                <i class="fa-solid fa-layer-group text-primary mr-1"></i> Manage Tiers
            </a>
            <a href="/admin/points-rules" class="btn btn-white text-dark shadow-sm border btn-sm px-3 py-2 rounded-lg font-weight-bold" style="background: #fff;">
                <i class="fa-solid fa-circle-dollar-to-slot text-warning mr-1"></i> Earning Rules
            </a>
            <a href="/admin/rewards" class="btn btn-white text-dark shadow-sm border btn-sm px-3 py-2 rounded-lg font-weight-bold" style="background: #fff;">
                <i class="fa-solid fa-gift text-success mr-1"></i> Rewards Catalog
            </a>
            <a href="/admin/redemptions" class="btn btn-white text-dark shadow-sm border btn-sm px-3 py-2 rounded-lg font-weight-bold" style="background: #fff;">
                <i class="fa-solid fa-clipboard-list text-info mr-1"></i> View Redemptions
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-xl py-3 px-4" style="border-radius: 16px; background: #fff;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="fa-solid fa-coins text-indigo" style="color: #4f46e5; font-size: 18px;"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold" style="font-size: 11px; tracking-wider: 0.05em;">Total Points Liability</span>
                            <h3 class="font-weight-bold text-dark mb-0 mt-1" style="font-size: 26px;">{{ number_format($totalLiability) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-xl py-3 px-4" style="border-radius: 16px; background: #fff;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="background: #fff5f5; width: 48px; height: 48px;">
                            <i class="fa-solid fa-chart-pie text-danger" style="font-size: 18px;"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold" style="font-size: 11px; tracking-wider: 0.05em;">Churn Risk Rate</span>
                            <h3 class="font-weight-bold text-danger mb-0 mt-1" style="font-size: 26px;">{{ $churnRate }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-xl py-3 px-4" style="border-radius: 16px; background: #fff;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center" style="background: #fff7ed; width: 48px; height: 48px;">
                            <i class="fa-solid fa-user-shield text-warning" style="color: #ea580c; font-size: 18px;"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold" style="font-size: 11px; tracking-wider: 0.05em;">Churn Risk Users</span>
                            <h3 class="font-weight-bold text-warning mb-0 mt-1" style="color: #ea580c; font-size: 26px;">{{ $churnCandidates->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 rounded-xl" style="border-radius: 16px; background: #fff; overflow: hidden;">
                <div class="card-header border-b bg-light/50 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <h6 class="font-weight-bold text-dark m-0 uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-id-badge text-primary mr-1"></i> TIER DISTRIBUTION
                    </h6>
                    <span class="badge badge-pill bg-light border text-dark font-weight-bold px-2 py-1" style="font-size: 10px;">Live Levels</span>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted text-uppercase" style="font-size: 11px; border-bottom: 2px solid #f1f5f9;">
                                    <th class="pb-2 font-weight-bold">Membership Tier</th>
                                    <th class="pb-2 text-right font-weight-bold">Total Members</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tierDistribution as $tier)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 text-dark font-weight-bold text-capitalize d-flex align-items-center">
                                            @if(strtolower($tier->current_tier) == 'platinum')
                                                <span class="d-inline-block rounded-circle mr-2" style="width: 8px; height: 8px; background: #a855f7; box-shadow: 0 0 0 4px #f3e8ff;"></span>
                                            @elseif(strtolower($tier->current_tier) == 'gold')
                                                <span class="d-inline-block rounded-circle mr-2" style="width: 8px; height: 8px; background: #eab308; box-shadow: 0 0 0 4px #fef9c3;"></span>
                                            @elseif(strtolower($tier->current_tier) == 'silver')
                                                <span class="d-inline-block rounded-circle mr-2" style="width: 8px; height: 8px; background: #94a3b8; box-shadow: 0 0 0 4px #f1f5f9;"></span>
                                            @else
                                                <span class="d-inline-block rounded-circle mr-2" style="width: 8px; height: 8px; background: #6366f1; box-shadow: 0 0 0 4px #e0e7ff;"></span>
                                            @endif
                                            {{ $tier->current_tier ?? 'Standard' }}
                                        </td>
                                        <td class="py-3 text-dark text-right font-weight-bold">{{ number_format($tier->total) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-muted small">No tier records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 rounded-xl" style="border-radius: 16px; background: #fff; overflow: hidden;">
                <div class="card-header border-b bg-light/50 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <h6 class="font-weight-bold text-dark m-0 uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-user-clock text-danger mr-1"></i> CUSTOMERS AT RISK OF CHURN
                    </h6>
                    <span class="badge badge-pill font-weight-bold px-2 py-1" style="font-size: 10px; background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2;">> 30 Days Idle</span>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted text-uppercase" style="font-size: 11px; border-bottom: 2px solid #f1f5f9;">
                                    <th class="pb-2 font-weight-bold">Customer Info</th>
                                    <th class="pb-2 text-center font-weight-bold">Tier</th>
                                    <th class="pb-2 text-right font-weight-bold">Points Balance</th>
                                    <th class="pb-2 text-right font-weight-bold">Last Interaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($churnCandidates as $candidate)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3">
                                            <div class="text-dark font-weight-bold" style="font-size: 14px;">{{ $candidate->name }}</div>
                                            <div class="text-muted" style="font-size: 12px;">{{ $candidate->email }}</div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge font-weight-bold text-uppercase px-2 py-1 text-secondary" style="background: #f1f5f9; font-size: 11px; border-radius: 6px;">
                                                {{ $candidate->current_tier ?? 'Standard' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right font-weight-bold text-danger" style="font-size: 14px;">
                                            {{ number_format($candidate->loyalty_points) }} <span class="text-muted font-weight-normal" style="font-size: 11px;">pts</span>
                                        </td>
                                        <td class="py-3 text-right text-muted small">
                                            <i class="fa-regular fa-clock mr-1 text-light"></i>{{ $candidate->updated_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-center text-muted">
                                            <div class="d-flex flex-col items-center justify-center p-3">
                                                <i class="fa-solid fa-circle-check text-success mb-2" style="font-size: 24px;"></i>
                                                <p class="font-weight-bold text-secondary mb-0">Excellent! No users are currently in the churn zone.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* បន្ថែមស្ទាយទម្រង់ Card ឱ្យមានកោងមូល និងស្រមោលស្រាលដូចគំរូ */
    .rounded-xl { border-radius: 16px !important; }
    .btn-white { background-color: #ffffff !important; border-color: #e2e8f0 !important; color: #334155 !important; }
    .btn-white:hover { background-color: #f8fafc !important; }
    .table th, .table td { vertical-align: middle !important; border-top: none !important; }
</style>
@endsection