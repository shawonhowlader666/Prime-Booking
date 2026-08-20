<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ─── Artisan Commands ─────────────────────────────────────────────────────────
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Scheduled Tasks ──────────────────────────────────────────────────────────

/**
 * Cache warming — runs every 2 hours to keep Redis hot
 * This ensures homepage stays fast even after cache expiry
 */
Schedule::command('cache:warm')->everyTwoHours()->withoutOverlapping();

/**
 * Process queued jobs — runs every minute (only if using sync queue)
 * For production: use supervisor to run 'php artisan queue:work' as a daemon
 */
// Schedule::command('queue:work --stop-when-empty --max-jobs=100')->everyMinute();

/**
 * Clean up failed jobs older than 7 days
 */
Schedule::command('queue:prune-failed --hours=168')->weekly();

/**
 * Clear expired password reset tokens
 */
Schedule::command('auth:clear-resets')->daily();

/**
 * Prune old activity logs older than 180 days to keep table lean
 */
Schedule::call(function () {
    \App\Models\ActivityLog::where('created_at', '<', now()->subDays(180))->delete();
})->monthly()->name('prune-old-activity-logs');

/**
 * Scan active price drop alerts and notify subscribers
 */
Schedule::command('alerts:check-price-drops')->dailyAt('09:00')->withoutOverlapping();

