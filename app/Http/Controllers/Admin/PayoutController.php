<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        // Seed default sample payouts if DB table is empty
        if (Payout::count() === 0) {
            $vendor = User::where('role', 'vendor')->first();
            Payout::create([
                'vendor_id'        => $vendor?->id,
                'vendor_name'      => 'Ocean Paradise Resort & Spa',
                'amount'           => 145000.00,
                'payment_method'   => 'bKash Merchant',
                'account_details'  => '01770887733',
                'reference_number' => 'TRX-BK-998822',
                'status'           => 'paid',
                'notes'            => 'Monthly hotel settlement payout via bKash',
            ]);
            Payout::create([
                'vendor_id'        => $vendor?->id,
                'vendor_name'      => 'Sundarban Cruise Line Ltd',
                'amount'           => 120000.00,
                'payment_method'   => 'Dutch Bangla Bank (DBBL)',
                'account_details'  => 'A/C: 124.110.45892 (Gulshan Branch)',
                'reference_number' => '',
                'status'           => 'pending',
                'notes'            => 'Cruise ship booking payout request',
            ]);
            Payout::create([
                'vendor_id'        => $vendor?->id,
                'vendor_name'      => 'Sajek Eco Cottages Group',
                'amount'           => 65000.00,
                'payment_method'   => 'Nagad Personal',
                'account_details'  => '01819887766',
                'reference_number' => 'NG-TX-445511',
                'status'           => 'paid',
                'notes'            => 'Weekly eco-resort settlement payout',
            ]);
        }

        $query = Payout::with('vendor')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payouts = $query->paginate(15)->withQueryString();

        return view('admin.payouts.index', compact('payouts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name'      => 'required|string|max:255',
            'amount'           => 'required|numeric|min:1',
            'payment_method'   => 'required|string|max:100',
            'account_details'  => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        Payout::create($validated + ['status' => 'pending']);

        return back()->with('success', 'Direct Vendor Payout request recorded successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:pending,paid,rejected',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $payout = Payout::findOrFail($id);
        $oldStatus = $payout->status;
        $payout->update([
            'status'           => $request->status,
            'reference_number' => $request->reference_number ?? $payout->reference_number,
        ]);

        // Auto-record payout debit transaction in general ledger
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            app(\App\Services\AccountingService::class)->recordManualEntry([
                'txn_reference'     => $request->reference_number ?: ('TXN-PO-' . strtoupper(\Illuminate\Support\Str::random(8))),
                'type'              => 'payout',
                'category'          => 'vendor_settlement',
                'vendor_id'         => $payout->vendor_id,
                'gross_amount'      => (float) $payout->amount,
                'commission_amount' => 0.00,
                'gateway_fee'       => 0.00,
                'net_amount'        => (float) $payout->amount,
                'payment_method'    => strtolower($payout->payment_method ?? 'bank_transfer'),
                'description'       => "Disbursed Vendor Settlement Payout #{$payout->id} to {$payout->vendor_name}",
                'notes'             => "Account: {$payout->account_details} | Ref: " . ($request->reference_number ?? $payout->reference_number),
            ]);
        }

        return back()->with('success', "Payout #{$payout->id} status updated to " . strtoupper($request->status) . " and ledger synced successfully!");
    }
}
