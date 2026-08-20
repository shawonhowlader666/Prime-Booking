<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VIPLoyaltyController extends Controller
{
    /**
     * VIP Loyalty Rules, Thresholds & Discounts Configuration View
     */
    public function settings()
    {
        $vipSettings = [
            'vip_silver_threshold'     => (int) SiteSetting::get('vip_silver_threshold', '2'),
            'vip_gold_threshold'       => (int) SiteSetting::get('vip_gold_threshold', '5'),
            'vip_gold_spend'           => (float) SiteSetting::get('vip_gold_spend', '200'),
            'vip_platinum_threshold'   => (int) SiteSetting::get('vip_platinum_threshold', '10'),
            'vip_platinum_spend'       => (float) SiteSetting::get('vip_platinum_spend', '400'),
            'vip_diamond_threshold'    => (int) SiteSetting::get('vip_diamond_threshold', '15'),
            'vip_diamond_spend'        => (float) SiteSetting::get('vip_diamond_spend', '1500'),
            
            // Discounts
            'vip_bronze_discount'      => (float) SiteSetting::get('vip_bronze_discount', '0'),
            'vip_silver_discount'      => (float) SiteSetting::get('vip_silver_discount', '12'),
            'vip_gold_discount'        => (float) SiteSetting::get('vip_gold_discount', '18'),
            'vip_platinum_discount'    => (float) SiteSetting::get('vip_platinum_discount', '25'),
            'vip_diamond_discount'     => (float) SiteSetting::get('vip_diamond_discount', '25'),
        ];

        return view('admin.vip.settings', compact('vipSettings'));
    }

    /**
     * Save VIP Rules
     */
    public function updateSettings(Request $request)
    {
        $fields = [
            'vip_silver_threshold', 'vip_gold_threshold', 'vip_gold_spend',
            'vip_platinum_threshold', 'vip_platinum_spend',
            'vip_diamond_threshold', 'vip_diamond_spend',
            'vip_bronze_discount', 'vip_silver_discount',
            'vip_gold_discount', 'vip_platinum_discount', 'vip_diamond_discount'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->input($field));
            }
        }

        // Flush cached thresholds so changes take effect instantly across live site
        Cache::forget('vip_thresholds_settings');
        Cache::forget('vip_discounts_settings');

        return back()->with('success', 'VIP Loyalty Tier Rules & Discounts updated successfully!');
    }

    /**
     * VIP Member Roster with Tier Breakdown & Manual Upgrade
     */
    public function members(Request $request)
    {
        $search = $request->input('search');
        $tierFilter = $request->input('tier');

        $query = User::where('role', 'customer');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        // Fetch aggregate booking stats for paginated users in 1 fast query
        $userIds = $users->pluck('id')->toArray();
        $userStats = Booking::whereIn('user_id', $userIds)
            ->where('created_at', '>=', now()->subYears(2))
            ->whereNotIn('booking_status', ['cancelled'])
            ->groupBy('user_id')
            ->selectRaw("user_id, COUNT(*) as bookings_count, COALESCE(SUM(CASE WHEN payment_status IN ('paid', 'completed') THEN total_amount ELSE 0 END), 0) as total_spend")
            ->get()
            ->keyBy('user_id');

        return view('admin.vip.members', compact('users', 'userStats', 'search', 'tierFilter'));
    }
}
