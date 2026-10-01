<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Accounting\AccountingApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API v1 — Enterprise Financial Accounting & Settlements
| Base URL: /api/v1/accounts/...
|--------------------------------------------------------------------------
*/

Route::prefix('accounts')->group(function () {
    // ── Platform Financial Administration (Admin & Super Admin Only) ──────
    Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
        Route::get('/overview',                   [AccountingApiController::class, 'overview'])->name('api.v1.accounts.overview');
        Route::get('/ledger',                     [AccountingApiController::class, 'ledger'])->name('api.v1.accounts.ledger');
        Route::get('/pnl',                        [AccountingApiController::class, 'pnl'])->name('api.v1.accounts.pnl');
        Route::post('/expense',                   [AccountingApiController::class, 'recordExpense'])->name('api.v1.accounts.expense');
        Route::get('/audit-trial-balance',        [AccountingApiController::class, 'auditTrialBalance'])->name('api.v1.accounts.audit-trial-balance');
        Route::get('/admin/commission-settings',  [AccountingApiController::class, 'getCommissionSettings'])->name('api.v1.accounts.admin.commission-settings.get');
        Route::post('/admin/commission-settings', [AccountingApiController::class, 'updateCommissionSettings'])->name('api.v1.accounts.admin.commission-settings.post');
    });

    // ── Vendor Financial & Settlement Endpoints ────────────────────────────
    Route::middleware(['auth:sanctum', 'role:vendor,admin,super_admin'])->group(function () {
        Route::get('/vendor/statement',       [AccountingApiController::class, 'vendorStatement'])->name('api.v1.accounts.vendor.statement');
        Route::post('/vendor/payout-request', [AccountingApiController::class, 'requestPayout'])->name('api.v1.accounts.vendor.payout-request');
        Route::get('/vendor/tax-certificate', [AccountingApiController::class, 'taxCertificate'])->name('api.v1.accounts.vendor.tax-certificate');
        Route::get('/analytics/yield',         [AccountingApiController::class, 'yieldAnalytics'])->name('api.v1.accounts.analytics.yield');
    });

    // ── Public Rate-Limited Checkout Estimator ─────────────────────────────
    Route::post('/rewards/estimate',          [AccountingApiController::class, 'estimateRewards'])->middleware('throttle:30,1')->name('api.v1.accounts.rewards.estimate');
});
