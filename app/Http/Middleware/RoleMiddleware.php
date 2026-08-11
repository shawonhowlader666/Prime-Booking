<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 * ──────────────
 * Usage in routes:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,super_admin')
 *   ->middleware('role:vendor')
 *
 * Also checks if user is banned — banned users are logged out.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Must be authenticated
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('signin')->with('error', 'Please log in to continue.');
        }

        $user = auth()->user();

        // Banned users are kicked out
        if ($user->isBanned()) {
            auth()->logout();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Your account has been suspended.'], 403);
            }
            return redirect()->route('signin')->with('error', 'Your account has been suspended. Contact support.');
        }

        // Check role — allow if user has any of the required roles
        if (!empty($roles) && !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Access denied. Insufficient permissions.'], 403);
            }
            abort(403, 'Access denied. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
