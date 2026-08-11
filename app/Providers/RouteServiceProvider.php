<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    /**
     * Define rate limiters for the application.
     * Called before routes are registered.
     */
    protected function configureRateLimiting(): void
    {
        // ── Public API: 60 requests per minute per IP ────────────────────
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)  // Authenticated: 120/min
                : Limit::perMinute(60)->by($request->ip());         // Guest: 60/min
        });

        // ── Search API: 30 searches per minute ───────────────────────────
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(fn() => response()->json([
                    'success' => false,
                    'message' => 'Too many search requests. Please wait a moment.',
                    'retry_after' => 60,
                ], 429));
        });

        // ── Auth API: 10 login attempts per minute ────────────────────────
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(fn() => response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please wait 1 minute.',
                    'retry_after' => 60,
                ], 429));
        });

        // ── Booking: 5 booking submissions per 5 minutes per IP ─────────
        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinutes(5, 5)
                ->by($request->user()?->id ?: $request->ip())
                ->response(fn() => response()->json([
                    'success' => false,
                    'message' => 'Too many booking attempts. Please try again in a few minutes.',
                ], 429));
        });
    }

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API v1 routes
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
