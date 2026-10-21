@extends('layouts.admin')

@section('title', 'Bonus Campaigns')
@section('page-title', 'Points Bonus Campaigns')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Bonus Point Campaigns</h4>
        <p class="text-muted mb-0">Time-limited multiplier campaigns (e.g. "3x Points Weekend")</p>
    </div>
    <a href="{{ route('admin.points-campaigns.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Campaign
    </a>
</div>

<div class="row g-4">
    @forelse($campaigns as $c)
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">{{ $c->name }}</h6>
                    @if($c->isLive())
                        <span class="badge bg-success">Live</span>
                    @elseif($c->is_active)
                        <span class="badge bg-warning text-dark">Scheduled</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
                <p class="text-muted small">{{ $c->description ?? 'No description' }}</p>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary fs-6">{{ $c->multiplier }}x Points</span>
                </div>
                <ul class="list-unstyled small text-muted mb-3">
                    <li><i class="fas fa-calendar me-1"></i>{{ $c->starts_at->format('d M Y') }} → {{ $c->ends_at->format('d M Y') }}</li>
                    @if($c->category)<li><i class="fas fa-tag me-1"></i>{{ $c->category->name }}</li>@endif
                    @if($c->product)<li><i class="fas fa-box me-1"></i>{{ $c->product->name }}</li>@endif
                    @if($c->tier)<li><i class="fas fa-gem me-1"></i>{{ $c->tier->name }} tier only</li>@endif
                </ul>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.points-campaigns.edit', $c->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.points-campaigns.toggle', $c->id) }}" method="POST" class="flex-fill">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-outline-warning w-100">
                            <i class="fas fa-power-off"></i> Toggle
                        </button>
                    </form>
                    <form action="{{ route('admin.points-campaigns.destroy', $c->id) }}" method="POST"
                          onsubmit="return confirm('Delete campaign?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">No campaigns yet.</div>
    @endforelse
</div>

<div class="mt-3">{{ $campaigns->links() }}</div>
@endsection
