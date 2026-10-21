@extends('layouts.admin')

@section('title', 'New Badge')
@section('page-title', 'Create Badge')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create New Badge</h4>
    <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary">
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
        <form action="{{ route('admin.badges.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Badge Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Icon Image</label>
                    <input type="file" name="icon" class="form-control" accept="image/*">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trigger Type <span class="text-danger">*</span></label>
                    <select name="trigger_type" class="form-select" required id="triggerType">
                        <option value="first_purchase">First Purchase</option>
                        <option value="order_milestone">Order Count Milestone</option>
                        <option value="referral_milestone">Referral Milestone</option>
                        <option value="login_streak">Login Streak (days)</option>
                        <option value="points_milestone">Points Milestone</option>
                        <option value="manual">Manual Award Only</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trigger Value</label>
                    <input type="number" name="trigger_value" class="form-control" placeholder="e.g. 10 for '10 orders'" value="{{ old('trigger_value') }}">
                    <small class="text-muted">Not needed for First Purchase / Manual</small>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Create Badge</button>
            </div>
        </form>
    </div>
</div>
@endsection
