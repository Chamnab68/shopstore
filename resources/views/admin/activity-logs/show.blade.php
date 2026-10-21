@extends('layouts.admin')

@section('title', 'Log Detail')
@section('page-title', 'Activity Log Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Log Entry #{{ $activityLog->id }}</h4>
    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th width="160">User</th><td>{{ $activityLog->user->name ?? 'System' }}</td></tr>
                    <tr><th>Action</th><td><span class="badge bg-light text-dark border">{{ $activityLog->action }}</span></td></tr>
                    <tr><th>Subject</th><td>
                        @if($activityLog->subject_type)
                            {{ class_basename($activityLog->subject_type) }} #{{ $activityLog->subject_id }}
                        @else — @endif
                    </td></tr>
                    <tr><th>IP Address</th><td>{{ $activityLog->ip_address }}</td></tr>
                    <tr><th>User Agent</th><td class="small">{{ $activityLog->user_agent }}</td></tr>
                    <tr><th>Date</th><td>{{ $activityLog->created_at->format('d M Y, h:i:s A') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if($activityLog->old_values)
        <div class="card mb-3">
            <div class="card-header bg-light fw-bold">Old Values</div>
            <div class="card-body">
                <pre class="mb-0 small">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        @if($activityLog->new_values)
        <div class="card mb-3">
            <div class="card-header bg-light fw-bold">New Values</div>
            <div class="card-body">
                <pre class="mb-0 small">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        @if($activityLog->metadata)
        <div class="card">
            <div class="card-header bg-light fw-bold">Metadata</div>
            <div class="card-body">
                <pre class="mb-0 small">{{ json_encode($activityLog->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
