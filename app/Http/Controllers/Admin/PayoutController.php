<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
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

        $payouts = $query->paginate(per_page(15))->withQueryString();

        return view('admin.payouts.index', compact('payouts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id'        => ['nullable', 'integer', \Illuminate\Validation\Rule::exists('users', 'id')->where('role', 'vendor')],
            'vendor_name'      => 'required|string|max:255',
            'amount'           => 'required|numeric|min:1|max:99999999',
            'payment_method'   => 'required|string|max:100',
            'account_details'  => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:2000',
        ]);

        Payout::create($validated + ['status' => 'pending']);

        return back()->with('success', 'Direct Vendor Payout request recorded successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:pending,paid,rejected',
        ];

        // Reference number is MANDATORY when approving a payout — prevents undocumented transfers
        if ($request->status === 'paid') {
            $rules['reference_number'] = 'required|string|min:5|max:100';
        } else {
            $rules['reference_number'] = 'nullable|string|max:100';
        }

        $request->validate($rules);

        $payout = Payout::findOrFail($id);
        $oldStatus = $payout->status;

        // A payout is settled exactly once. Re-opening a paid or rejected payout would let the
        // same money be recorded in the ledger twice.
        if ($oldStatus !== 'pending') {
            return back()->with('error', "Payout #{$payout->id} is already " . strtoupper((string) $oldStatus) . ' and cannot be changed.');
        }
        if ($request->status === 'pending') {
            return back();
        }

        $updateData = [
            'status'           => $request->status,
            'reference_number' => $request->reference_number ?? $payout->reference_number,
        ];

        // Track who processed this and when
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            $updateData['processed_by'] = auth()->user()->name ?? 'Administrator';
            $updateData['processed_at'] = now();
        }

        DB::transaction(function () use ($payout, $updateData, $request, $oldStatus) {
            // Re-read under a lock so two admins clicking "paid" together settle it once.
            $locked = Payout::whereKey($payout->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'pending') {
                return;
            }
            $payout->update($updateData);

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
        });

        return back()->with('success', "Payout #{$payout->id} status updated to " . strtoupper($request->status) . " and ledger synced successfully!");
    }
}
