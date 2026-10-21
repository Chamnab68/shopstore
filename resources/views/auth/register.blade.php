@extends('layouts.app')
@section('title','Register - ShopStore')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div style="font-size:36px">🛒</div>
                        <h3 class="fw-bold">Create Account</h3>
                        <p class="text-muted">Join ShopStore today</p>
                    </div>
                    @if($errors->any())
                    <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                    @endif
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Optional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3">Create Account</button>
                    </form>
                    <div class="text-center">
                        <span class="text-muted" style="font-size:13px">Already have an account?</span>
                        <a href="{{ route('login') }}" class="fw-bold ms-1">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
