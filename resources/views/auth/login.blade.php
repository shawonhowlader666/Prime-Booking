@extends('layouts.main')

@section('title', 'Sign In — Prime Booking')
@section('meta_description', 'Sign in to your Prime Booking account to manage bookings, access deals, and earn loyalty rewards.')

@push('styles')
<style>
/* ───────────── AURORA BACKGROUND ───────────── */
.pb-login-bg {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 16px;
    background: #080d1a;
    overflow: hidden;
}

/* Animated radial aurora blobs */
.pb-login-bg::before,
.pb-login-bg::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(90px);
    pointer-events: none;
    animation: pbBlobFloat 8s ease-in-out infinite alternate;
}
.pb-login-bg::before {
    width: 560px; height: 560px;
    background: radial-gradient(circle, #2067e1 0%, #6366f1 60%, transparent 80%);
    top: -160px; right: -120px;
    opacity: 0.5;
}
.pb-login-bg::after {
    width: 420px; height: 420px;
    background: radial-gradient(circle, #0ea5e9 0%, #8b5cf6 70%, transparent 90%);
    bottom: -110px; left: -100px;
    opacity: 0.45;
    animation-delay: -4s;
}
.pb-blob-mid {
    position: absolute;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, #f43f5e 0%, transparent 70%);
    filter: blur(80px);
    opacity: 0.2;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
    animation: pbBlobFloat 10s ease-in-out infinite alternate-reverse;
}

@keyframes pbBlobFloat {
    0%   { transform: translateY(0)    scale(1);    }
    50%  { transform: translateY(28px) scale(1.07); }
    100% { transform: translateY(-22px) scale(0.95); }
}

/* ───────────── SHIMMER GLASS CARD ───────────── */
.pb-shimmer-card {
    position: relative;
    background: rgba(255,255,255,0.065);
    border: 1px solid rgba(255,255,255,0.13);
    border-radius: 24px;
    padding: 44px 40px 36px;
    backdrop-filter: blur(28px) saturate(160%);
    -webkit-backdrop-filter: blur(28px) saturate(160%);
    box-shadow:
        0 0 0 1px rgba(255,255,255,0.07) inset,
        0 32px 80px rgba(0,0,0,0.6),
        0 4px 20px rgba(32,103,225,0.2);
    overflow: hidden;
    animation: pbCardIn 0.6s cubic-bezier(0.16,1,0.3,1) both;
}

@keyframes pbCardIn {
    from { opacity:0; transform: translateY(30px) scale(0.97); }
    to   { opacity:1; transform: translateY(0)    scale(1);    }
}

/*
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 *  SHIMMER EFFECT  (professional name: "Aurora Shimmer")
 *  Technique: CSS linear-gradient sweep via @keyframes
 *  Direction: right → left  (background-position 220% → -20%)
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */
.pb-shimmer-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        120deg,
        transparent   0%,
        transparent  30%,
        rgba(255,255,255,0.16) 48%,
        rgba(255,255,255,0.20) 50%,
        rgba(255,255,255,0.16) 52%,
        transparent  70%,
        transparent 100%
    );
    background-size: 240% 100%;
    background-position: 240% 0;
    border-radius: inherit;
    pointer-events: none;
    animation: pbShimmerSweep 3.6s ease-in-out infinite;
    z-index: 0;
}

@keyframes pbShimmerSweep {
    0%   { background-position: 240%  0; }
    60%  { background-position: -40%  0; }
    100% { background-position: -40%  0; }
}

/* Lift all card content above the shimmer layer */
.pb-shimmer-card > * { position: relative; z-index: 1; }

/* ───────────── INPUTS ───────────── */
.pb-input {
    background: rgba(255,255,255,0.08) !important;
    border: 1.5px solid rgba(255,255,255,0.13) !important;
    border-radius: 12px !important;
    color: #ffffff !important;
    font-size: 14px;
    height: 48px;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.pb-input::placeholder { color: rgba(255,255,255,0.35) !important; }
.pb-input:focus {
    background: rgba(255,255,255,0.12) !important;
    border-color: #2067e1 !important;
    box-shadow: 0 0 0 3px rgba(32,103,225,0.28) !important;
    outline: none !important;
}
.pb-input.is-invalid {
    border-color: #f43f5e !important;
    box-shadow: 0 0 0 3px rgba(244,63,94,0.22) !important;
}
.pb-input-wrap { position: relative; }
.pb-input-wrap .pb-icon {
    position: absolute;
    left: 13px; top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.4);
    font-size: 13px;
    pointer-events: none;
    z-index: 2;
}
.pb-input-wrap .pb-input { padding-left: 38px !important; }

/* Eye toggle */
.pb-eye {
    position: absolute;
    right: 12px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: rgba(255,255,255,0.38);
    font-size: 14px; cursor: pointer;
    transition: color .15s;
    z-index: 3;
}
.pb-eye:hover { color: rgba(255,255,255,0.85); }

/* ───────────── LABELS ───────────── */
.pb-label {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.6);
    margin-bottom: 7px;
    display: block;
    letter-spacing: 0.35px;
}

/* ───────────── SUBMIT BTN ───────────── */
.pb-submit {
    height: 50px;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.4px;
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #2067e1 0%, #6366f1 100%);
    box-shadow: 0 6px 22px rgba(32,103,225,0.42);
    transition: transform .18s, box-shadow .18s, filter .18s;
    cursor: pointer;
}
.pb-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(32,103,225,0.52);
    filter: brightness(1.1);
}
.pb-submit:active { transform: translateY(0); }

/* ───────────── SOCIAL BTNS ───────────── */
.pb-social {
    height: 46px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    transition: transform .18s, box-shadow .18s, filter .18s;
    border: 1.5px solid rgba(255,255,255,0.13);
}
.pb-social:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(0,0,0,0.35);
    filter: brightness(1.07);
}
.pb-google  { background: rgba(255,255,255,0.09);  color: #ffffff !important; }
.pb-fb      { background: rgba(24,119,242,0.18);   color: #60a5fa !important; border-color: rgba(24,119,242,0.35); }

/* ───────────── DIVIDER ───────────── */
.pb-divider { display:flex; align-items:center; gap:12px; margin:20px 0; }
.pb-divider hr { flex:1; margin:0; border-color:rgba(255,255,255,0.12); }
.pb-divider span { font-size:11.5px; color:rgba(255,255,255,0.35); white-space:nowrap; }

/* ───────────── ALERTS ───────────── */
.pb-alert {
    border-radius: 10px;
    font-size: 13px;
    padding: 10px 14px;
    margin-bottom: 18px;
}
.pb-alert-err { background:rgba(244,63,94,0.14);  border:1px solid rgba(244,63,94,0.35);  color:#fca5a5; }
.pb-alert-ok  { background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3);  color:#6ee7b7; }

/* ───────────── REMEMBER CHECK ───────────── */
.form-check-input:checked { background-color:#2067e1; border-color:#2067e1; }

/* ───────────── FLOATING PARTICLES ───────────── */
.pb-particles { position:absolute; inset:0; pointer-events:none; overflow:hidden; }
.pb-dot {
    position: absolute;
    border-radius: 50%;
    animation: pbRise linear infinite;
}
@keyframes pbRise {
    0%   { transform: translateY(0)   rotate(0deg);   opacity:0; }
    8%   { opacity:1; }
    92%  { opacity:1; }
    100% { transform: translateY(-105vh) rotate(700deg); opacity:0; }
}

@media (max-width:575px) {
    .pb-shimmer-card { padding:32px 22px 28px; }
}
</style>
@endpush

@section('content')
<section class="pb-login-bg">

    {{-- Aurora mid glow --}}
    <div class="pb-blob-mid"></div>

    {{-- Ambient particles (JS spawned) --}}
    <div class="pb-particles" id="pbDots"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

                {{--
                    ╔══════════════════════════════════════════════════╗
                    ║  AURORA SHIMMER CARD                             ║
                    ║  The light that sweeps right→left is called:     ║
                    ║  "Shimmer Effect" / "Glimmer Sweep Animation"    ║
                    ║  CSS: @keyframes pbShimmerSweep                  ║
                    ║  Used by: Apple, Stripe, Linear, Vercel          ║
                    ╚══════════════════════════════════════════════════╝
                --}}
                <div class="pb-shimmer-card">

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <x-logo height="40" />
                        <h1 class="fw-bold mt-3 mb-1 text-white" style="font-size:22px;letter-spacing:-0.4px;">Welcome back</h1>
                        <p style="font-size:13px;color:rgba(255,255,255,0.48);">Sign in to your account to continue</p>
                    </div>

                    {{-- Flash --}}
                    @if(session('success'))
                    <div class="pb-alert pb-alert-ok"><i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}</div>
                    @endif
                    @if(session('error') || $errors->any())
                    <div class="pb-alert pb-alert-err"><i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') ?: $errors->first() }}</div>
                    @endif

                    {{-- Social --}}
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('auth.social.redirect', 'google') }}" class="pb-social pb-google">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:22px;height:22px;">
                                <svg width="14" height="14" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84-.63.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                            </div>
                            Continue with Google
                        </a>
                        <a href="{{ route('auth.social.redirect', 'facebook') }}" class="pb-social pb-fb">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:22px;height:22px;background:#1877f2;font-size:11px;">
                                <i class="fa-brands fa-facebook-f"></i>
                            </div>
                            Continue with Facebook
                        </a>
                    </div>

                    {{-- Divider --}}
                    <div class="pb-divider"><hr><span>or sign in with email</span><hr></div>

                    {{-- Form --}}
                    <form action="{{ route('login.post') }}" method="POST" novalidate id="pbLoginForm">
                        @csrf

                        <div class="mb-3">
                            <label class="pb-label">Email or Phone Number</label>
                            <div class="pb-input-wrap">
                                <i class="fa-solid fa-envelope pb-icon"></i>
                                <input type="text" name="login_credential" id="login-email"
                                       class="form-control pb-input @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="you@example.com or 017…"
                                       autocomplete="email" required>
                            </div>
                            @error('email')<div class="mt-1" style="font-size:11.5px;color:#fca5a5;">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="pb-label mb-0">Password</label>
                                <a href="{{ route('password.request') }}" style="font-size:11.5px;color:#60a5fa;text-decoration:none;font-weight:600;">Forgot password?</a>
                            </div>
                            <div class="pb-input-wrap position-relative">
                                <i class="fa-solid fa-lock pb-icon"></i>
                                <input type="password" name="password" id="login-password"
                                       class="form-control pb-input @error('password') is-invalid @enderror"
                                       style="padding-right:44px !important;"
                                       placeholder="••••••••"
                                       autocomplete="current-password" required>
                                <button type="button" class="pb-eye" onclick="pbTogglePass()" title="Toggle password">
                                    <i class="fa-regular fa-eye" id="pbEyeIcon"></i>
                                </button>
                            </div>
                            @error('password')<div class="mt-1" style="font-size:11.5px;color:#fca5a5;">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember-me" style="font-size:13px;color:rgba(255,255,255,0.52);">Remember me for 30 days</label>
                        </div>

                        <button type="submit" id="pbSubmitBtn" class="pb-submit w-100">
                            <span id="pbBtnText">Sign In &nbsp;<i class="fa-solid fa-arrow-right"></i></span>
                            <span id="pbBtnSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing in…
                            </span>
                        </button>
                    </form>

                    <div class="text-center mt-4" style="font-size:13px;color:rgba(255,255,255,0.42);">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="fw-bold ms-1" style="color:#60a5fa;text-decoration:none;">Create one free →</a>
                    </div>

                    <div class="text-center mt-3" style="font-size:11px;color:rgba(255,255,255,0.22);line-height:1.55;">
                        By signing in you agree to our
                        <a href="{{ route('terms') }}" style="color:rgba(255,255,255,0.42);text-decoration:none;font-weight:600;">Terms of Use</a>
                        and
                        <a href="{{ route('privacy') }}" style="color:rgba(255,255,255,0.42);text-decoration:none;font-weight:600;">Privacy Policy</a>.
                    </div>

                </div>{{-- /pb-shimmer-card --}}

            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
/* ── Password visibility toggle ── */
function pbTogglePass() {
    const inp  = document.getElementById('login-password');
    const icon = document.getElementById('pbEyeIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    icon.className = inp.type === 'text' ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
}

/* ── Submit loading state ── */
document.getElementById('pbLoginForm').addEventListener('submit', function() {
    document.getElementById('pbBtnText').classList.add('d-none');
    document.getElementById('pbBtnSpinner').classList.remove('d-none');
    document.getElementById('pbSubmitBtn').disabled = true;
});

/* ────────────────────────────────────────────────────────────
   AMBIENT PARTICLE SYSTEM
   Professional term: "Floating Bubble Particles" or
   "Bokeh Background Effect" — common in Stripe, Linear, Clerk
   Tiny translucent orbs that rise from the bottom continuously
──────────────────────────────────────────────────────────── */
(function pbParticleSystem() {
    const wrap   = document.getElementById('pbDots');
    if (!wrap) return;

    const SIZES  = [4, 6, 8, 10, 14, 18, 24];
    const SPEEDS = [12, 16, 20, 24, 28, 34, 40];
    const COLORS = [
        'rgba(32,103,225,0.18)',
        'rgba(99,102,241,0.14)',
        'rgba(14,165,233,0.12)',
        'rgba(255,255,255,0.06)',
        'rgba(244,63,94,0.09)',
        'rgba(139,92,246,0.12)',
    ];

    function spawn() {
        const el    = document.createElement('div');
        el.className = 'pb-dot';
        const sz    = SIZES[Math.random() * SIZES.length | 0];
        const spd   = SPEEDS[Math.random() * SPEEDS.length | 0];
        const color = COLORS[Math.random() * COLORS.length | 0];
        const left  = (Math.random() * 100).toFixed(2);
        const delay = -(Math.random() * spd).toFixed(2);

        el.style.cssText =
            `width:${sz}px;height:${sz}px;` +
            `left:${left}%;bottom:-${sz + 10}px;` +
            `background:${color};` +
            `animation-duration:${spd}s;` +
            `animation-delay:${delay}s;`;

        wrap.appendChild(el);
        setTimeout(() => el.remove(), (spd + Math.abs(delay) + 1) * 1000);
    }

    /* Seed initial batch so screen isn't empty */
    for (let i = 0; i < 24; i++) spawn();
    /* Keep spawning fresh particles every 1.5 s */
    setInterval(spawn, 1500);
}());
</script>
@endpush
