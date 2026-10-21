@extends('layouts.admin')

@section('title', 'Badges')
@section('page-title', 'Gamification Badges')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Achievement Badges</h4>
        <p class="text-muted mb-0">Manage gamification badges and trigger conditions</p>
    </div>
    <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Badge
    </a>
</div>

<div class="row g-4">
    @forelse($badges as $badge)
    <div class="col-md-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    @if($badge->icon)
                        <img src="{{ asset('storage/'.$badge->icon) }}" style="width:64px;height:64px;object-fit:contain">
                    @else
                        <i class="fas fa-award fa-3x text-warning"></i>
                    @endif
                </div>
                <h6 class="fw-bold">{{ $badge->name }}</h6>
                <p class="small text-muted">{{ $badge->description }}</p>
                <span class="badge bg-light text-dark border mb-2">
                    {{ str_replace('_',' ',$badge->trigger_type) }}
                    @if($badge->trigger_value) ({{ $badge->trigger_value }}) @endif
                </span>
                <p class="small text-muted"><i class="fas fa-users me-1"></i>{{ $badge->user_badges_count }} earned</p>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.badges.edit', $badge->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="POST" class="flex-fill"
                          onsubmit="return confirm('Delete badge?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">No badges configured yet.</div>
    @endforelse
</div>

<div class="mt-3">{{ $badges->links() }}</div>
@endsection
