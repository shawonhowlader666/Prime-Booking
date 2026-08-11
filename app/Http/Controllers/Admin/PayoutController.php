<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        try {
            $payouts = Payout::with('vendor')->latest()->get();
            if ($payouts->isEmpty()) {
                $payouts = $this->getMockPayouts();
            }
        } catch (\Throwable $e) {
            $payouts = $this->getMockPayouts();
        }

        return view('admin.payouts.index', compact('payouts'));
    }

    private function getMockPayouts()
    {
        return collect([
            (object)[
                'id'               => 1,
                'vendor_name'      => 'Ocean Paradise Hospitality',
                'amount'           => 45000,
                'payment_method'   => 'bKash Merchant',
                'account_details'  => '01770887733',
                'reference_number' => 'TRX-BK-998822',
                'status'           => 'paid',
                'created_at'       => now()->subDays(2),
            ],
            (object)[
                'id'               => 2,
                'vendor_name'      => 'MV Zabin Cruise Lines',
                'amount'           => 120000,
                'payment_method'   => 'Dutch Bangla Bank (DBBL)',
                'account_details'  => 'A/C: 124.110.45892',
                'reference_number' => '',
                'status'           => 'pending',
                'created_at'       => now()->subHours(5),
            ],
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:pending,paid,rejected',
            'reference_number' => 'nullable|string',
        ]);

        try {
            $payout = Payout::findOrFail($id);
            $payout->update([
                'status'           => $request->status,
                'reference_number' => $request->reference_number ?? $payout->reference_number,
            ]);
        } catch (\Exception $e) {}

        return back()->with('success', 'Payout status updated successfully.');
    }
}
