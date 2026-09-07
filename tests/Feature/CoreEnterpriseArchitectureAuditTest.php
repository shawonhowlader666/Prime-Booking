<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreEnterpriseArchitectureAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_enterprise_security_headers_and_correlation_id_are_present(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNotEmpty($response->headers->get('X-Correlation-ID'));
    }

    public function test_enterprise_health_telemetry_probe(): void
    {
        $response = $this->getJson('/api/v1/health');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'timestamp',
            'app_name',
            'environment',
            'php_version',
            'laravel_version',
            'memory_usage_mb',
            'latency_ms',
            'checks' => [
                'database' => ['status', 'latency_ms', 'connection'],
                'cache' => ['status', 'driver'],
                'storage' => ['status', 'path'],
            ],
        ]);

        $this->assertEquals('healthy', $response->json('status'));
        $this->assertEquals('UP', $response->json('checks.database.status'));
        $this->assertEquals('UP', $response->json('checks.cache.status'));
        $this->assertEquals('UP', $response->json('checks.storage.status'));
    }
}
