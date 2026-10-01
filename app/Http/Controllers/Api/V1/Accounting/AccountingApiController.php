<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingLedger;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * AccountingApiController
 * ───────────────────────
 * REST API v1 for Enterprise Accounting, Income & Expense Tracking,
 * Trial Balance Reconciliations, and Vendor Settlements.
 */
class AccountingApiController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * GET /api/v1/accounts/overview
     * Platform Master Income, Expenses, Escrow, and Margin Overview.
     */
    public function overview(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $kpis = $this->accountingService->getOverviewKPIs($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data'    => [
                'income' => [
                    'gross_booking_value' => $kpis['gross_booking_value'],
                    'platform_commission' => $kpis['platform_commission'],
                    'total_orders'        => $kpis['total_orders'],
                    'confirmed_orders'    => $kpis['confirmed_orders'],
                ],
                'expenses_and_costs' => [
                    'gateway_fees'         => $kpis['gateway_fees'],
                    'customer_refunds'     => $kpis['total_refunded'],
                    'cancelled_value'      => $kpis['total_cancelled'],
                ],
                'liabilities_and_escrow' => [
                    'vendor_payable_pool'   => $kpis['vendor_payable'],
                    'settled_payouts'       => $kpis['total_settled_payouts'],
                    'pending_payouts'       => $kpis['pending_payouts'],
                    'escrow_holding_pool'   => $kpis['escrow_holding_pool'],
                ],
                'profitability' => [
                    'net_operating_profit'  => $kpis['net_profit'],
                    'net_margin_percentage' => $kpis['platform_commission'] > 0
                        ? round(($kpis['net_profit'] / $kpis['platform_commission']) * 100, 2)
                        : 0.0,
                ],
                'meta' => [
                    'currency'   => 'BDT',
                    'start_date' => $startDate ?? 'all_time',
                    'end_date'   => $endDate ?? 'all_time',
                    'audited_at' => now()->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * GET /api/v1/accounts/ledger
     * Filterable double-entry transactions.
     */
    public function ledger(Request $request): JsonResponse
    {
        $filters = [
            'type'           => $request->query('type', 'all'),
            'payment_method' => $request->query('payment_method', 'all'),
            'start_date'     => $request->query('start_date'),
            'end_date'       => $request->query('end_date'),
            'search'         => $request->query('search'),
        ];

        $perPage = min(100, (int) $request->query('per_page', 25));
        $ledgers = $this->accountingService->getGeneralLedger($filters, $perPage);

        return response()->json([
            'success' => true,
            'data'    => $ledgers->items(),
            'pagination' => [
                'current_page' => $ledgers->currentPage(),
                'last_page'    => $ledgers->lastPage(),
                'total'        => $ledgers->total(),
                'per_page'     => $ledgers->perPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/accounts/pnl
     * 12-Month Profit & Loss Series.
     */
    public function pnl(Request $request): JsonResponse
    {
        $year = (int) $request->query('year', date('Y'));
        $data = $this->accountingService->getMonthlyPnLChartData($year);

        return response()->json([
            'success' => true,
            'year'    => $year,
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/v1/accounts/vendor/statement
     * Vendor statement & Matured vs In-Escrow balance breakdown.
     */
    public function vendorStatement(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $vendorId = ($user->isAdmin() || $user->isSuperAdmin())
            ? (int) ($request->query('vendor_id') ?? $user->id)
            : (int) $user->id;

        $finance = $this->accountingService->getSingleVendorAccounting($vendorId);
        $recentTransactions = AccountingLedger::where('vendor_id', $vendorId)->latest('id')->take(10)->get();

        return response()->json([
            'success' => true,
            'vendor_id' => $vendorId,
            'data' => [
                'withdrawable_balance'   => $finance['withdrawable_balance'],
                'matured_balance'        => $finance['matured_earnings'],
                'escrow_upcoming_balance'=> $finance['escrow_future_balance'],
                'gross_sales_turnover'   => $finance['gross_revenue'],
                'commission_deducted'    => $finance['commission_paid'],
                'net_earnings'           => $finance['net_earnings'],
                'payouts_settled'        => $finance['payouts_paid'],
                'payouts_pending'        => $finance['payouts_pending'],
                'total_bookings'         => $finance['total_bookings'],
                'recent_ledger_records'  => $recentTransactions,
            ],
        ]);
    }

    /**
     * POST /api/v1/accounts/vendor/payout-request
     * Submit an atomic withdrawal request.
     */
    public function requestPayout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id'       => 'nullable|integer|exists:users,id',
            'amount'          => 'required|numeric|min:100',
            'payment_method'  => 'required|string|in:bkash,nagad,rocket,bank_transfer',
            'account_number'  => 'required|string|max:50',
            'bank_name'       => 'nullable|string|max:100',
            'branch_name'     => 'nullable|string|max:100',
            'routing_number'  => 'nullable|string|max:50',
            'account_name'    => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $vendorId = ($user->isAdmin() || $user->isSuperAdmin())
            ? (int) ($validated['vendor_id'] ?? $user->id)
            : (int) $user->id;

        $amount   = (float) $validated['amount'];

        $details = [
            'account_number' => $validated['account_number'],
            'bank_name'      => $validated['bank_name'] ?? null,
            'branch_name'    => $validated['branch_name'] ?? null,
            'routing_number' => $validated['routing_number'] ?? null,
            'account_name'   => $validated['account_name'] ?? null,
            'phone'          => $validated['account_number'],
        ];

        $res = $this->accountingService->processSecureVendorPayout(
            $vendorId,
            $amount,
            $validated['payment_method'],
            $details,
            $user
        );

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    /**
     * POST /api/v1/accounts/expense
     * Record platform operational expense (Server hosting, SMS, marketing, office).
     */
    public function recordExpense(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category'       => 'required|string|max:64',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:32',
            'description'    => 'required|string|max:255',
            'notes'          => 'nullable|string|max:500',
        ]);

        $entry = $this->accountingService->recordManualEntry([
            'type'           => 'debit',
            'category'       => $validated['category'],
            'gross_amount'   => (float) $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'description'    => $validated['description'],
            'notes'          => $validated['notes'] ?? null,
            'user_id'        => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Operational expense recorded in General Ledger.',
            'entry'   => $entry,
        ], 201);
    }

    /**
     * GET /api/v1/accounts/audit-trial-balance
     * Mathematical Zero-Leakage Trial Balance Invariant Audit.
     */
    public function auditTrialBalance(): JsonResponse
    {
        $audit = $this->accountingService->auditTrialBalanceIntegrity();
        $immutability = $this->accountingService->verifyLedgerImmutability();

        return response()->json([
            'success'       => $audit['is_balanced'] && $immutability['is_tamper_free'],
            'trial_balance' => $audit,
            'immutability'  => $immutability,
        ]);
    }

    /**
     * GET & POST /api/v1/accounts/admin/commission-settings
     * Dynamic Platform Commission Control API.
     */
    public function getCommissionSettings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'default_commission_rate' => (float) \App\Models\SiteSetting::get('default_commission_rate', 12.0),
                'mobile_banking_fee'      => (float) \App\Models\SiteSetting::get('mobile_banking_fee', 0.015),
                'card_processing_fee'     => (float) \App\Models\SiteSetting::get('card_processing_fee', 0.020),
                'vat_rate_on_commission'  => 15.0,
                'tds_rate_on_commission'  => 5.0,
                'min_payout_threshold'    => (float) \App\Models\SiteSetting::get('min_vendor_payout', 500),
            ],
        ]);
    }

    public function updateCommissionSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'default_commission_rate' => 'required|numeric|min:0|max:50',
            'mobile_banking_fee'      => 'nullable|numeric|min:0|max:0.1',
            'card_processing_fee'     => 'nullable|numeric|min:0|max:0.1',
            'min_payout_threshold'    => 'nullable|numeric|min:100',
        ]);

        \App\Models\SiteSetting::set('default_commission_rate', (string)$validated['default_commission_rate']);
        if (isset($validated['mobile_banking_fee'])) {
            \App\Models\SiteSetting::set('mobile_banking_fee', (string)$validated['mobile_banking_fee']);
        }
        if (isset($validated['card_processing_fee'])) {
            \App\Models\SiteSetting::set('card_processing_fee', (string)$validated['card_processing_fee']);
        }
        if (isset($validated['min_payout_threshold'])) {
            \App\Models\SiteSetting::set('min_vendor_payout', (string)$validated['min_payout_threshold']);
        }

        \Illuminate\Support\Facades\Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Platform commission & financial settings updated dynamically.',
            'data'    => $validated,
        ]);
    }

    /**
     * POST /api/v1/accounts/rewards/estimate
     * Real-time checkout reward points and vendor payout estimator.
     */
    public function estimateRewards(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_amount' => 'required|numeric|min:1',
            'user_id'        => 'nullable|integer|exists:users,id',
            'property_id'    => 'nullable|integer|exists:properties,id',
        ]);

        $amount  = (float) $validated['booking_amount'];
        $user    = isset($validated['user_id']) ? User::find($validated['user_id']) : auth()->user();
        $property= isset($validated['property_id']) ? \App\Models\Property::find($validated['property_id']) : null;

        $rewardService = app(\App\Services\RewardPointService::class);
        $earnablePoints = $rewardService->calculatePoints($amount);

        $userSummary = $rewardService->getUserRewardSummary($user);
        $availablePoints = $userSummary['points'] ?? 0;
        $maxRedeemableBdt = $rewardService->convertPointsToBdt($availablePoints);

        $commRate = (float) ($property?->commission_rate ?? \App\Models\SiteSetting::get('default_commission_rate', 12.0));
        $commAmount = round($amount * ($commRate / 100.0), 2);
        $vendorGuaranteedPayout = round(max(0, $amount - $commAmount), 2);

        return response()->json([
            'success' => true,
            'data'    => [
                'booking_amount'           => $amount,
                'earnable_points'          => $earnablePoints,
                'user_available_points'    => $availablePoints,
                'max_redeemable_bdt'       => $maxRedeemableBdt,
                'commission_rate'          => $commRate,
                'platform_commission'      => $commAmount,
                'vendor_guaranteed_payout' => $vendorGuaranteedPayout,
                'vendor_protected'         => true,
            ],
        ]);
    }

    /**
     * GET /api/v1/accounts/vendor/tax-certificate
     * Monthly NBR Tax & VAT certificate API.
     */
    public function taxCertificate(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $vendorId = ($user->isAdmin() || $user->isSuperAdmin())
            ? (int) ($request->query('vendor_id') ?? $user->id)
            : (int) $user->id;

        $year     = (int) ($request->query('year') ?? date('Y'));
        $month    = (int) ($request->query('month') ?? date('m'));

        $certificate = $this->accountingService->generateMonthlyTaxCertificate($vendorId, $year, $month);

        return response()->json([
            'success'     => true,
            'certificate' => $certificate,
        ]);
    }

    /**
     * GET /api/v1/accounts/analytics/yield
     * Agoda YCS / Booking.com Yield & Revenue Management Analytics (ADR, RevPAR, Occupancy).
     */
    public function yieldAnalytics(Request $request): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user && ($user->isAdmin() || $user->isSuperAdmin());

        $vendorId = $isAdmin
            ? ($request->filled('vendor_id') ? (int) $request->query('vendor_id') : null)
            : (int) ($user?->id ?? 0);

        $propertyId = $request->filled('property_id') ? (int) $request->query('property_id') : null;

        // If non-admin vendor requests specific property, verify ownership
        if (!$isAdmin && $propertyId) {
            $belongs = \App\Models\Property::where('id', $propertyId)->where('vendor_id', $vendorId)->exists();
            if (!$belongs) {
                return response()->json(['success' => false, 'message' => 'Unauthorized property analytics request.'], 403);
            }
        }

        $yieldService = app(\App\Services\YieldAnalyticsService::class);
        $metrics = $yieldService->getPropertyYieldMetrics($propertyId, $vendorId);

        return response()->json([
            'success' => true,
            'data'    => $metrics,
        ]);
    }
}
