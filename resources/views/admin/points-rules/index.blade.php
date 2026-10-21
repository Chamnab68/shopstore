@extends('layouts.admin')

@section('title', 'Points Rules')
@section('page-title', 'Loyalty Points Rules')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Points Earning Rules</h4>
        <p class="text-muted mb-0">Configure how customers earn loyalty points</p>
    </div>
    <a href="{{ route('admin.points-rules.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Rule
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Name</th>
                        <th>Type</th>
                        <th>Points/$</th>
                        <th>Multiplier</th>
                        <th>Active Days</th>
                        <th>Period</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    <tr>
                        <td class="ps-3 fw-bold">{{ $rule->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ str_replace('_',' ',$rule->type) }}</span></td>
                        <td>{{ $rule->points_per_dollar }}</td>
                        <td>{{ $rule->multiplier }}x</td>
                        <td class="small">{{ $rule->active_days ? implode(', ', $rule->active_days) : '—' }}</td>
                        <td class="small">
                            @if($rule->starts_at)
                                {{ $rule->starts_at->format('d M') }} - {{ $rule->ends_at?->format('d M Y') ?? '∞' }}
                            @else
                                Always
                            @endif
                        </td>
                        <td>{{ $rule->priority }}</td>
                        <td>
                            @if($rule->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.points-rules.edit', $rule->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.points-rules.destroy', $rule->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this rule?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No points rules configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $rules->links() }}
</div>

<div class="card mt-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-1"><i class="fas fa-hourglass-half me-2 text-warning"></i>Points Expiry Policy</h6>
            <p class="text-muted small mb-0">Set how long earned points remain valid before they expire.</p>
        </div>
        <a href="{{ route('admin.points-rules.expiry-policy') }}" class="btn btn-outline-warning">
            <i class="fas fa-cog me-1"></i>Configure
        </a>
    </div>
</div>
@endsection
