<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PayoutRequestController extends Controller
{
    /**
     * Show vendor's financial ledger and payout request history.
     * GET /vendor/payouts
     */
    public function index(): View
    {
        $vendorId = auth()->id();

        // Calculate total earnings from completed/confirmed bookings for vendor's properties
        $totalRevenue = Booking::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->sum('total_price');

        // Platform commission 10%
        $commissionRate = 0.10;
        $platformCommission = round($totalRevenue * $commissionRate, 2);
        $netEarnings = $totalRevenue - $platformCommission;

        // Total paid out so far
        $totalPaidOut = Payout::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->sum('amount');

        // Pending payouts
        $totalPending = Payout::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = max(0, $netEarnings - $totalPaidOut - $totalPending);

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
        $vendorId = auth()->id();

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:500',
            'payout_method'  => 'required|in:bkash,bank_transfer,nagad,rocket',
            'account_details'=> 'required|string|max:255',
        ]);

        // Re-verify available balance
        $totalRevenue = Booking::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->sum('total_price');

        $netEarnings = $totalRevenue * 0.90;
        $totalPaidOut = Payout::where('vendor_id', $vendorId)->where('status', 'paid')->sum('amount');
        $totalPending = Payout::where('vendor_id', $vendorId)->where('status', 'pending')->sum('amount');
        $availableBalance = max(0, $netEarnings - $totalPaidOut - $totalPending);

        if ($validated['amount'] > $availableBalance) {
            return back()->with('error', 'Requested amount exceeds your available balance for withdrawal.');
        }

        Payout::create([
            'vendor_id'       => $vendorId,
            'amount'          => $validated['amount'],
            'payment_method'  => $validated['payout_method'],
            'account_number'  => $validated['account_details'],
            'status'          => 'pending',
            'requested_at'    => now(),
        ]);

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
