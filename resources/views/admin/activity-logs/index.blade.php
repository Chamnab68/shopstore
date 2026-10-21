@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="fw-bold">System Activity Logs</h1>
            <p class="text-muted mb-0">Track and audit all user actions, system events, and point transactions.</p>
        </div>
        <div>
            <a href="{{ route('admin.activity-logs.export', request()->all()) }}" class="btn btn-success shadow-sm">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label fw-semibold small">Filter by User</label>
                    <select name="user_id" id="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label fw-semibold small">Filter by Action</label>
                    <select name="action" id="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $act)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from" class="form-label fw-semibold small">From Date</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label fw-semibold small">To Date</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table Card -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <i class="fas fa-history me-1 text-primary"></i>
            <strong>Audit Trail & Logs</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">ID</th>
                            <th class="py-3">User</th>
                            <th class="py-3">Action</th>
                            <th class="py-3">Subject</th>
                            <th class="py-3">IP Address</th>
                            <th class="py-3">Date & Time</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-3 fw-bold text-secondary">#{{ $log->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $log->user->name ?? 'System / Guest' }}</div>
                                <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ class_basename($log->subject_type ?? '') }} (ID: {{ $log->subject_id ?? 'N/A' }})
                                </span>
                            </td>
                            <td><code class="text-secondary small">{{ $log->ip_address ?? 'N/A' }}</code></td>
                            <td>{{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 text-black-50"></i>
                                <p class="mb-0">No activity logs found matching your criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection