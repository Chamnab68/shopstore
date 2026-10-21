@extends('layouts.admin')

@section('title', 'New Campaign')
@section('page-title', 'Create Bonus Campaign')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create Bonus Campaign</h4>
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
        <form action="{{ route('admin.points-campaigns.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Black Friday 3x Points"
                           value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Multiplier <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="multiplier" class="form-control" min="1.1"
                           value="{{ old('multiplier', 2.0) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scope: Category (optional)</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scope: Product (optional)</label>
                    <select name="product_id" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scope: Tier (optional)</label>
                    <select name="tier_id" class="form-select">
                        <option value="">All Tiers</option>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Starts At <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ends At <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-rocket me-1"></i>Launch Campaign</button>
            </div>
        </form>
    </div>
</div>
@endsection
