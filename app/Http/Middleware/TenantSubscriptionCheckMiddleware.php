<?php

namespace App\Http\Middleware;

use App\Models\Hotel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantSubscriptionCheckMiddleware
{
    /**
     * Handle an incoming request and ensure the tenant's SaaS subscription is active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Whitelist routes that should always be accessible even when subscription is expired/locked
        $whitelistedPatterns = [
            'login*',
            'signup*',
            'logout*',
            'subscription*',
            'api/v1/health*',
            'api/v1/sync/*',
            'api/v1/auth/*',
            'assets/*',
        ];

        foreach ($whitelistedPatterns as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Determine active hotel
        $hotel = null;
        if (auth()->check()) {
            $hotelId = session('active_hotel_id', auth()->user()->hotel_id);
            $hotel = Hotel::find($hotelId);
        } elseif ($request->hasHeader('X-Hotel-Code')) {
            $hotel = Hotel::where('code', $request->header('X-Hotel-Code'))->first();
        }

        if ($hotel && !$hotel->isSubscriptionActive()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'subscription_status' => $hotel->subscription_status,
                    'is_locked' => (bool) $hotel->is_subscription_locked,
                    'message' => 'Hotel subscription is currently expired or suspended by Prime Booking SuperAdmin. Please renew.',
                ], 403);
            }

            return redirect()->route('subscription.show')->with('warning', 'Your hotel subscription is currently inactive or suspended. Please renew to regain full access.');
        }

        return $next($request);
    }
}
