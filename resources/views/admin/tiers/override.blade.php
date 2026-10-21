@extends('layouts.admin')

@section('title', 'Manual Tier Override')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Manual Tier Override — {{ $user->name }}</h2>
        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to User
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <div class="alert alert-info">
                        <strong>Current Tier:</strong>
                        {{ $user->tier->name ?? 'No Tier Assigned' }}
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.tier.override', $user->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">New Tier <span class="text-danger">*</span></label>
                            <select name="tier_id" class="form-select" required>
                                <option value="">-- Select Tier --</option>
                                @foreach($tiers as $tier)
                                    <option value="{{ $tier->id }}" {{ $user->tier_id == $tier->id ? 'selected' : '' }}>
                                        {{ $tier->name }} (Level {{ $tier->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason for Override <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3"
                                      placeholder="Required for audit trail — e.g. 'Customer service goodwill gesture'"
                                      required></textarea>
                        </div>

                        <div class="alert alert-warning small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            This action will be logged in the Activity Log with your admin ID and the reason provided.
                        </div>

                        <button type="submit" class="btn btn-warning px-4">
                            <i class="fas fa-exchange-alt me-1"></i>Override Tier
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection