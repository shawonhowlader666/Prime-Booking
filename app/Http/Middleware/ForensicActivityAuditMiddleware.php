<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForensicActivityAuditMiddleware
{
    /**
     * Handle an incoming request and record forensic audit trail on data mutations.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log mutating actions that succeeded or produced business response (HTTP 200, 201, 204, 302)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $response->getStatusCode() < 400) {
            $user = $request->user();
            $hotelId = $user?->hotel_id ?? $request->header('X-Hotel-ID') ?? session('active_hotel_id');

            // Sanitize payload to protect sensitive fields
            $sanitizedPayload = $request->except([
                'password',
                'password_confirmation',
                '_token',
                'current_password',
                'credit_card_number',
                'cvv',
                'sync_secret',
            ]);

            $action = strtoupper($request->method()) . ' ' . $request->path();

            try {
                AuditLog::create([
                    'hotel_id' => $hotelId,
                    'user_id' => $user?->id,
                    'action' => substr($action, 0, 50),
                    'entity_type' => 'HTTP_REQUEST',
                    'entity_id' => null,
                    'old_values' => null,
                    'new_values' => [
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'status_code' => $response->getStatusCode(),
                        'payload' => $sanitizedPayload,
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Ensure audit failures do not break production traffic
                logger()->warning('ForensicActivityAuditMiddleware failed to log: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
