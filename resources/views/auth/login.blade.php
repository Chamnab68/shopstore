{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title','Login - ShopStore')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div style="font-size:36px">🛒</div>
                        <h3 class="fw-bold">Welcome Back</h3>
                        <p class="text-muted">Sign in to your ShopStore account</p>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                    </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember" style="font-size:13px">Remember me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3">Sign In</button>
                    </form>

                    <div class="text-center">
                        <span class="text-muted" style="font-size:13px">Don't have an account?</span>
                        <a href="{{ route('register') }}" class="fw-bold ms-1">Create account</a>
                    </div>

                    <hr class="my-4">
                    <div class="alert alert-light border" style="font-size:12px">
                        <strong>Demo accounts:</strong><br>
                        Admin: admin@shopstore.com / 123456<br>
                        Customer: customer@shopstore.com / password
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
