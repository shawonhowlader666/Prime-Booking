<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorSubscription;

class SubscriptionController extends Controller
{
    private function vendorId(): int
    {
        $id = auth()->id();
        if (! $id) {
            abort(401, 'Unauthorized vendor session.');
        }
        return (int) $id;
    }

    public function index()
    {
        $company  = config('company');
        $vendorId = $this->vendorId();

        $plans = [
            [
                'id' => 'starter',
                'name' => 'Starter Partner',
                'price' => 0,
                'commission' => '12%',
                'color' => '#8c8c8c',
                'max_props' => 3,
                'placement' => 'Standard Search Position',
                'analytics' => 'Basic Weekly Reports',
                'support' => 'Email Support',
                'features' => ['Up to 3 Property Listings', 'Standard Search Placement', 'bKash/Nagad Payouts', 'Email Support'],
                'popular' => false,
            ],
            [
                'id' => 'pro',
                'name' => 'Pro Partner',
                'price' => 2500,
                'commission' => '8%',
                'color' => '#1890ff',
                'max_props' => 15,
                'placement' => 'Agoda Preferred #1 Badge',
                'analytics' => 'Real-Time Revenue Analytics',
                'support' => '24/7 Phone & Chat Support',
                'features' => ['Up to 15 Property Listings', 'Agoda Preferred Badge', 'Featured Search Priority', 'Channel Manager API Key', '24/7 Dedicated Manager'],
                'popular' => true,
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise SaaS',
                'price' => 7500,
                'commission' => '5%',
                'color' => '#722ed1',
                'max_props' => -1,
                'placement' => 'Global Top Ranked Placement',
                'analytics' => 'Full BI & CSV Export Suite',
                'support' => 'Dedicated Account Executive',
                'features' => ['Unlimited Property Listings', '0% Extra Booking Fee', 'White-Label Guest Voucher', 'Custom Currency Rules', 'VIP API Integration'],
                'popular' => false,
            ],
        ];

        $currentSubscription = VendorSubscription::where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->latest()
            ->first();

        return view('vendor.plans', compact('company', 'plans', 'currentSubscription'));
    }

    public function selectPlan(Request $request)
    {
        $catalog = [
            'Starter Partner' => 0,
            'Pro Partner'     => 2500,
            'Enterprise SaaS' => 7500,
        ];

        $validated = $request->validate([
            'plan_name'     => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys($catalog))],
            'billing_cycle' => 'nullable|in:monthly,yearly',
        ]);

        $vendorId = $this->vendorId();
        $cycle    = $validated['billing_cycle'] ?? 'monthly';
        $price    = $catalog[$validated['plan_name']] * ($cycle === 'yearly' ? 12 : 1);

        // Paid plans start as "pending" and are activated by an administrator once the fee is received.
        $status = $price > 0 ? 'pending' : 'active';

        \Illuminate\Support\Facades\DB::transaction(function () use ($vendorId, $validated, $price, $cycle, $status) {
            if ($status === 'active') {
                VendorSubscription::where('vendor_id', $vendorId)->where('status', 'active')->update(['status' => 'cancelled']);
            }
            VendorSubscription::where('vendor_id', $vendorId)->where('status', 'pending')->update(['status' => 'cancelled']);

            VendorSubscription::create([
                'vendor_id'     => $vendorId,
                'plan_name'     => $validated['plan_name'],
                'price'         => $price,
                'billing_cycle' => $cycle,
                'starts_at'     => now(),
                'ends_at'       => $cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'status'        => $status,
            ]);
        });

        if ($status === 'pending') {
            return redirect()->back()->with('success', $validated['plan_name'] . ' requested. Our partner team will contact you to complete payment and activate the plan.');
        }

        return redirect()->back()->with('success', 'SaaS Partner Subscription upgraded to ' . $validated['plan_name'] . ' successfully!');
    }
}

