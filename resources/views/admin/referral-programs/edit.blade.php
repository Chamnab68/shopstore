@extends('layouts.admin')

@section('title', 'Edit Referral Program')
@section('page-title', 'Edit Referral Program')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit — {{ $referralProgram->name }}</h4>
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
        <form action="{{ route('admin.referral-programs.update', $referralProgram->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Referrer Reward (points) <span class="text-danger">*</span></label>
                    <input type="number" name="referrer_reward_points" class="form-control" min="0"
                           value="{{ old('referrer_reward_points', $referralProgram->referrer_reward_points) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Referee Reward (points) <span class="text-danger">*</span></label>
                    <input type="number" name="referee_reward_points" class="form-control" min="0"
                           value="{{ old('referee_reward_points', $referralProgram->referee_reward_points) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Commission %</label>
                    <input type="number" step="0.01" name="commission_percent" class="form-control" min="0" max="100"
                           value="{{ old('commission_percent', $referralProgram->commission_percent) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Level-2 Commission %</label>
                    <input type="number" step="0.01" name="level2_commission_percent" class="form-control" min="0" max="100"
                           value="{{ old('level2_commission_percent', $referralProgram->level2_commission_percent) }}">
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ $referralProgram->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
