@extends('layouts.admin')

@section('title', 'Redemptions')
@section('page-title', 'Reward Redemptions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Redemption History</h4>
        <p class="text-muted mb-0">Track and process customer reward redemptions</p>
    </div>
</div>

<div class="card mb-3">

    
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="fulfilled" {{ request('status')=='fulfilled'?'selected':'' }}>Fulfilled</option>
                    <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Code</th>
                        <th>Customer</th>
                        <th>Reward</th>
                        <th>Points Spent</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redemptions as $r)
                    <tr>
                        <td class="ps-3"><code>{{ $r->redemption_code }}</code></td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->reward->name }}</td>
                        <td><span class="text-warning fw-bold">{{ $r->points_spent }}</span></td>
                        <td>
                            @php
                                $colors = ['pending'=>'warning text-dark','fulfilled'=>'success','expired'=>'secondary','cancelled'=>'danger'];
                            @endphp
                            <span class="badge bg-{{ $colors[$r->status] ?? 'secondary' }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td class="small">{{ $r->created_at ? $r->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.redemptions.show', $r->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($r->status === 'pending')
                            <form action="{{ route('admin.redemptions.fulfill', $r->id) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No redemptions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $redemptions->links() }}</div>
@endsection
