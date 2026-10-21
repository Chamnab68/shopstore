@extends('layouts.admin')

@section('title', 'Edit Points Rule')
@section('page-title', 'Edit Points Rule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Rule — {{ $pointsRule->name }}</h4>
    <a href="{{ route('admin.points-rules.index') }}" class="btn btn-outline-secondary">
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
        <form action="{{ route('admin.points-rules.update', $pointsRule->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $pointsRule->name) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Points per $1 <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="points_per_dollar" class="form-control"
                           value="{{ old('points_per_dollar', $pointsRule->points_per_dollar) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Multiplier <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="multiplier" class="form-control"
                           value="{{ old('multiplier', $pointsRule->multiplier) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minimum Order Amount</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-control"
                           value="{{ old('min_order_amount', $pointsRule->min_order_amount) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Priority <span class="text-danger">*</span></label>
                    <input type="number" name="priority" class="form-control" value="{{ old('priority', $pointsRule->priority) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ $pointsRule->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Starts At</label>
                    <input type="date" name="starts_at" class="form-control"
                           value="{{ old('starts_at', $pointsRule->starts_at?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ends At</label>
                    <input type="date" name="ends_at" class="form-control"
                           value="{{ old('ends_at', $pointsRule->ends_at?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label d-block">Active Days</label>
                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="active_days[]" value="{{ $day }}" id="d_{{ $day }}"
                               {{ in_array($day, $pointsRule->active_days ?? []) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="d_{{ $day }}">{{ ucfirst(substr($day,0,3)) }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
