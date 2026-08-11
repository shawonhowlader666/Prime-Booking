{{-- ═══════════════════════════════════════════════════════════════
     Prime Booking — Sign In / Create Account Modal
     Uses: Laravel Socialite OAuth redirect for Google & Facebook
     ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="agodaAuthModal" tabindex="-1" aria-labelledby="authModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background: #fff;">

            {{-- Close --}}
            <div class="modal-header border-0 pb-0 position-relative" style="min-height: 40px;">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pt-0 pb-4">

                {{-- Brand Logo --}}
                <div class="text-center mb-3">
                    <x-logo height="36" />
                </div>

                {{-- ── AUTHENTICATED VIEW ─────────────────────────────────────── --}}
                @auth
                <div class="mb-3">
                    {{-- User Card --}}
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="border-color: #e2e8f0; background: #f8fafc;">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}"
                                 class="rounded-circle flex-shrink-0"
                                 style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #2067e1;">
                        @else
                            <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #2067e1, #6366f1); font-size: 20px;">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 15px;">{{ auth()->user()->name }}</div>
                            <div class="text-secondary text-truncate" style="font-size: 12.5px;">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>

                {{-- Continue Button --}}
                <a href="{{ route('account.bookings') }}"
                   class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2 mb-3 text-white"
                   style="background: #2067e1; border-radius: 24px; height: 46px; font-size: 14.5px; text-decoration: none;">
                    <i class="fa-solid fa-circle-check"></i> Continue to My Account
                </a>

                {{-- Sign Out --}}
                <div class="text-center">
                    <form action="{{ route('auth.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <span class="text-secondary small">Not {{ auth()->user()->name }}?</span>
                        <button type="submit" class="btn btn-link text-decoration-none p-0 ms-1 small fw-bold shadow-none" style="color: #2067e1;">
                            Sign out
                        </button>
                    </form>
                </div>

                {{-- ── GUEST VIEW ──────────────────────────────────────────────── --}}
                @else

                {{-- Title --}}
                <h5 class="fw-bold text-dark mb-1 text-center" id="authModalTitle" style="font-size: 18px; letter-spacing: -0.2px;">
                    Sign in or create an account
                </h5>
                <p class="text-secondary text-center mb-4" style="font-size: 12.5px;">
                    Sign up for free or log in to access amazing deals!
                </p>

                {{-- ── Social OAuth Buttons ─── --}}
                <div class="d-flex flex-column" style="gap: 10px;">

                    {{-- Google — real OAuth redirect --}}
                    <a href="{{ route('auth.social.redirect', 'google') }}"
                       id="btn-signin-google"
                       class="btn text-white w-100 fw-semibold d-flex align-items-center justify-content-center gap-2"
                       style="background: #2067e1; border-radius: 24px; height: 46px; font-size: 14.5px; text-decoration: none; border: none;">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 26px; height: 26px;">
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                        </div>
                        <span>Sign in with Google</span>
                    </a>

                    {{-- Facebook — real OAuth redirect --}}
                    <a href="{{ route('auth.social.redirect', 'facebook') }}"
                       id="btn-signin-facebook"
                       class="btn w-100 fw-semibold d-flex align-items-center justify-content-center gap-2"
                       style="background: #fff; border: 1.5px solid #cbd5e1; border-radius: 24px; height: 46px; font-size: 14.5px; color: #1877f2; text-decoration: none;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width: 26px; height: 26px; background: #1877f2; font-size: 13px;">
                            <i class="fa-brands fa-facebook-f"></i>
                        </div>
                        <span>Sign in with Facebook</span>
                    </a>

                    {{-- Apple — coming soon toast --}}
                    <button type="button"
                            id="btn-signin-apple"
                            class="btn text-white w-100 fw-semibold d-flex align-items-center justify-content-center gap-2"
                            style="background: #1c1e21; border-radius: 24px; height: 46px; font-size: 14.5px; border: none; position: relative;"
                            onclick="showAppleComingSoon()">
                        <i class="fa-brands fa-apple" style="font-size: 18px;"></i>
                        <span>Sign in with Apple</span>
                        <span class="badge bg-secondary ms-1" style="font-size: 9px; font-weight: 500;">Soon</span>
                    </button>

                </div>

                {{-- Divider --}}
                <div class="d-flex align-items-center gap-2 my-3">
                    <hr class="flex-grow-1 m-0" style="border-color: #e2e8f0;">
                    <span class="text-secondary" style="font-size: 12px;">or</span>
                    <hr class="flex-grow-1 m-0" style="border-color: #e2e8f0;">
                </div>

                {{-- Email Auto-login Form --}}
                <form action="{{ route('auth.modal.email') }}" method="POST" id="email-auth-form">
                    @csrf
                    <div class="mb-3">
                        <div class="border rounded-3 px-3 pt-2 pb-1" style="border-color: #cbd5e1;">
                            <label class="d-block text-secondary" style="font-size: 10.5px; font-weight: 600; margin-bottom: 1px;">Email</label>
                            <input type="email" name="email" id="modal-email-input"
                                   class="form-control border-0 p-0 shadow-none text-dark fw-medium"
                                   placeholder="you@example.com" required
                                   style="font-size: 14px; background: transparent; height: 26px;">
                        </div>
                    </div>
                    <button type="submit" id="btn-email-continue"
                            class="btn text-white w-100 fw-semibold"
                            style="background: #2067e1; border-radius: 24px; height: 46px; font-size: 14.5px; border: none;">
                        Continue with Email
                    </button>
                </form>

                {{-- Other Ways --}}
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold d-inline-flex align-items-center gap-1" style="color: #2067e1; font-size: 13px;">
                        Other ways to sign in <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
                    </a>
                </div>

                @endauth

                {{-- Legal Footer --}}
                <div class="text-center mt-4" style="font-size: 11px; color: #94a3b8; line-height: 1.5;">
                    By signing in, I agree to Prime Booking's
                    <a href="{{ route('terms') }}" class="fw-semibold text-decoration-none" style="color: #2067e1;">Terms of Use</a>
                    and
                    <a href="{{ route('privacy') }}" class="fw-semibold text-decoration-none" style="color: #2067e1;">Privacy Policy</a>.
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function showAppleComingSoon() {
    const toast = document.createElement('div');
    toast.innerHTML = `<div style="position:fixed;top:24px;right:24px;z-index:99999;background:#1c1e21;color:#fff;padding:14px 20px;border-radius:12px;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,0.3);display:flex;align-items:center;gap:10px;animation:slideInRight .3s ease;">
        <i class="fa-brands fa-apple" style="font-size:18px;"></i> Apple Sign In coming soon!
    </div>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
