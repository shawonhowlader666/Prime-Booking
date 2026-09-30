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
    Route::get('/overview',              [AccountingApiController::class, 'overview'])->name('api.v1.accounts.overview');
    Route::get('/ledger',                [AccountingApiController::class, 'ledger'])->name('api.v1.accounts.ledger');
    Route::get('/pnl',                   [AccountingApiController::class, 'pnl'])->name('api.v1.accounts.pnl');
    Route::get('/vendor/statement',      [AccountingApiController::class, 'vendorStatement'])->name('api.v1.accounts.vendor.statement');
    Route::post('/vendor/payout-request',[AccountingApiController::class, 'requestPayout'])->name('api.v1.accounts.vendor.payout-request');
    Route::post('/expense',                   [AccountingApiController::class, 'recordExpense'])->name('api.v1.accounts.expense');
    Route::get('/audit-trial-balance',        [AccountingApiController::class, 'auditTrialBalance'])->name('api.v1.accounts.audit-trial-balance');
    Route::get('/admin/commission-settings',  [AccountingApiController::class, 'getCommissionSettings'])->name('api.v1.accounts.admin.commission-settings.get');
    Route::post('/admin/commission-settings', [AccountingApiController::class, 'updateCommissionSettings'])->name('api.v1.accounts.admin.commission-settings.post');
    Route::post('/rewards/estimate',          [AccountingApiController::class, 'estimateRewards'])->name('api.v1.accounts.rewards.estimate');
    Route::get('/vendor/tax-certificate',     [AccountingApiController::class, 'taxCertificate'])->name('api.v1.accounts.vendor.tax-certificate');
    Route::get('/analytics/yield',            [AccountingApiController::class, 'yieldAnalytics'])->name('api.v1.accounts.analytics.yield');
});
