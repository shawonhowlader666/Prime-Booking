@extends('layouts.main')

@section('title', 'Sign In — Prime Booking')
@section('meta_description', 'Sign in to your Prime Booking account to manage bookings, access deals, and earn loyalty rewards.')

@section('content')
<section class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%); padding: 60px 0;">

    {{-- Decorative circles --}}
    <div style="position:fixed;top:-100px;right:-100px;width:400px;height:400px;border-radius:50%;background:rgba(32,103,225,0.08);pointer-events:none;"></div>
    <div style="position:fixed;bottom:-80px;left:-80px;width:300px;height:300px;border-radius:50%;background:rgba(99,102,241,0.08);pointer-events:none;"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

                {{-- Card --}}
                <div class="rounded-4 p-4 p-md-5" style="background:rgba(255,255,255,0.98);box-shadow:0 24px 80px rgba(0,0,0,0.35);">

                    {{-- Logo & Title --}}
                    <div class="text-center mb-4">
                        <x-logo height="40" />
                        <h1 class="fw-bold mt-3 mb-1 text-dark" style="font-size:22px;letter-spacing:-0.3px;">Welcome back</h1>
                        <p class="text-secondary mb-0" style="font-size:13px;">Sign in to your account to continue</p>
                    </div>

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 mb-3 py-2" style="font-size:13px;">
                            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 mb-3 py-2" style="font-size:13px;">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    {{-- Social OAuth Buttons --}}
                    <div class="d-flex flex-column" style="gap:10px;margin-bottom:20px;">

                        <a href="{{ route('auth.social.redirect', 'google') }}"
                           class="btn fw-semibold d-flex align-items-center justify-content-center gap-2 text-white"
                           style="background:#2067e1;border-radius:24px;height:46px;font-size:14px;text-decoration:none;border:none;transition:all .2s;">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:24px;height:24px;">
                                <svg width="15" height="15" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                            </div>
                            Continue with Google
                        </a>

                        <a href="{{ route('auth.social.redirect', 'facebook') }}"
                           class="btn fw-semibold d-flex align-items-center justify-content-center gap-2"
                           style="background:#fff;border:1.5px solid #cbd5e1;border-radius:24px;height:46px;font-size:14px;color:#1877f2;text-decoration:none;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:24px;height:24px;background:#1877f2;font-size:12px;">
                                <i class="fa-brands fa-facebook-f"></i>
                            </div>
                            Continue with Facebook
                        </a>

                    </div>

                    {{-- Divider --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
                        <span class="text-secondary px-2" style="font-size:12px;">or sign in with email</span>
                        <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
                    </div>

                    {{-- Email + Password Form --}}
                    <form action="{{ route('login.post') }}" method="POST" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="login-email" class="form-label fw-semibold text-dark" style="font-size:13px;">Email address</label>
                            <input type="email" name="email" id="login-email"
                                   class="form-control rounded-3 @error('email') is-invalid @enderror"
                                   style="height:46px;font-size:14px;border-color:#cbd5e1;"
                                   value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="login-password" class="form-label fw-semibold text-dark mb-0" style="font-size:13px;">Password</label>
                                <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size:12px;color:#2067e1;">Forgot password?</a>
                            </div>
                            <div class="position-relative">
                                <input type="password" name="password" id="login-password"
                                       class="form-control rounded-3 @error('password') is-invalid @enderror"
                                       style="height:46px;font-size:14px;border-color:#cbd5e1;padding-right:44px;"
                                       placeholder="••••••••" autocomplete="current-password" required>
                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 p-1 text-secondary shadow-none"
                                        onclick="togglePassword('login-password', this)" style="font-size:14px;">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback" style="font-size:12px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Remember + Submit --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-secondary" for="remember-me" style="font-size:13px;">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" id="btn-login-submit"
                                class="btn text-white w-100 fw-bold"
                                style="background:#2067e1;border-radius:24px;height:48px;font-size:15px;border:none;transition:all .2s;">
                            Sign In
                        </button>
                    </form>

                    {{-- Register Link --}}
                    <div class="text-center mt-4" style="font-size:13.5px;">
                        <span class="text-secondary">Don't have an account?</span>
                        <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color:#2067e1;">Create one free</a>
                    </div>

                    {{-- Legal --}}
                    <div class="text-center mt-3" style="font-size:11px;color:#94a3b8;line-height:1.5;">
                        By signing in, I agree to
                        <a href="{{ route('terms') }}" class="text-decoration-none fw-semibold" style="color:#2067e1;">Terms of Use</a>
                        and
                        <a href="{{ route('privacy') }}" class="text-decoration-none fw-semibold" style="color:#2067e1;">Privacy Policy</a>.
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-regular fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-regular fa-eye';
    }
}
</script>
@endpush
