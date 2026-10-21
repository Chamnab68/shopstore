@extends('layouts.admin')

@section('title', 'Points Expiry Policy')
@section('page-title', 'Points Expiry Policy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Points Expiry Policy</h4>
    <a href="{{ route('admin.points-rules.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Set how many months after earning, points will automatically expire.</p>
                <form action="{{ route('admin.points-rules.expiry-policy.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Expiry Period (months) <span class="text-danger">*</span></label>
                        <input type="number" name="expiry_months" class="form-control" min="1" max="60"
                               value="{{ \App\Models\Setting::where('key','points_expiry_months')->value('value') ?? 12 }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Save Policy</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
