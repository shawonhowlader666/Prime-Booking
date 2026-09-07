<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HealthCheckController extends Controller
{
    /**
     * Enterprise System Health & Telemetry Probe for Cloud Orchestrators (Kubernetes / AWS / ALB)
     */
    public function __invoke(): JsonResponse
    {
        $start = microtime(true);
        $status = 'healthy';
        $checks = [];

        // 1. Database Connectivity & Ping Latency
        try {
            $dbStart = microtime(true);
            DB::select('SELECT 1');
            $dbLatencyMs = round((microtime(true) - $dbStart) * 1000, 2);
            $checks['database'] = [
                'status' => 'UP',
                'latency_ms' => $dbLatencyMs,
                'connection' => config('database.default'),
            ];
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $checks['database'] = [
                'status' => 'DOWN',
                'error' => $e->getMessage(),
            ];
        }

        // 2. Cache Engine Check
        try {
            $cacheKey = 'health_probe_' . time();
            Cache::put($cacheKey, 'ok', 5);
            $isCacheOk = (Cache::get($cacheKey) === 'ok');
            Cache::forget($cacheKey);

            $checks['cache'] = [
                'status' => $isCacheOk ? 'UP' : 'DEGRADED',
                'driver' => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $checks['cache'] = [
                'status' => 'DOWN',
                'error' => $e->getMessage(),
            ];
        }

        // 3. Storage Write Permissions
        try {
            $storagePath = storage_path('framework/cache');
            $isWritable = is_writable($storagePath);
            $checks['storage'] = [
                'status' => $isWritable ? 'UP' : 'READ_ONLY',
                'path' => $storagePath,
            ];
        } catch (\Throwable $e) {
            $checks['storage'] = [
                'status' => 'DOWN',
                'error' => $e->getMessage(),
            ];
        }

        $totalExecutionMs = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'app_name' => config('app.name', 'Prime-PMS'),
            'environment' => config('app.env'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'latency_ms' => $totalExecutionMs,
            'checks' => $checks,
        ], $status === 'healthy' ? 200 : 503);
    }
}
