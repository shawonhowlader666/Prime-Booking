<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * IdempotencyMiddleware — Stripe & FinTech Standard Idempotency Guard.
 * Prevents duplicate charges, bookings, and payouts when mobile or web clients retry HTTP requests.
 * Header: `Idempotency-Key: <unique-uuid-or-token>`
 */
class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce for state-changing mutating requests (POST, PUT, PATCH, DELETE)
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key') ?? $request->input('_idempotency_key');
        if (!$idempotencyKey) {
            return $next($request);
        }

        $userId = auth()->id() ?? 'anon_' . $request->ip();
        $cacheKey = "idempotency_res_{$userId}_" . md5($idempotencyKey . '_' . $request->path());

        // Check if an identical request was already processed
        if ($cachedResponse = Cache::get($cacheKey)) {
            return response()->json($cachedResponse['data'], $cachedResponse['status'], [
                'X-Idempotency-Replay' => 'true',
                'X-Idempotency-Key'    => $idempotencyKey,
            ]);
        }

        // Lock execution to prevent parallel concurrent execution of the exact same key
        $lockKey = "idempotency_lock_{$cacheKey}";
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Concurrent request in progress with the same Idempotency-Key. Please wait.',
            ], 409);
        }

        try {
            $response = $next($request);

            // Cache successful response for 24 hours
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $content = json_decode($response->getContent(), true) ?: $response->getContent();
                Cache::put($cacheKey, [
                    'data'   => $content,
                    'status' => $response->getStatusCode(),
                ], now()->addDay());
            }

            return $response;
        } finally {
            $lock->release();
        }
    }
}
