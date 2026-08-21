<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountingLedger;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AccountingService
{
    /**
     * Get Master Financial Overview KPIs with single-pass SQL aggregation & caching
     */
    public function getOverviewKPIs(?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = 'finance_kpis_' . ($startDate ?? 'all') . '_' . ($endDate ?? 'all');

        return Cache::remember($cacheKey, 60, function () use ($startDate, $endDate) {
            $bookingQuery = Booking::query();

            if ($startDate && $endDate) {
                $bookingQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // Single-pass indexed SQL aggregation
            $stats = $bookingQuery->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price ELSE 0 END) as gross_booking_value,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price * 0.12 ELSE 0 END) as platform_commission,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price * 0.88 ELSE 0 END) as vendor_payable,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') AND payment_method IN ('bkash', 'nagad') THEN total_price * 0.015 
                         WHEN status NOT IN ('cancelled', 'refunded') AND payment_method IN ('card', 'sslcommerz') THEN total_price * 0.02 
                         ELSE 0 END) as gateway_fees,
                SUM(CASE WHEN status = 'refunded' THEN total_price ELSE 0 END) as total_refunded
            ")->first();

            $payoutsQuery = Payout::query();
            if ($startDate && $endDate) {
                $payoutsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $totalSettledPayouts = (float) $payoutsQuery->where('status', 'completed')->sum('amount');
            $pendingPayouts      = (float) $payoutsQuery->where('status', 'pending')->sum('amount');

            $gbv            = (float) ($stats->gross_booking_value ?? 0);
            $commission     = (float) ($stats->platform_commission ?? 0);
            $vendorPayable  = (float) ($stats->vendor_payable ?? 0);
            $gatewayFees    = (float) ($stats->gateway_fees ?? 0);
            $refunds        = (float) ($stats->total_refunded ?? 0);
            $netProfit      = max(0, $commission - $gatewayFees);
            $escrowVault    = max(0, $gbv - $totalSettledPayouts - $refunds);

            return [
                'total_orders'           => (int) ($stats->total_orders ?? 0),
                'gross_booking_value'    => $gbv,
                'platform_commission'    => $commission,
                'vendor_payable'         => $vendorPayable,
                'gateway_fees'           => $gatewayFees,
                'total_refunded'         => $refunds,
                'net_profit'             => $netProfit,
                'total_settled_payouts'  => $totalSettledPayouts,
                'pending_payouts'        => $pendingPayouts,
                'escrow_vault_balance'   => $escrowVault,
                'escrow_holding_pool'    => $escrowVault,
            ];
        });
    }

    /**
     * Get 12-Month Profit & Loss (P&L) Data for Interactive Charts
     */
    public function getMonthlyPnLChartData(int $year): array
    {
        $cacheKey = "finance_pnl_chart_{$year}";

        return Cache::remember($cacheKey, 120, function () use ($year) {
            $months = [];
            $revenue = [];
            $commission = [];
            $payouts = [];

            for ($m = 1; $m <= 12; $m++) {
                $monthStr = date('M', mktime(0, 0, 0, $m, 1));
                $start = sprintf('%04d-%02d-01 00:00:00', $year, $m);
                $end   = date('Y-m-t 23:59:59', strtotime($start));

                $monthBooking = Booking::whereBetween('created_at', [$start, $end])
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->selectRaw("
                        SUM(total_price) as gross,
                        SUM(total_price * 0.12) as comm
                    ")->first();

                $monthPayout = (float) Payout::whereBetween('created_at', [$start, $end])
                    ->where('status', 'completed')
                    ->sum('amount');

                $months[]     = $monthStr;
                $revenue[]    = (float) ($monthBooking->gross ?? 0);
                $commission[] = (float) ($monthBooking->comm ?? 0);
                $payouts[]    = $monthPayout;
            }

            return [
                'months'      => $months,
                'revenue'     => $revenue,
                'commission'  => $commission,
                'payouts'     => $payouts,
            ];
        });
    }

    /**
     * Get Detailed Vendor Financial Statements
     */
    public function getVendorStatements(?int $vendorId = null, int $perPage = 25)
    {
        $query = User::where('role', 'vendor')->withCount('properties');

        if ($vendorId) {
            $query->where('id', $vendorId);
        }

        $vendors = $query->paginate($perPage);

        foreach ($vendors as $v) {
            // Find all bookings for this vendor's properties
            $propIds = Property::where('vendor_id', $v->id)->pluck('id');
            
            $stats = Booking::whereIn('property_id', $propIds)
                ->selectRaw("
                    COUNT(*) as total_sales,
                    SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price ELSE 0 END) as gross_sales,
                    SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price * 0.12 ELSE 0 END) as commission_deducted,
                    SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price * 0.88 ELSE 0 END) as net_payable
                ")->first();

            $payoutsPaid = (float) Payout::where('vendor_id', $v->id)->where('status', 'completed')->sum('amount');
            $payoutsPending = (float) Payout::where('vendor_id', $v->id)->where('status', 'pending')->sum('amount');

            $grossSales  = (float) ($stats->gross_sales ?? 0);
            $commission  = (float) ($stats->commission_deducted ?? 0);
            $netPayable  = (float) ($stats->net_payable ?? 0);
            $currentDue  = max(0, $netPayable - $payoutsPaid);

            $v->finance_stats = (object) [
                'total_bookings'      => (int) ($stats->total_sales ?? 0),
                'gross_sales'         => $grossSales,
                'commission_deducted' => $commission,
                'net_payable'         => $netPayable,
                'payouts_paid'        => $payoutsPaid,
                'payouts_pending'     => $payoutsPending,
                'available_balance'   => $currentDue,
            ];
        }

        return $vendors;
    }

    /**
     * Get Single Vendor Accounting Hub Profile
     */
    public function getSingleVendorAccounting(int $vendorId): array
    {
        $propIds = Property::where('vendor_id', $vendorId)->pluck('id');

        $stats = Booking::whereIn('property_id', $propIds)
            ->selectRaw("
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price ELSE 0 END) as gross_revenue,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price * 0.12 ELSE 0 END) as commission_paid,
                SUM(CASE WHEN status NOT IN ('cancelled', 'refunded') THEN total_price * 0.88 ELSE 0 END) as net_earnings
            ")->first();

        $payoutsPaid    = (float) Payout::where('vendor_id', $vendorId)->where('status', 'completed')->sum('amount');
        $payoutsPending = (float) Payout::where('vendor_id', $vendorId)->where('status', 'pending')->sum('amount');

        $gross       = (float) ($stats->gross_revenue ?? 0);
        $commission  = (float) ($stats->commission_paid ?? 0);
        $netEarnings = (float) ($stats->net_earnings ?? 0);
        $withdrawable= max(0, $netEarnings - $payoutsPaid);

        return [
            'total_bookings'      => (int) ($stats->total_bookings ?? 0),
            'gross_revenue'       => $gross,
            'commission_paid'     => $commission,
            'net_earnings'        => $netEarnings,
            'payouts_paid'        => $payoutsPaid,
            'payouts_pending'     => $payoutsPending,
            'withdrawable_balance'=> $withdrawable,
        ];
    }

    /**
     * Query General Ledger with multi-filtering
     */
    public function getGeneralLedger(array $filters = [], int $perPage = 25)
    {
        $this->ensureLedgerPopulated();

        $query = AccountingLedger::with(['booking', 'vendor', 'property', 'user'])->latest('id');

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['payment_method']) && $filters['payment_method'] !== 'all') {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'] . ' 00:00:00', $filters['end_date'] . ' 23:59:59']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(fn($q) => $q
                ->where('txn_reference', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")
            );
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Real-time double-entry ledger entry generator for newly created or completed bookings
     */
    public static function recordBookingLedger(Booking $b, string $paymentMethod = 'bkash'): AccountingLedger
    {
        $gross = (float) ($b->total_price ?? $b->amount ?? 0);
        $comm  = round($gross * 0.12, 2);
        $fee   = in_array($paymentMethod, ['bkash', 'nagad']) ? round($gross * 0.015, 2) : round($gross * 0.02, 2);
        $net   = round($gross - $comm - $fee, 2);

        return AccountingLedger::create([
            'txn_reference'     => 'TXN-BK-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'type'              => 'credit',
            'category'          => 'hotel_booking',
            'booking_id'        => $b->id,
            'vendor_id'         => $b->property?->vendor_id,
            'property_id'       => $b->property_id,
            'user_id'           => $b->user_id,
            'gross_amount'      => $gross,
            'commission_amount' => $comm,
            'gateway_fee'       => $fee,
            'net_amount'        => $net,
            'payment_method'    => $paymentMethod,
            'currency'          => 'BDT',
            'status'            => $b->status === 'cancelled' ? 'cancelled' : 'completed',
            'description'       => "Booking Reference #{$b->booking_reference} for {$b->property?->name}",
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Record Refund / Cancellation in Double-Entry General Ledger
     */
    public static function recordRefundLedger(Booking $b, ?float $amount = null, string $reason = 'Booking Cancelled / Refunded'): AccountingLedger
    {
        $refundAmount = $amount ?? (float) ($b->total_price ?? $b->amount ?? 0);
        $vendorId = $b->property?->vendor_id;

        // Invalidate finance caches
        \Illuminate\Support\Facades\Cache::forget('finance_overview_kpis_all');
        if ($vendorId) {
            \Illuminate\Support\Facades\Cache::forget("vendor_finance_{$vendorId}");
        }

        return AccountingLedger::create([
            'txn_reference'     => 'TXN-RF-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'type'              => 'refund',
            'category'          => 'booking_refund',
            'booking_id'        => $b->id,
            'vendor_id'         => $vendorId,
            'property_id'       => $b->property_id,
            'user_id'           => $b->user_id,
            'gross_amount'      => $refundAmount,
            'commission_amount' => round($refundAmount * 0.12, 2),
            'gateway_fee'       => 0.00,
            'net_amount'        => round($refundAmount * 0.88, 2),
            'payment_method'    => $b->payment_method ?? 'bkash',
            'currency'          => 'BDT',
            'status'            => 'completed',
            'description'       => "Refund: {$reason} (Ref #{$b->booking_reference})",
            'metadata'          => [
                'refund_reason' => $reason,
                'initiated_by'  => auth()->user()?->name ?? 'Administrator',
            ],
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Record Manual Transaction / Commission Adjustment / Direct Bank or Cash Deposit
     */
    public function recordManualEntry(array $data): AccountingLedger
    {
        $gross = (float) ($data['gross_amount'] ?? 0);
        $comm  = (float) ($data['commission_amount'] ?? 0);
        $fee   = (float) ($data['gateway_fee'] ?? 0);
        $net   = (float) ($data['net_amount'] ?? ($gross - $comm - $fee));
        
        $type = $data['type'] ?? 'credit';
        $category = $data['category'] ?? 'manual_adjustment';
        $vendorId = !empty($data['vendor_id']) ? (int) $data['vendor_id'] : null;

        // Clear dashboard finance caches
        \Illuminate\Support\Facades\Cache::forget('finance_overview_kpis_all');
        if ($vendorId) {
            \Illuminate\Support\Facades\Cache::forget("vendor_finance_{$vendorId}");
        }

        return AccountingLedger::create([
            'txn_reference'     => !empty($data['txn_reference']) ? $data['txn_reference'] : ('TXN-MAN-' . strtoupper(\Illuminate\Support\Str::random(8))),
            'type'              => $type,
            'category'          => $category,
            'booking_id'        => !empty($data['booking_id']) ? (int) $data['booking_id'] : null,
            'vendor_id'         => $vendorId,
            'property_id'       => !empty($data['property_id']) ? (int) $data['property_id'] : null,
            'user_id'           => !empty($data['user_id']) ? (int) $data['user_id'] : auth()->id(),
            'gross_amount'      => $gross,
            'commission_amount' => $comm,
            'gateway_fee'       => $fee,
            'net_amount'        => $net,
            'payment_method'    => $data['payment_method'] ?? 'cash',
            'currency'          => 'BDT',
            'status'            => $data['status'] ?? 'completed',
            'description'       => $data['description'] ?? 'Manual ledger adjustment entry',
            'metadata'          => [
                'recorded_by' => auth()->user()?->name ?? 'Administrator',
                'notes'       => $data['notes'] ?? null,
            ],
            'created_at'        => !empty($data['created_at']) ? \Carbon\Carbon::parse($data['created_at']) : now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Static helper for vendor finance summary
     */
    public static function getVendorFinanceSummary(int $vendorId): array
    {
        $service = new self();
        $data = $service->getSingleVendorAccounting($vendorId);
        return [
            'total_bookings'      => $data['total_bookings'] ?? 0,
            'gross_sales'         => $data['gross_revenue'] ?? 0,
            'commission_deducted' => $data['commission_paid'] ?? 0,
            'net_payable'         => $data['net_earnings'] ?? 0,
            'payouts_paid'        => $data['payouts_paid'] ?? 0,
            'payouts_pending'     => $data['payouts_pending'] ?? 0,
            'available_balance'   => $data['withdrawable_balance'] ?? 0,
        ];
    }

    /**
     * Ensure historical bookings are mirrored in the double-entry accounting ledger
     */
    public function ensureLedgerPopulated(): void
    {
        $count = AccountingLedger::count();
        if ($count > 0) {
            return;
        }

        $bookings = Booking::with('property')->get();
        foreach ($bookings as $b) {
            $gross = (float) ($b->total_price ?? $b->amount ?? 0);
            $comm  = round($gross * 0.12, 2);
            $fee   = in_array($b->payment_method, ['bkash', 'nagad']) ? round($gross * 0.015, 2) : round($gross * 0.02, 2);
            $net   = max(0, $gross - $comm - $fee);

            AccountingLedger::create([
                'txn_reference'     => 'TXN-BK-' . str_pad((string)$b->id, 6, '0', STR_PAD_LEFT),
                'type'              => 'credit',
                'category'          => 'hotel_booking',
                'booking_id'        => $b->id,
                'vendor_id'         => $b->property?->vendor_id,
                'property_id'       => $b->property_id,
                'user_id'           => $b->user_id,
                'gross_amount'      => $gross,
                'commission_amount' => $comm,
                'gateway_fee'       => $fee,
                'net_amount'        => $net,
                'payment_method'    => $b->payment_method ?? 'bkash',
                'currency'          => 'BDT',
                'status'            => $b->status === 'cancelled' ? 'cancelled' : 'completed',
                'description'       => "Booking Reference #{$b->booking_reference} for " . ($b->property?->name ?? 'Hotel Booking'),
                'created_at'        => $b->created_at ?? now(),
                'updated_at'        => $b->updated_at ?? now(),
            ]);
        }
    }
}

