<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorSubscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $company  = config('company');
        $vendorId = auth()->id() ?? 1;

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
        $validated = $request->validate([
            'plan_name'     => 'required|string',
            'price'         => 'required|numeric',
            'billing_cycle' => 'nullable|in:monthly,yearly',
        ]);

        $vendorId = auth()->id() ?? 1;

        // Cancel previous active subscriptions
        VendorSubscription::where('vendor_id', $vendorId)->update(['status' => 'cancelled']);

        // Create new active subscription
        VendorSubscription::create([
            'vendor_id'     => $vendorId,
            'plan_name'     => $validated['plan_name'],
            'price'         => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'] ?? 'monthly',
            'starts_at'     => now(),
            'ends_at'       => now()->addMonth(),
            'status'        => 'active',
        ]);

        return redirect()->back()->with('success', 'SaaS Partner Subscription upgraded to ' . $validated['plan_name'] . ' successfully!');
    }
}

