@extends('layouts.admin')

@section('title', 'Create Tier')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Create New Tier</h2>
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

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.tiers.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. Silver, Gold, Platinum"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Level (ordering) <span class="text-danger">*</span></label>
                        <input type="number" name="level" class="form-control"
                               placeholder="1 = lowest, higher = better"
                               value="{{ old('level') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Min Spend ($) <span class="text-danger">*</span></label>
                        <input type="number" name="min_spend" class="form-control"
                               value="{{ old('min_spend', 0) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Min Points <span class="text-danger">*</span></label>
                        <input type="number" name="min_points" class="form-control"
                               value="{{ old('min_points', 0) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Point Earn Multiplier <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" name="point_earn_multiplier" class="form-control"
                               value="{{ old('point_earn_multiplier', 1.0) }}" required>
                        <small class="text-muted">e.g. 1.5 = 50% bonus points</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Badge Color</label>
                        <input type="color" name="badge_color" class="form-control form-control-color"
                               value="{{ old('badge_color', '#C0C0C0') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">Benefits</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="free_shipping" value="1" id="freeShip">
                            <label class="form-check-label" for="freeShip">Free Shipping</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="priority_support" value="1" id="prioritySupport">
                            <label class="form-check-label" for="prioritySupport">Priority Support</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="exclusive_access" value="1" id="exclusiveAccess">
                            <label class="form-check-label" for="exclusiveAccess">Exclusive Access</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>Create Tier
                    </button>
                    <a href="{{ route('admin.tiers.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection