<?php

declare(strict_types=1);

namespace App\Console\Commands\Finance;

use App\Services\AccountingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * DailyFinancialReconciliationCommand
 * ────────────────────────────────────
 * Automated Midnight FinTech Trial Balance & Ledger Integrity Verification.
 * Guarantees zero-paisa discrepancy and detects any ledger tampering.
 */
class DailyFinancialReconciliationCommand extends Command
{
    protected $signature = 'finance:reconcile-daily';

    protected $description = 'Perform automated trial balance audit and general ledger immutability scan';

    public function handle(AccountingService $accountingService): int
    {
        $this->info('Starting automated daily financial reconciliation audit...');

        // 1. Audit Trial Balance (Zero-Leakage Invariant)
        $audit = $accountingService->auditTrialBalanceIntegrity();
        $this->table(
            ['Metric', 'Amount / Status'],
            [
                ['Audit Status', $audit['status']],
                ['Gross Cash In', '৳ ' . number_format($audit['gross_collected'], 2)],
                ['Platform Commission', '৳ ' . number_format($audit['platform_commission'], 2)],
                ['Vendor Payable Pool', '৳ ' . number_format($audit['vendor_payable_pool'], 2)],
                ['Total Refunded', '৳ ' . number_format($audit['total_refunded'], 2)],
                ['Discrepancy Amount', '৳ ' . number_format($audit['discrepancy_amount'], 2)],
                ['Mathematical Match', $audit['is_balanced'] ? 'PERFECT (0.00 Diff)' : 'MISMATCH DETECTED'],
            ]
        );

        // 2. Audit Ledger Immutability
        $immutability = $accountingService->verifyLedgerImmutability();
        $this->info("Ledger Immutability Status: {$immutability['status']} ({$immutability['total_records_checked']} records checked)");

        // 3. Cache Audit Snapshot for Admin Live Dashboard
        Cache::put('daily_financial_audit_snapshot', [
            'trial_balance' => $audit,
            'immutability'  => $immutability,
            'audited_at'    => now()->toDateTimeString(),
        ], now()->addDay());

        if (!$audit['is_balanced'] || !$immutability['is_tamper_free']) {
            $this->error('⚠️ FINANCIAL INTEGRITY ALERT: Discrepancy or corruption detected!');
            Log::emergency('FINANCIAL_DISCREPANCY_ALERT', compact('audit', 'immutability'));
            return Command::FAILURE;
        }

        $this->info('✅ Daily financial reconciliation passed with 100% precision. Zero leakage detected.');
        return Command::SUCCESS;
    }
}
