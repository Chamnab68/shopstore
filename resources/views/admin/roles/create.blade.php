@extends('layouts.admin')

@section('title', 'New Role')
@section('page-title', 'Create Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create New Role</h4>
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
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Role Key <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. marketing_manager" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="display_name" class="form-control" placeholder="e.g. Marketing Manager" value="{{ old('display_name') }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="mb-4">
    <label class="form-label fw-bold d-block mb-3">Assign Permissions</label>
    <div class="row">
        @forelse($permissions as $permission)
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="form-check card p-2 border-light shadow-sm">
                    <input class="form-check-input ms-0 me-2" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}">
                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                        <strong>{{ $permission->name }}</strong>
                        @if($permission->description)
                            <small class="text-muted d-block" style="font-size:11px">{{ $permission->description }}</small>
                        @endif
                    </label>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> No permissions found in database. Please seed your permissions first.</p>
            </div>
        @endforelse
    </div>
</div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Create Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
