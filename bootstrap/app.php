<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api-v1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ── Exclude External Callback / One-Tap POSTs from CSRF Protection ──
        $middleware->validateCsrfTokens(except: [
            'auth/*',
            'auth/*/*',
            'payment/*',
        ]);

        // ── Web Middleware Stack ──────────────────────────────────────────
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackLastLogin::class,
        ]);

        // ── Named Middleware Aliases ──────────────────────────────────────
        $middleware->alias([
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ── API Error Responses — return JSON instead of HTML ─────────────
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login.',
                    'code'    => 401,
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Insufficient permissions.',
                    'code'    => 403,
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'success' => false,
                    'message' => "{$model} not found.",
                    'code'    => 404,
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors(),
                    'code'    => 422,
                ], 422);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'HTTP Error.',
                    'code'    => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                \Log::error('DB Query Exception: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'A database error occurred. Please try again.',
                    'code'    => 500,
                ], 500);
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                \Log::error('Unhandled Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                $message = app()->environment('production') ? 'An unexpected error occurred.' : $e->getMessage();
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'code'    => 500,
                ], 500);
            }
        });

    })->create();
