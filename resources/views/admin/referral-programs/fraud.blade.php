@extends('layouts.admin')

@section('title', 'Fraud Review')
@section('page-title', 'Fraud Detection')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Fraud Review</h4>
        <p class="text-muted mb-0">Referrals flagged for self-referral or duplicate IP patterns</p>
    </div>
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
                        <th>IP Address</th>
                        <th>Fraud Reason</th>
                        <th>Date</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flagged as $r)
                    <tr class="table-danger">
                        <td class="ps-3">{{ $r->referrer->name }}</td>
                        <td>{{ $r->referee->name ?? '—' }}</td>
                        <td><code>{{ $r->ip_address }}</code></td>
                        <td class="small">{{ $r->fraud_reason }}</td>
                        <td class="small">{{ $r->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-3">
                            <form action="{{ route('admin.referral-programs.fraud.confirm', $r->id) }}" method="POST"
                                  onsubmit="return confirm('Confirm as fraud and reject?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-ban me-1"></i>Confirm Fraud
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-check-circle text-success me-2"></i>No fraud cases flagged.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $flagged->links() }}</div>
@endsection
