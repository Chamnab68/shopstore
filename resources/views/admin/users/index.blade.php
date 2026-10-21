@extends('layouts.admin')
@section('title', 'User & Role Management')
@section('page-title', '👥 User & Role Management')

@section('content')
<div class="row g-4">
    <!-- Form បង្កើត Account ថ្មី -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3 text-dark">Create New User</h5>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Enter name">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="******">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Assign Role</label>
                    <select name="role_id" class="form-select">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="isAdminCheck">
                    <label class="form-check-label small fw-bold text-danger" for="isAdminCheck">Grant Administrator Access</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-user-plus me-1"></i> Create Account
                </button>
            </form>
        </div>
    </div>

    <!-- តារាងបង្ហាញបញ្ជី Users និងកែប្រែ Role -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name / Email</th>
                                <th>Current Role / Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">Customer</span>
                                    @endif
                                    @if($user->roleModel)
                                        <span class="badge bg-info text-dark">{{ $user->roleModel->name }}</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Form កែប្រែ Role ឆាប់រហ័ស -->
                                    <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role_id" class="form-select form-select-sm" style="width: 130px;">
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Update Role">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection