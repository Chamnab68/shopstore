@extends('layouts.admin')

@section('title', 'Edit Campaign')
@section('page-title', 'Edit Bonus Campaign')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Campaign — {{ $pointsCampaign->name }}</h4>
    <a href="{{ route('admin.points-campaigns.index') }}" class="btn btn-outline-secondary">
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
        <form action="{{ route('admin.points-campaigns.update', $pointsCampaign->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $pointsCampaign->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Multiplier <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="multiplier" class="form-control" min="1.1"
                           value="{{ old('multiplier', $pointsCampaign->multiplier) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Starts At <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                           value="{{ old('starts_at', $pointsCampaign->starts_at->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ends At <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="ends_at" class="form-control"
                           value="{{ old('ends_at', $pointsCampaign->ends_at->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ $pointsCampaign->is_active ? 'checked' : '' }}>
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
