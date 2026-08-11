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
                'features' => ['Up to 3 Property Listings', 'Standard Search Placement', 'bKash/Nagad Payouts', 'Email Support'],
                'popular' => false,
            ],
            [
                'id' => 'pro',
                'name' => 'Pro Partner',
                'price' => 2500,
                'commission' => '8%',
                'features' => ['Unlimited Property Listings', 'Agoda Preferred Badge', 'Featured Search Priority', 'Channel Manager API Key', '24/7 Dedicated Manager'],
                'popular' => true,
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise SaaS',
                'price' => 7500,
                'commission' => '5%',
                'features' => ['Custom Dedicated Subdomain', '0% Extra Booking Fee', 'White-Label Guest Voucher', 'Custom Currency Rules', 'VIP API Integration'],
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

