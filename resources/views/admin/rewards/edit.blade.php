@extends('layouts.admin')

@section('title', 'Edit Reward')
@section('page-title', 'Edit Reward')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Reward — {{ $reward->name }}</h4>
    <a href="{{ route('admin.rewards.index') }}" class="btn btn-outline-secondary">
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
        <form action="{{ route('admin.rewards.update', $reward->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Reward Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $reward->name) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $reward->description) }}</textarea>
                </div>
                <div class="col-md-6">
                    @if($reward->image)
                        <img src="{{ asset('storage/'.$reward->image) }}" class="mb-2" style="height:80px; object-fit:cover; border-radius:8px;">
                    @endif
                    <label class="form-label">Replace Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Point Cost <span class="text-danger">*</span></label>
                    <input type="number" name="point_cost" class="form-control" min="1" value="{{ old('point_cost', $reward->point_cost) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Minimum Tier</label>
                    <select name="min_tier_id" class="form-select">
                        <option value="">Any Tier</option>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id }}" {{ $reward->min_tier_id == $t->id ? 'selected' : '' }}>{{ $t->name }}+</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Stock</label>
                    <input type="number" name="total_stock" class="form-control" min="1" value="{{ old('total_stock', $reward->total_stock) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Per-Member Limit</label>
                    <input type="number" name="per_member_limit" class="form-control" min="1" value="{{ old('per_member_limit', $reward->per_member_limit) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until" class="form-control"
                           value="{{ old('valid_until', $reward->valid_until?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ $reward->is_active ? 'checked' : '' }}>
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
