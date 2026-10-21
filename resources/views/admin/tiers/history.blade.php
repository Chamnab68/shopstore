@extends('layouts.admin')

@section('title', 'Tier History')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Tier History — {{ $user->name }}</h2>
        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to User
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>From Tier</th>
                            <th>To Tier</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Changed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $entry)
                        <tr>
                            <td class="ps-3 small">{{ $entry->created_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $entry->fromTier->name ?? '—' }}</td>
                            <td><strong>{{ $entry->toTier->name }}</strong></td>
                            <td>
                                @php
                                    $typeColors = ['upgrade' => 'success', 'downgrade' => 'danger', 'manual_override' => 'warning'];
                                @endphp
                                <span class="badge bg-{{ $typeColors[$entry->change_type] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $entry->change_type)) }}
                                </span>
                            </td>
                            <td class="small">{{ $entry->reason ?? '—' }}</td>
                            <td class="small">{{ $entry->changedBy->name ?? 'System (auto)' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No tier changes recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $history->links() }}
    </div>

</div>
@endsection