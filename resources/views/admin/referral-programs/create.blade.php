@extends('layouts.admin')

@section('title', 'New Referral Program')
@section('page-title', 'Create Referral Program')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create Referral Program</h4>
    <a href="{{ route('admin.referral-programs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.referral-programs.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Program Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Referrer Reward (points) <span class="text-danger">*</span></label>
                    <input type="number" name="referrer_reward_points" class="form-control" min="0"
                           value="{{ old('referrer_reward_points', 500) }}" required>
                    <small class="text-muted">Points the existing customer earns</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Referee Reward (points) <span class="text-danger">*</span></label>
                    <input type="number" name="referee_reward_points" class="form-control" min="0"
                           value="{{ old('referee_reward_points', 200) }}" required>
                    <small class="text-muted">Points the new member earns</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Commission % (cash, optional)</label>
                    <input type="number" step="0.01" name="commission_percent" class="form-control" min="0" max="100"
                           value="{{ old('commission_percent', 0) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Max Levels <span class="text-danger">*</span></label>
                    <select name="max_levels" class="form-select" required>
                        <option value="1">1 — Direct referral only</option>
                        <option value="2">2 — Include sub-referrals</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Level-2 Commission %</label>
                    <input type="number" step="0.01" name="level2_commission_percent" class="form-control" min="0" max="100"
                           value="{{ old('level2_commission_percent', 0) }}">
                    <small class="text-muted">% of level-1 reward passed to sub-referrer</small>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="requires_first_purchase" value="1" checked>
                        <label class="form-check-label">Require first purchase before reward</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Create Program</button>
            </div>
        </form>
    </div>
</div>
@endsection
