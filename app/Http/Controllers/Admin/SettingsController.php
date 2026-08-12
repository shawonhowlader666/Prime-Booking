<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siteSettings = [
            // General & Company
            'site_name'                 => SiteSetting::get('site_name', 'PRIME BOOKING'),
            'site_tagline'              => SiteSetting::get('site_tagline', "Bangladesh's #1 Hotel & Flight Platform"),
            'primary_color'             => SiteSetting::get('primary_color', '#1890ff'),
            'default_currency'          => SiteSetting::get('currency', 'BDT'),
            'support_phone'             => SiteSetting::get('support_phone', '+880 1700 000000'),
            'support_email'             => SiteSetting::get('support_email', 'support@primeaviation.com'),
            'support_address'           => SiteSetting::get('support_address', 'Gulshan-2, Dhaka-1212, Bangladesh'),
            'maintenance_mode'          => SiteSetting::get('maintenance_mode', '0'),
            'new_registrations'         => SiteSetting::get('new_registrations', '1'),

            // Booking & Tax
            'platform_commission'       => SiteSetting::get('platform_commission', '12'),
            'tax_rate'                  => SiteSetting::get('tax_rate', '7.5'),
            'min_booking_nights'        => SiteSetting::get('min_booking_nights', '1'),
            'max_booking_nights'        => SiteSetting::get('max_booking_nights', '60'),
            'max_guests_per_booking'    => SiteSetting::get('max_guests_per_booking', '8'),
            'cancellation_free_hours'   => SiteSetting::get('cancellation_free_hours', '24'),

            // VIP Tiers
            'vip_silver_threshold'     => SiteSetting::get('vip_silver_threshold', '2'),
            'vip_gold_threshold'       => SiteSetting::get('vip_gold_threshold', '5'),
            'vip_platinum_threshold'   => SiteSetting::get('vip_platinum_threshold', '10'),
            'vip_diamond_threshold'    => SiteSetting::get('vip_diamond_threshold', '15'),
            'vip_silver_discount'      => SiteSetting::get('vip_silver_discount', '5'),
            'vip_gold_discount'        => SiteSetting::get('vip_gold_discount', '8'),
            'vip_platinum_discount'    => SiteSetting::get('vip_platinum_discount', '12'),
            'vip_diamond_discount'     => SiteSetting::get('vip_diamond_discount', '18'),

            // Payments
            'enable_bkash'              => SiteSetting::get('payment_bkash_enabled', '1'),
            'enable_nagad'              => SiteSetting::get('payment_nagad_enabled', '1'),
            'enable_card'               => SiteSetting::get('payment_card_enabled', '1'),
            'enable_stripe'             => SiteSetting::get('payment_stripe_enabled', '0'),
            'enable_paypal'             => SiteSetting::get('payment_paypal_enabled', '0'),

            // SMTP
            'mail_host'                 => SiteSetting::get('mail_host', 'smtp.mailtrap.io'),
            'mail_port'                 => SiteSetting::get('mail_port', '2525'),
            'mail_username'             => SiteSetting::get('mail_username', ''),
            'mail_password'             => SiteSetting::get('mail_password', ''),
            'mail_encryption'           => SiteSetting::get('mail_encryption', 'tls'),
            'mail_from_name'            => SiteSetting::get('mail_from_name', 'Prime Booking'),
            'mail_from_address'         => SiteSetting::get('mail_from_address', 'noreply@primebooking.com.bd'),
        ];

        return view('admin.settings', compact('user', 'siteSettings'));
    }

    public function update(Request $request)
    {
        // 1. Profile updates
        $user = auth()->user();
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email') && $request->email !== $user->email) {
            $request->validate(['email' => 'required|email|unique:users,email,' . $user->id]);
            $user->email = $request->email;
        }
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->filled('new_password')) {
            $request->validate(['new_password' => 'required|string|min:8']);
            $user->password = Hash::make($request->new_password);
        }
        $user->save();

        // 2. All SiteSettings
        $keys = [
            'site_name', 'site_tagline', 'primary_color', 'support_phone', 'support_email', 'support_address',
            'platform_commission', 'tax_rate', 'min_booking_nights', 'max_booking_nights',
            'max_guests_per_booking', 'cancellation_free_hours',
            'vip_silver_threshold', 'vip_gold_threshold', 'vip_platinum_threshold', 'vip_diamond_threshold',
            'vip_silver_discount', 'vip_gold_discount', 'vip_platinum_discount', 'vip_diamond_discount',
            'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption',
            'mail_from_name', 'mail_from_address'
        ];

        foreach ($keys as $k) {
            if ($request->has($k)) {
                SiteSetting::set($k, $request->input($k));
            }
        }

        if ($request->has('default_currency')) {
            SiteSetting::set('currency', $request->default_currency);
        }

        // Toggles
        SiteSetting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        SiteSetting::set('new_registrations', $request->has('new_registrations') ? '1' : '0');
        SiteSetting::set('payment_bkash_enabled', $request->has('enable_bkash') ? '1' : '0');
        SiteSetting::set('payment_nagad_enabled', $request->has('enable_nagad') ? '1' : '0');
        SiteSetting::set('payment_card_enabled', $request->has('enable_card') ? '1' : '0');
        SiteSetting::set('payment_stripe_enabled', $request->has('enable_stripe') ? '1' : '0');
        SiteSetting::set('payment_paypal_enabled', $request->has('enable_paypal') ? '1' : '0');

        Cache::flush();

        return redirect()->back()->with('success', 'Stockifly SaaS Settings & System Control Parameters updated successfully!');
    }
}
