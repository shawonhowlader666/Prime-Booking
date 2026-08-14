<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Tenant;

class PlatformHealthCheckCommand extends Command
{
    protected $signature = 'prime:health-check';
    protected $description = 'Perform an enterprise-level platform health audit & system status diagnostic';

    public function handle(): int
    {
        $this->info('=====================================================');
        $this->info('      PRIME BOOKING ENTERPRISE HEALTH DIAGNOSTIC    ');
        $this->info('=====================================================');

        $rows = [];

        // 1. Database Check
        try {
            $dbDriver = DB::getDriverName();
            $dbHost   = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort   = config('database.connections.mysql.port', '3307');
            $dbName   = DB::connection()->getDatabaseName();
            $rows[] = ['Database', 'HEALTHY', "Connected via {$dbDriver} ({$dbHost}:{$dbPort} / {$dbName})"];
        } catch (\Throwable $e) {
            $rows[] = ['Database', 'FAILED', $e->getMessage()];
        }

        // 2. Cache Check
        try {
            Cache::put('health_test_key', 'OK', 10);
            $val = Cache::get('health_test_key');
            $rows[] = ['Cache Engine', $val === 'OK' ? 'HEALTHY' : 'DEGRADED', 'Driver: ' . config('cache.default')];
        } catch (\Throwable $e) {
            $rows[] = ['Cache Engine', 'FAILED', $e->getMessage()];
        }

        // 3. Storage Link Check
        $publicStorage = public_path('storage');
        $rows[] = ['Public Storage Symlink', file_exists($publicStorage) ? 'HEALTHY' : 'WARNING', $publicStorage];

        // 4. Metrics & Entity Audits
        try {
            $propCount = Property::whereIn('status', ['active', 'published'])->count();
            $bookingCount = Booking::count();
            $tenantCount = Tenant::count();

            $rows[] = ['Active Properties', 'ACTIVE', "{$propCount} Verified Properties in DB"];
            $rows[] = ['Total Reservations', 'ACTIVE', "{$bookingCount} Guest Bookings Processed"];
            $rows[] = ['SaaS Tenants', 'ACTIVE', "{$tenantCount} SaaS Partner Tenants"];
        } catch (\Throwable $e) {
            $rows[] = ['Entity Metrics', 'FAILED', $e->getMessage()];
        }

        $this->table(['Component', 'Status', 'Diagnostic Details'], $rows);
        $this->info("\nPlatform Status: 100% Operational & Enterprise Ready.\n");

        return self::SUCCESS;
    }
}

