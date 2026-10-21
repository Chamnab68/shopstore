{{-- customers.blade.php --}}
@extends('layouts.admin')
@section('title','Customer Report')
@section('page-title','👥 Customer Report')
@section('content')
<div class="mb-4"><a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>All Reports</a></div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Customer</th><th>Phone</th><th>Orders</th><th>Lifetime Value</th><th>Joined</th></tr></thead>
                <tbody>
                @forelse($data as $user)
                <tr>
                    <td><div class="fw-bold">{{ $user->name }}</div><div class="text-muted" style="font-size:12px">{{ $user->email }}</div></td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td><span class="badge bg-primary">{{ $user->order_count }}</span></td>
                    <td><strong class="text-success">${{ number_format($user->lifetime_value,2) }}</strong></td>
                    <td style="font-size:12px">{{ $user->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No customers yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $data->links() }}
    </div>
</div>
@endsection
