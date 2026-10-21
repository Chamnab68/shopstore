@extends('layouts.admin')

@section('title', 'Rewards Catalog')
@section('page-title', 'Rewards Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rewards Catalog</h4>
        <p class="text-muted mb-0">Manage redeemable rewards: vouchers, free products, experiences</p>
    </div>
    <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Reward
    </a>
</div>

<div class="row g-4">
    @forelse($rewards as $reward)
    <div class="col-md-3">
        <div class="card h-100">
            <div style="height:140px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                @if($reward->image)
                    <img src="{{ asset('storage/'.$reward->image) }}" class="w-100 h-100" style="object-fit:cover">
                @else
                    <i class="fas fa-gift fa-3x text-muted"></i>
                @endif
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="fw-bold mb-0">{{ $reward->name }}</h6>
                    @if($reward->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Off</span>
                    @endif
                </div>
                <span class="badge bg-light text-dark border mb-2">{{ str_replace('_',' ',$reward->type) }}</span>
                <p class="text-warning fw-bold mb-1"><i class="fas fa-coins me-1"></i>{{ $reward->point_cost }} points</p>
                @if($reward->minTier)
                    <p class="small text-muted mb-1"><i class="fas fa-gem me-1"></i>{{ $reward->minTier->name }}+ only</p>
                @endif
                <p class="small text-muted mb-2">
                    <i class="fas fa-box me-1"></i>
                    {{ $reward->total_stock ? ($reward->total_stock - $reward->redeemed_count) . ' left' : 'Unlimited' }}
                    · {{ $reward->redemptions_count }} redeemed
                </p>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.rewards.edit', $reward->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.rewards.destroy', $reward->id) }}" method="POST" class="flex-fill"
                          onsubmit="return confirm('Delete this reward?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">No rewards in catalog yet.</div>
    @endforelse
</div>

<div class="mt-3">{{ $rewards->links() }}</div>
@endsection
