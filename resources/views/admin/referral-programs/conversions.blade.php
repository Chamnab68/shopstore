@extends('layouts.admin')

@section('title', 'Referral Conversions')
@section('page-title', 'Referral Conversions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Converted Referrals</h4>
    <a href="{{ route('admin.referral-programs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Referrer</th>
                        <th>Referee</th>
                        <th>Program</th>
                        <th>Registered</th>
                        <th>Converted</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $r)
                    <tr>
                        <td class="ps-3">{{ $r->referrer->name }}</td>
                        <td>{{ $r->referee->name ?? '—' }}</td>
                        <td>{{ $r->program->name }}</td>
                        <td class="small">{{ $r->registered_at?->format('d M Y') ?? '—' }}</td>
                        <td class="small">{{ $r->converted_at?->format('d M Y') ?? '—' }}</td>
                        <td><span class="badge bg-success">Converted</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No conversions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $referrals->links() }}</div>
@endsection
