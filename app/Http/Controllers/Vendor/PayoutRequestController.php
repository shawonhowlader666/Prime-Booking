<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payout;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayoutRequestController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Show vendor's financial ledger and payout request history.
     * GET /vendor/payouts
     */
    public function index(): View
    {
        $vendorId = (int) auth()->id();

        // Exact Double-Entry Fintech Engine Calculation (Matches Admin Ledger perfectly)
        $acc = $this->accountingService->getSingleVendorAccounting($vendorId);

        $totalRevenue       = (float) $acc['gross_revenue'];
        $platformCommission = (float) $acc['commission_paid'];
        $netEarnings        = (float) $acc['net_earnings'];
        $totalPaidOut       = (float) $acc['payouts_paid'];
        $totalPending       = (float) $acc['payouts_pending'];
        $availableBalance   = (float) $acc['withdrawable_balance'];

        $payouts = Payout::where('vendor_id', $vendorId)
            ->latest()
            ->paginate(10);

        return view('vendor.payouts.index', compact(
            'totalRevenue',
            'platformCommission',
            'netEarnings',
            'totalPaidOut',
            'totalPending',
            'availableBalance',
            'payouts'
        ));
    }

    /**
     * Submit a new withdrawal payout request.
     * POST /vendor/payouts
     */
    public function store(Request $request): RedirectResponse
    {
        $vendorId = (int) auth()->id();

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:500',
            'payout_method'  => 'required|in:bkash,bank_transfer,nagad,rocket',
            'account_details'=> 'required|string|max:255',
        ]);

        // Re-verify available balance through AccountingService (Zero-Paisa Discrepancy Guarantee)
        $acc = $this->accountingService->getSingleVendorAccounting($vendorId);
        $availableBalance = (float) $acc['withdrawable_balance'];

        if ($validated['amount'] > $availableBalance) {
            return back()->with('error', "Requested amount (৳{$validated['amount']}) exceeds your verified available balance (৳{$availableBalance}) for withdrawal.");
        }

        $user = auth()->user();
        DB::transaction(function () use ($vendorId, $user, $validated) {
            Payout::create([
                'vendor_id'       => $vendorId,
                'vendor_name'     => $user->name ?? 'Vendor Partner',
                'amount'          => $validated['amount'],
                'payment_method'  => $validated['payout_method'],
                'account_details' => $validated['account_details'],
                'account_number'  => $validated['account_details'],
                'status'          => 'pending',
                'requested_at'    => now(),
            ]);
        });

        return redirect()->route('vendor.payouts.index')->with('success', 'Payout withdrawal request submitted successfully! Admin will process within 24 hours.');
    }

    /**
     * Export vendor booking earnings statement as CSV.
     * GET /vendor/earnings/export
     */
    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $vendorId = auth()->id();
        $bookings = Booking::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->with(['property:id,name', 'room:id,name'])
            ->latest()
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendor_earnings_report_' . date('Y_m_d') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Booking Reference', 'Property', 'Room', 'Guest Name', 'Check In', 'Check Out', 'Total BDT', 'Status', 'Booking Date']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_reference,
                    $b->property->name ?? 'Property',
                    $b->room->name ?? 'Standard',
                    $b->guest_name,
                    $b->check_in,
                    $b->check_out,
                    $b->total_price,
                    $b->booking_status,
                    $b->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
