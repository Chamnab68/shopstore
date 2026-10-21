@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Role — {{ $role->display_name }}</h4>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
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
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $role->display_name) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Permissions</label>
                    @foreach($permissions as $module => $perms)
                    <div class="card mb-2">
                        <div class="card-header bg-light fw-bold small text-uppercase">{{ $module }}</div>
                        <div class="card-body py-2">
                            @foreach($perms as $perm)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                       id="perm_{{ $perm->id }}" {{ in_array($perm->id, $assignedIds) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                    {{ $perm->name }}
                                    @if($perm->isTierGated())
                                        <span class="badge bg-warning text-dark">{{ $perm->min_tier }}+ only</span>
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
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
