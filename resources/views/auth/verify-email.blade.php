@extends('layouts.main')

@section('title', 'Verify your email | PRIME BOOKING')
@section('extra_meta')<meta name="robots" content="noindex, nofollow">@endsection

@section('content')
<div class="py-5 bg-light" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 480px;">
        <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white text-center">
            <div class="mb-3" style="width: 50px; height: 50px; background: #e0edff; color: #2067e1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>

            @if (auth()->user()->hasVerifiedEmail())
                <h4 class="fw-bold text-dark mb-2">Your email is verified</h4>
                <p class="text-secondary small mb-4">{{ auth()->user()->email }} is confirmed. Bookings made with this address now appear in your account.</p>
                <a href="{{ route('home') }}" class="btn btn-primary fw-bold rounded-pill px-4">Continue</a>
            @else
                <h4 class="fw-bold text-dark mb-2">Verify your email address</h4>
                <p class="text-secondary small mb-4">
                    We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.
                    Verifying lets you see and manage bookings made with this email.
                </p>

                @if (session('status'))
                    <div class="alert alert-success rounded-3 small" role="alert">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger rounded-3 small" role="alert">{{ session('error') }}</div>
                @endif

                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">Send the link again</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
