{{-- customers.blade.php --}}
@extends('layouts.admin')
@section('title','Customer Report')
@section('page-title','👥 Customer Report')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>All Reports
    </a>
</div>
@include('admin.components.date-filter')

{{-- បង្ហាញផ្ទាំង Success Alert ពេលកែប្រែជោគជ័យ --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Lifetime Value</th>
                        <th>Current Points</th>
                        <th>Tier</th>
                        <th>Joined</th>
                        {{-- <th class="text-center">Action</th> 🛠️ ថែមក្បាលតារាង Action --}}
                    </tr>
                </thead>
                <tbody>
                @forelse($data as $user)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $user->name }}</div>
                        <div class="text-muted" style="font-size:12px">{{ $user->email }}</div>
                    </td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td><span class="badge bg-primary">{{ $user->order_count }}</span></td>
                    <td><strong class="text-success">${{ number_format($user->lifetime_value, 2) }}</strong></td>
                    
                    {{-- បង្ហាញចំនួន Points របស់ Customer --}}
                    <td>
                        <strong class="text-warning">
                            <i class="fas fa-coins me-1"></i>{{ number_format($user->points_balance ?? 0) }}
                        </strong>
                    </td>
                    
                    {{-- បង្ហាញកម្រិត Tier --}}
                    <td>
                        @if($user->tier)
                            @if(strtolower($user->tier->name) === 'gold')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-gem me-1"></i>Gold
                                </span>
                            @elseif(strtolower($user->tier->name) === 'silver')
                                <span class="badge bg-secondary text-white">
                                    <i class="fas fa-gem me-1"></i>Silver
                                </span>
                            @else
                                <span class="badge bg-info text-dark">
                                    <i class="fas fa-gem me-1"></i>{{ $user->tier->name }}
                                </span>
                            @endif
                        @else
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-gem me-1"></i>Normal
                            </span>
                        @endif
                    </td>

                    <td style="font-size:12px">{{ $user->created_at->format('d M Y') }}</td>
                    
                    {{-- 🛠️ ប៊ូតុងសកម្មភាពសម្រាប់កំណត់ Role (Action Column) --}}
                    <td class="text-center">
                        {{-- <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#roleModal-{{ $user->id }}">
                            <i class="fas fa-user-shield me-1"></i> Role
                        </button> --}}

                        <div class="modal fade" id="roleModal-{{ $user->id }}" tabindex="-1" aria-labelledby="roleModalLabel-{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-start">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title" id="roleModalLabel-{{ $user->id }}">
                                            <i class="fas fa-user-edit me-1 text-primary"></i> Assign Role to {{ $user->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('admin/users/'.$user->id.'/assign-role') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Select User Role</label>
                                                <select name="role_id" class="form-select" required>
                                                    <option value="">-- Choose Role --</option>
                                                    {{-- លោតបញ្ជីឈ្មោះ Role ទាំងអស់ចេញពី Database មកឱ្យរើស --}}
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}" {{ (optional($user->roles)->contains($role->id) ?? false) ? 'selected' : '' }}>
                                                            {{ $role->display_name }} ({{ $role->name }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text text-muted" style="font-size:11px">
                                                    *Note: Changing the role will immediately affect the user's access control permissions.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-save me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No customers yet.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $data->links() }}
    </div>
</div>
@endsection