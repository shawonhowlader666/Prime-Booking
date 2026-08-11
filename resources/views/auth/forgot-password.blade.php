@extends('layouts.main')

@section('title', 'Forgot Password | PRIME BOOKING')

@section('content')
<div class="py-5 bg-light" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 460px;">
        <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div style="width: 50px; height: 50px; background: #e0edff; color: #2067e1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 20px;" class="mb-3">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Reset Password</h4>
                <p class="text-secondary small mb-0">Enter your registered email address to receive password reset instructions.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success rounded-3 small mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger rounded-3 small mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reset-email" class="form-label fw-semibold text-dark small">Email Address</label>
                    <input type="email" name="email" id="reset-email" class="form-control rounded-3" placeholder="name@example.com" value="{{ old('email') }}" required style="height: 46px;">
                </div>

                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 py-2.5 shadow-sm" style="background-color: #2067e1; font-size: 14.5px;">
                    Send Reset Link
                </button>

                <div class="text-center mt-4 pt-3 border-top">
                    <a href="{{ route('signin') }}" class="text-decoration-none fw-semibold small" style="color: #2067e1;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Sign In
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

