@extends('layouts.main')

@section('title', 'Set New Password | PRIME BOOKING')

@section('content')
<div class="py-5 bg-light" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 460px;">
        <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div style="width: 50px; height: 50px; background: #e0edff; color: #2067e1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 20px;" class="mb-3">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Set New Password</h4>
                <p class="text-secondary small mb-0">Enter your new secure password below to regain account access.</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger rounded-3 small mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger rounded-3 small mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="reset-email" class="form-label fw-semibold text-dark small">Email Address</label>
                    <input type="email" name="email" id="reset-email" class="form-control rounded-3" value="{{ old('email', $email) }}" required readonly style="height: 46px; background-color: #f8fafc;">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold text-dark small">New Password</label>
                    <input type="password" name="password" id="password" class="form-control rounded-3" placeholder="At least 8 characters" required minlength="8" style="height: 46px;">
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold text-dark small">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded-3" placeholder="Re-enter password" required minlength="8" style="height: 46px;">
                </div>

                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 py-2.5 shadow-sm" style="background-color: #2067e1; font-size: 14.5px;">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
