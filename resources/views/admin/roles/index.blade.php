@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Roles & Permissions</h4>
        <p class="text-muted mb-0">Manage admin roles: Super Admin, Marketing Manager, Loyalty Manager</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Role
    </a>
</div>

<div class="row g-4">
    @forelse($roles as $role)
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">{{ $role->display_name }}</h6>
                    @if($role->is_system)
                        <span class="badge bg-secondary">System</span>
                    @endif
                </div>
                <p class="small text-muted">{{ $role->description ?? 'No description' }}</p>
                <div class="mb-3">
                    @foreach($role->permissions->take(5) as $perm)
                        <span class="badge bg-light text-dark border mb-1">{{ $perm->name }}</span>
                    @endforeach
                    @if($role->permissions->count() > 5)
                        <span class="badge bg-light text-muted">+{{ $role->permissions->count() - 5 }} more</span>
                    @endif
                </div>
                @if(!$role->is_system)
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                          onsubmit="return confirm('Delete role?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">No roles configured yet.</div>
    @endforelse
</div>

<div class="mt-3">{{ $roles->links() }}</div>
@endsection
