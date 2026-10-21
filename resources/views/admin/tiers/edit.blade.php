@extends('layouts.admin')

@section('title', 'Edit Tier')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Edit Tier — {{ $tier->name }}</h2>
        <a href="{{ route('admin.tiers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Tier Details --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Tier Details</div>
                <div class="card-body">
                    <form action="{{ route('admin.tiers.update', $tier->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Tier Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', $tier->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Min Spend ($) <span class="text-danger">*</span></label>
                                <input type="number" name="min_spend" class="form-control"
                                       value="{{ old('min_spend', $tier->min_spend) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Min Points <span class="text-danger">*</span></label>
                                <input type="number" name="min_points" class="form-control"
                                       value="{{ old('min_points', $tier->min_points) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Point Earn Multiplier <span class="text-danger">*</span></label>
                                <input type="number" step="0.1" name="point_earn_multiplier" class="form-control"
                                       value="{{ old('point_earn_multiplier', $tier->point_earn_multiplier) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Badge Color</label>
                                <input type="color" name="badge_color" class="form-control form-control-color"
                                       value="{{ old('badge_color', $tier->badge_color) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label d-block">Benefits</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="free_shipping" value="1"
                                           id="freeShip" {{ $tier->free_shipping ? 'checked' : '' }}>
                                    <label class="form-check-label" for="freeShip">Free Shipping</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="priority_support" value="1"
                                           id="prioritySupport" {{ $tier->priority_support ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prioritySupport">Priority Support</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="exclusive_access" value="1"
                                           id="exclusiveAccess" {{ $tier->exclusive_access ? 'checked' : '' }}>
                                    <label class="form-check-label" for="exclusiveAccess">Exclusive Access</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="isActive" {{ $tier->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tier Benefits List --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Custom Benefits</div>
                <div class="card-body">

                    @forelse($benefits as $benefit)
                    <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                        <div>
                            <strong>{{ $benefit->benefit_key }}</strong>: {{ $benefit->benefit_value }}
                            @if($benefit->description)
                                <div class="text-muted small">{{ $benefit->description }}</div>
                            @endif
                        </div>
                        <form action="{{ route('admin.tiers.benefits.remove', [$tier->id, $benefit->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <p class="text-muted small">No custom benefits added yet.</p>
                    @endforelse

                    <form action="{{ route('admin.tiers.benefits.add', $tier->id) }}" method="POST" class="mt-3 pt-3 border-top">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="benefit_key" class="form-control form-control-sm"
                                   placeholder="e.g. birthday_bonus" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="benefit_value" class="form-control form-control-sm"
                                   placeholder="e.g. 500 points" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="description" class="form-control form-control-sm"
                                   placeholder="Description (optional)">
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-plus me-1"></i>Add Benefit
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection