<?php

namespace App\Console\Commands;

use App\Repositories\PropertyRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * WarmCache — Pre-loads frequently-accessed data into Redis
 * Run on every deployment: php artisan cache:warm
 * Also set as a scheduled job every hour in console.php
 */
class WarmCache extends Command
{
    protected $signature   = 'cache:warm {--force : Clear existing cache before warming}';
    protected $description = 'Pre-load homepage, featured properties, and destinations into Redis cache';

    public function __construct(
        protected PropertyRepository $repository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('force')) {
            Cache::flush();
            $this->info('🗑️  Existing cache cleared.');
        }

        $this->info('🔥 Warming PRIME BOOKING cache...');
        $start = microtime(true);

        // ── Featured Properties ──────────────────────────────────────────
        Cache::forget('properties:featured:6');
        $featured = $this->repository->getFeatured(6);
        $this->line("  ✅ Featured properties: <info>{$featured->count()}</info> loaded");

        // ── Destinations ─────────────────────────────────────────────────
        Cache::forget('properties:destinations:8');
        $destinations = $this->repository->getDestinations(8);
        $this->line("  ✅ Destinations: <info>{$destinations->count()}</info> loaded");

        // ── Site Stats ────────────────────────────────────────────────────
        Cache::forget('properties:site_stats');
        $stats = $this->repository->getSiteStats();
        $this->line("  ✅ Site stats: <info>" . json_encode($stats) . "</info>");

        // ── Available Cities ──────────────────────────────────────────────
        Cache::forget('properties:cities');
        $cities = $this->repository->getAvailableCities();
        $this->line("  ✅ Cities: <info>" . count($cities) . "</info> loaded");

        // ── Price Range ───────────────────────────────────────────────────
        Cache::forget('properties:price_range');
        $prices = $this->repository->getPriceRange();
        $this->line("  ✅ Price range: <info>BDT {$prices['min']} – {$prices['max']}</info>");

        $elapsed = round((microtime(true) - $start) * 1000, 1);
        $this->info("🚀 Cache warm-up complete in <comment>{$elapsed}ms</comment>");

        return Command::SUCCESS;
    }
}

