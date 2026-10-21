@extends('layouts.admin')

@section('title', 'Edit Badge')
@section('page-title', 'Edit Badge')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Badge — {{ $badge->name }}</h4>
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
        <form action="{{ route('admin.badges.update', $badge->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Badge Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $badge->name) }}" required>
                </div>
                <div class="col-md-6">
                    @if($badge->icon)
                        <img src="{{ asset('storage/'.$badge->icon) }}" style="height:50px" class="mb-2">
                    @endif
                    <label class="form-label">Replace Icon</label>
                    <input type="file" name="icon" class="form-control" accept="image/*">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $badge->description) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trigger Value</label>
                    <input type="number" name="trigger_value" class="form-control" value="{{ old('trigger_value', $badge->trigger_value) }}">
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ $badge->is_active ? 'checked' : '' }}>
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
