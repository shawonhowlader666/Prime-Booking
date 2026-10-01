<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

/**
 * Production-grade OAuth + Traditional Auth Controller.
 *
 * OAuth Flow:
 *   GET  /auth/{provider}/redirect  → Redirect to provider consent screen
 *   GET  /auth/{provider}/callback  → Handle callback, auto login/register
 *
 * Traditional Auth:
 *   GET  /login                     → Login page
 *   POST /login                     → Authenticate with email + password
 *   GET  /register                  → Register page
 *   POST /register                  → Create account with email + password
 *   POST /auth/email                → Modal email-only auto login/register
 *   POST /auth/logout               → Sign out
 */
class OAuthController extends Controller
{
    /** Providers with real Socialite integration */
    private const LIVE_PROVIDERS = ['google', 'facebook'];

    /** Providers planned but not yet ready */
    private const COMING_SOON = ['apple'];

    // =========================================================================
    // OAUTH — Social Sign In (Google, Facebook, Apple)
    // =========================================================================

    /**
     * Redirect the user to the OAuth provider's authorization page or handle One-Tap POST payload.
     * GET/POST /auth/{provider}/redirect
     */
    public function redirect(Request $request, string $provider)
    {
        if (in_array($provider, self::COMING_SOON)) {
            return redirect()->back()
                ->with('info', 'Apple Sign In is coming soon! Please use Google or Facebook for now.');
        }

        if (! in_array($provider, self::LIVE_PROVIDERS)) {
            abort(404);
        }

        // ── Handle Google One-Tap POST Credential Payload ────────────────────
        if ($request->isMethod('post') && $request->filled('credential')) {
            try {
                $idToken = (string) $request->input('credential');
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

                if ($response->successful()) {
                    $payload = $response->json();
                    $isVerified = ($payload['email_verified'] ?? null) === 'true' || ($payload['email_verified'] ?? null) === true;

                    if ($isVerified && ! empty($payload['email'])) {
                        $email  = strtolower(trim($payload['email']));
                        $name   = $payload['name'] ?? $payload['given_name'] ?? explode('@', $email)[0];
                        $avatar = $payload['picture'] ?? null;

                        $user = User::where('email', $email)->first();

                        if (! $user) {
                            $user = User::create([
                                'name'              => $name,
                                'email'             => $email,
                                'password'          => Hash::make(Str::random(32)),
                                'role'              => 'customer',
                                'status'            => 'active',
                                'avatar'            => $avatar,
                                'email_verified_at' => now(),
                            ]);
                        } else {
                            if ($avatar && ! $user->avatar) {
                                $user->update(['avatar' => $avatar]);
                            }
                        }

                        $this->loginUser($request, $user);

                        return redirect()->route('home')->with('success', "Welcome back, {$user->name}!");
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Google One-Tap POST handler error: " . $e->getMessage());
            }
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle account selection from local preview Account Chooser.
     */
    public function demoSelect(Request $request): RedirectResponse
    {
        abort(404);
    }

    /**
     * Handle callback from OAuth provider. Find or create user, then login.
     * GET /auth/{provider}/callback
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        if (! in_array($provider, self::LIVE_PROVIDERS)) {
            return redirect()->route('home')->with('error', 'Unknown provider.');
        }

        try {
            /** @var \Laravel\Socialite\Two\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::warning("OAuth [{$provider}] callback failed", [
                'error' => $e->getMessage(),
                'ip'    => $request->ip(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Google sign-in failed. Please use email & password, or try again later.');
        }

        $user = DB::transaction(fn () => $this->findOrCreateUser($socialUser, $provider));

        $this->loginUser($request, $user);

        Log::info("User [{$user->email}] authenticated via [{$provider}]", ['user_id' => $user->id]);

        $intended = session()->pull('url.intended', route('home'));

        return redirect($intended)
            ->with('success', "Welcome, {$user->name}! Signed in with " . ucfirst($provider) . '.');
    }

    // =========================================================================
    // TRADITIONAL AUTH — Email + Password Login / Register Pages
    // =========================================================================

    /**
     * Show the dedicated Sign In page.
     * GET /login
     */
    public function showLogin(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Authenticate user with email + password.
     * POST /login
     */
    public function loginWithPassword(Request $request): RedirectResponse
    {
        $rawCredential = $request->input('login_credential', $request->input('email'));
        if (empty($rawCredential)) {
            return back()->withInput()->withErrors(['email' => 'Please enter your email address or phone number.']);
        }

        $credential = trim((string)$rawCredential);
        $password   = (string)$request->input('password', '');
        $remember   = $request->boolean('remember');

        if (empty($password)) {
            return back()->withInput()->withErrors(['password' => 'Please enter your password.']);
        }

        $isEmail = filter_var($credential, FILTER_VALIDATE_EMAIL);
        $user = $isEmail 
            ? User::where('email', $credential)->first() 
            : User::where('phone', $credential)->orWhere('phone', 'like', '%' . $credential)->first();

        $authenticated = false;

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $authenticated = true;
        }

        if (! $authenticated) {
            $field = $isEmail ? 'email' : 'phone';
            if (Auth::attempt([$field => $credential, 'password' => $password], $remember)) {
                $user = Auth::user();
                $authenticated = true;
            }
        }

        if (! $authenticated || ! $user) {
            return back()
                ->withInput($request->only('email', 'login_credential'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        if ($user->status === 'banned') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been suspended. Contact support.']);
        }

        $request->session()->regenerate();
        $user->recordLogin($request->ip());

        // Role-based smart redirection
        if ($user->isAdmin() || in_array($user->role, ['admin', 'super_admin'], true)) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Welcome back, Admin {$user->name}!");
        } elseif ($user->isVendor() || $user->role === 'vendor') {
            return redirect()->intended(route('vendor.dashboard'))
                ->with('success', "Welcome back, Partner {$user->name}!");
        }

        return redirect()->intended(route('home'))
            ->with('success', "Welcome back, {$user->name}!");
    }

    /**
     * Show the Register / Create Account page.
     * GET /register
     */
    public function showRegister(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Create a new user account with email + password.
     * POST /register
     */
    public function registerWithPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = DB::transaction(function () use ($data) {
            return User::create([
                'name'     => trim($data['name']),
                'email'    => strtolower(trim($data['email'])),
                'password' => Hash::make($data['password']),
                'role'     => 'customer',
                'status'   => 'active',
            ]);
        });

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        Log::info("New user registered via email form", ['user_id' => $user->id, 'email' => $user->email]);

        return redirect()->route('home')
            ->with('success', "Welcome to Prime Booking, {$user->name}! Your account has been created.");
    }

    /**
     * Show dedicated Super Admin Portal Login Page.
     * GET /admin/login
     */
    public function showAdminLogin(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Authenticate Super Admin user credentials.
     * POST /admin/login
     */
    public function loginAdmin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $email    = trim($request->input('email'));
        $password = (string)$request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $email)->first();

        $valid = false;
        if ($user && ($user->role === 'admin' || $user->role === 'super_admin')) {
            if (Hash::check($password, $user->password)) {
                $valid = true;
                Auth::login($user, $remember);
            }
        }

        if (! $valid && ! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Invalid admin credentials provided.']);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. You do not have administrator privileges.']);
        }

        $request->session()->regenerate();
        $user->recordLogin($request->ip());

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', "Welcome back, Admin {$user->name}!");
    }

    /**
     * Show dedicated Vendor Partner Portal Login Page.
     * GET /vendor/login
     */
    public function showVendorLogin(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (Auth::check() && (Auth::user()->isVendor() || Auth::user()->role === 'vendor')) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendor.login');
    }

    /**
     * Authenticate Vendor Partner credentials.
     * POST /vendor/login
     */
    public function loginVendor(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $email    = trim($request->input('email'));
        $password = (string)$request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $email)->first();

        $valid = false;
        if ($user && ($user->role === 'vendor' || $user->role === 'admin' || $user->role === 'super_admin')) {
            if (Hash::check($password, $user->password)) {
                $valid = true;
                Auth::login($user, $remember);
            }
        }

        if (! $valid && ! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Invalid partner credentials.']);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'vendor' && $user->role !== 'admin' && $user->role !== 'super_admin') {
            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. You are not registered as a vendor partner.']);
        }

        $request->session()->regenerate();
        $user->recordLogin($request->ip());

        return redirect()->intended(route('vendor.dashboard'))
            ->with('success', "Welcome to your Partner Control Center, {$user->name}!");
    }

    // =========================================================================
    // EMAIL MODAL — Safe Verification / Sign In
    // =========================================================================

    /**
     * Email modal continuation: Redirects existing users to password login,
     * or new users to registration. Never logs in without password.
     * POST /auth/email
     */
    public function handleEmail(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);

        $email = strtolower(trim($data['email']));
        $user  = User::where('email', $email)->first();

        if ($user) {
            return redirect()->route('login', ['email' => $email])
                ->with('info', 'An account with this email was found. Please enter your password to sign in.');
        }

        return redirect()->route('register', ['email' => $email])
            ->with('info', 'Please set up a password to complete your account registration.');
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    /**
     * Sign out the authenticated user.
     * POST /auth/logout  or  POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $name = Auth::user()?->name ?? 'Guest';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', "Goodbye, {$name}! You have been signed out.");
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Find an existing user via their social account, or create a new one.
     * Uses a dedicated `social_accounts` pivot table (best practice).
     */
    private function findOrCreateUser(
        \Laravel\Socialite\Contracts\User $socialUser,
        string $provider
    ): User {
        // 1. Look up existing social account link
        $link = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->with('user')
            ->first();

        if ($link) {
            // Refresh tokens and avatar
            $link->update([
                'access_token'     => $socialUser->token,
                'refresh_token'    => $socialUser->refreshToken,
                'token_expires_at' => isset($socialUser->expiresIn)
                    ? now()->addSeconds((int) $socialUser->expiresIn)
                    : null,
                'provider_avatar'  => $socialUser->getAvatar(),
                'provider_name'    => $socialUser->getName(),
            ]);

            if ($socialUser->getAvatar()) {
                $link->user->update(['avatar' => $socialUser->getAvatar()]);
            }

            return $link->user;
        }

        // 2. Check if user exists with this email (account merge)
        $email = strtolower((string)$socialUser->getEmail());
        $isAdminEmail = in_array($email, ['shawonhawlader1044@gmail.com', 'shawonhawlader666@gmail.com', 'admin@primebooking.com.bd', 'admin@primeavn.com']);

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name'              => $socialUser->getName() ?? explode('@', $email)[0],
                'email'             => $email,
                'password'          => Hash::make(Str::random(32)),
                'role'              => $isAdminEmail ? 'admin' : 'customer',
                'status'            => 'active',
                'avatar'            => $socialUser->getAvatar(),
                'email_verified_at' => now(), // OAuth emails are pre-verified by provider
            ]);
        } else {
            if ($isAdminEmail && $user->role !== 'admin') {
                $user->update(['role' => 'admin']);
            }
            if (! $user->avatar && $socialUser->getAvatar()) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }
        }

        // 3. Create the social_accounts link
        SocialAccount::create([
            'user_id'          => $user->id,
            'provider'         => $provider,
            'provider_id'      => $socialUser->getId(),
            'provider_email'   => $socialUser->getEmail(),
            'provider_name'    => $socialUser->getName(),
            'provider_avatar'  => $socialUser->getAvatar(),
            'access_token'     => $socialUser->token,
            'refresh_token'    => $socialUser->refreshToken,
            'token_expires_at' => isset($socialUser->expiresIn)
                ? now()->addSeconds((int) $socialUser->expiresIn)
                : null,
        ]);

        return $user;
    }

    /**
     * Login the given user and regenerate the session.
     */
    private function loginUser(Request $request, User $user): void
    {
        Auth::login($user, remember: true);
        $request->session()->regenerate();
        $user->recordLogin($request->ip());

        try {
            ActivityLog::create([
                'user_id'     => $user->id,
                'user_name'   => $user->name,
                'action'      => 'login',
                'model_type'  => 'User',
                'model_id'    => $user->id,
                'description' => "User [{$user->name}] signed in from IP {$request->ip()}",
                'ip_address'  => $request->ip(),
                'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Silence log exceptions if table unavailable
        }
    }
}
