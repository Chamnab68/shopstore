@extends('layouts.admin')

@section('title', 'New Reward')
@section('page-title', 'Create Reward')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create New Reward</h4>
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
        <form action="{{ route('admin.rewards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Reward Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="voucher">Voucher</option>
                        <option value="free_product">Free Product</option>
                        <option value="experience">Experience</option>
                        <option value="discount">Discount</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Linked Product (for Free Product type)</label>
                    <select name="product_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Point Cost <span class="text-danger">*</span></label>
                    <input type="number" name="point_cost" class="form-control" min="1" value="{{ old('point_cost') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minimum Tier</label>
                    <select name="min_tier_id" class="form-select">
                        <option value="">Any Tier</option>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}+</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Stock</label>
                    <input type="number" name="total_stock" class="form-control" min="1" placeholder="Blank = unlimited" value="{{ old('total_stock') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Per-Member Limit</label>
                    <input type="number" name="per_member_limit" class="form-control" min="1" placeholder="Blank = no limit" value="{{ old('per_member_limit') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valid From</label>
                    <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Create Reward</button>
            </div>
        </form>
    </div>
</div>
@endsection
