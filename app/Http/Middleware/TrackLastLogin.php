<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TrackLastLogin — updates last_login_at on every authenticated web request
 * Runs transparently in the web middleware stack
 */
class TrackLastLogin
{
    private static bool $updated = false;

    public function handle(Request $request, Closure $next): Response
    {
        if (!self::$updated && auth()->check()) {
            $user = auth()->user();
            // Only write once per session (every 10 min max)
            $key  = 'last_login_tracked_' . $user->id;
            if (!session()->has($key)) {
                try {
                    $user->update([
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip(),
                    ]);
                } catch (\Exception $e) {}
                session()->put($key, true);
                self::$updated = true;
            }
        }
        return $next($request);
    }
}
