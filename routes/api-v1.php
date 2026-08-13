<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile App & Web RESTful API v1 Master Router
| Base URL: /api/v1/...
| Architecture: Enterprise Modular Route Structure
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // System Deployment & Cache Purge Route
    Route::get('/deploy-sync-secret-key-9808165d', function () {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        $gitOut = @shell_exec('cd ' . base_path() . ' && git pull origin master 2>&1');
        return response()->json([
            'success'    => true,
            'message'    => 'View cache cleared and git sync executed successfully!',
            'git_output' => $gitOut,
        ]);
    });

    // Payment Gateway Webhook / IPN Listener Callback
    Route::match(['get', 'post'], '/payment/callback/{gateway}', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'handleCallback']);

    // ── Modular API v1 Route Files ──────────────────────────────────────
    require __DIR__ . '/api/v1/auth.php';
    require __DIR__ . '/api/v1/properties.php';
    require __DIR__ . '/api/v1/bookings.php';
    require __DIR__ . '/api/v1/inquiries.php';
    require __DIR__ . '/api/v1/content.php';

});
