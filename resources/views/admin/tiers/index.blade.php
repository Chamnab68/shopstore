@extends('layouts.admin')

@section('title', 'Tier Management')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tier & Membership Management</h2>
            <p class="text-muted mb-0">Silver / Gold / Platinum tier configuration</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.tiers.quarterly-review') }}" method="POST"
                  onsubmit="return confirm('Run quarterly tier review for all members now?')">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-sync me-1"></i>Run Quarterly Review
                </button>
            </form>
            <a href="{{ route('admin.tiers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>New Tier
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($tiers as $tier)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                     style="background-color: {{ $tier->badge_color }}">
                    <span class="fw-bold">{{ $tier->name }}</span>
                    <span class="badge bg-white text-dark">Level {{ $tier->level }}</span>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-2">
                            <i class="fas fa-dollar-sign text-muted me-2"></i>
                            Min Spend: <strong>${{ number_format($tier->min_spend) }}</strong>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-coins text-muted me-2"></i>
                            Min Points: <strong>{{ number_format($tier->min_points) }}</strong>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-star text-muted me-2"></i>
                            Earn Multiplier: <strong>{{ $tier->point_earn_multiplier }}x</strong>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-truck text-muted me-2"></i>
                            Free Shipping:
                            @if($tier->free_shipping)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-headset text-muted me-2"></i>
                            Priority Support:
                            @if($tier->priority_support)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-gem text-muted me-2"></i>
                            Exclusive Access:
                            @if($tier->exclusive_access)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </li>
                    </ul>

                   <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route('admin.tiers.members', $tier->id) }}" class="text-decoration-none text-muted small">
        <i class="fas fa-users me-1"></i><strong>{{ $tier->users_count ?? 0 }}</strong> members
    </a>
</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.tiers.edit', $tier->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.tiers.destroy', $tier->id) }}" method="POST"
                                  onsubmit="return confirm('Delete tier {{ $tier->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <p class="text-muted">No tiers configured yet.</p>
                <a href="{{ route('admin.tiers.create') }}" class="btn btn-primary">Create First Tier</a>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection