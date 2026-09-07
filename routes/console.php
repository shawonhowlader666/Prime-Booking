<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Background Tasks
|--------------------------------------------------------------------------
*/

// Run automated Night Audit every night at 23:59 for all active properties
Schedule::command('pms:night-audit')
    ->dailyAt('23:59')
    ->withoutOverlapping()
    ->runInBackground();

// Audit SaaS tenant subscription health & license expiries daily at 01:00
Schedule::command('pms:check-subscriptions')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->runInBackground();
