<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * SiteSettingsController
 * ─────────────────────
 * Admin panel for all platform configuration:
 *  - VIP/Loyalty tier thresholds
 *  - Commission rates
 *  - Booking rules
 *  - Payment options
 *  - Platform info (name, tagline, contact)
 */
class SiteSettingsController extends Controller
{
    public function index()
    {
        $groups = [
            'vip'      => SiteSetting::group('vip'),
            'booking'  => SiteSetting::group('booking'),
            'payment'  => SiteSetting::group('payment'),
            'general'  => SiteSetting::group('general'),
        ];

        return view('admin.settings.index', compact('groups'));
    }

    /** Update all settings at once (grouped form submit) */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value ?? '');
        }

        // Clear all cached settings
        Cache::flush(); // or selectively delete setting keys

        try {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'user_name'   => auth()->user()?->name ?? 'Admin',
                'action'      => 'settings_updated',
                'model_type'  => 'SiteSetting',
                'model_id'    => null,
                'description' => 'Platform settings updated: ' . implode(', ', array_keys($settings)),
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Exception $e) {}

        return back()->with('success', 'Platform settings saved successfully.');
    }

    /** Update a single setting via AJAX */
    public function updateSingle(Request $request)
    {
        $request->validate([
            'key'   => 'required|string',
            'value' => 'nullable|string',
        ]);

        SiteSetting::set($request->key, $request->value ?? '');

        return response()->json([
            'success' => true,
            'message' => "Setting '{$request->key}' updated.",
        ]);
    }

    /** Seed default settings (run once on first install) */
    public function seedDefaults(Request $request)
    {
        $defaults = [
            // ── VIP / Loyalty ─────────────────────────────────────────────
            ['key'=>'vip_silver_threshold',   'group'=>'vip', 'value'=>'2',  'type'=>'number', 'label'=>'Silver Tier (min bookings)', 'description'=>'Min bookings in last 2 years to reach Silver'],
            ['key'=>'vip_gold_threshold',     'group'=>'vip', 'value'=>'5',  'type'=>'number', 'label'=>'Gold Tier (min bookings)'],
            ['key'=>'vip_platinum_threshold', 'group'=>'vip', 'value'=>'10', 'type'=>'number', 'label'=>'Platinum Tier (min bookings)'],
            ['key'=>'vip_diamond_threshold',  'group'=>'vip', 'value'=>'15', 'type'=>'number', 'label'=>'Diamond Tier (min bookings)'],
            ['key'=>'vip_bronze_discount',    'group'=>'vip', 'value'=>'0',  'type'=>'number', 'label'=>'Bronze Discount %'],
            ['key'=>'vip_silver_discount',    'group'=>'vip', 'value'=>'5',  'type'=>'number', 'label'=>'Silver Discount %'],
            ['key'=>'vip_gold_discount',      'group'=>'vip', 'value'=>'8',  'type'=>'number', 'label'=>'Gold Discount %'],
            ['key'=>'vip_platinum_discount',  'group'=>'vip', 'value'=>'12', 'type'=>'number', 'label'=>'Platinum Discount %'],
            ['key'=>'vip_diamond_discount',   'group'=>'vip', 'value'=>'18', 'type'=>'number', 'label'=>'Diamond Discount %'],

            // ── Booking ───────────────────────────────────────────────────
            ['key'=>'platform_commission',         'group'=>'booking', 'value'=>'12',   'type'=>'number', 'label'=>'Platform Commission %'],
            ['key'=>'tax_rate',                    'group'=>'booking', 'value'=>'7.5',  'type'=>'number', 'label'=>'Tax Rate (VAT) %'],
            ['key'=>'min_booking_nights',          'group'=>'booking', 'value'=>'1',    'type'=>'number', 'label'=>'Min Booking Nights'],
            ['key'=>'max_booking_nights',          'group'=>'booking', 'value'=>'60',   'type'=>'number', 'label'=>'Max Booking Nights'],
            ['key'=>'max_guests_per_booking',      'group'=>'booking', 'value'=>'8',    'type'=>'number', 'label'=>'Max Guests per Booking'],
            ['key'=>'advance_booking_days',        'group'=>'booking', 'value'=>'365',  'type'=>'number', 'label'=>'Max Advance Booking Days'],
            ['key'=>'cancellation_free_hours',     'group'=>'booking', 'value'=>'24',   'type'=>'number', 'label'=>'Free Cancellation Window (hours)'],

            // ── Payment ───────────────────────────────────────────────────
            ['key'=>'payment_bkash_enabled',  'group'=>'payment', 'value'=>'1', 'type'=>'boolean', 'label'=>'Enable bKash Payment'],
            ['key'=>'payment_nagad_enabled',  'group'=>'payment', 'value'=>'1', 'type'=>'boolean', 'label'=>'Enable Nagad Payment'],
            ['key'=>'payment_card_enabled',   'group'=>'payment', 'value'=>'1', 'type'=>'boolean', 'label'=>'Enable Card Payment'],
            ['key'=>'payment_paypal_enabled', 'group'=>'payment', 'value'=>'0', 'type'=>'boolean', 'label'=>'Enable PayPal'],
            ['key'=>'payment_stripe_enabled', 'group'=>'payment', 'value'=>'0', 'type'=>'boolean', 'label'=>'Enable Stripe'],
            ['key'=>'currency',               'group'=>'payment', 'value'=>'BDT', 'type'=>'text', 'label'=>'Default Currency'],

            // ── General ───────────────────────────────────────────────────
            ['key'=>'site_name',         'group'=>'general', 'value'=>'Prime Aviation', 'type'=>'text', 'label'=>'Site Name'],
            ['key'=>'site_tagline',      'group'=>'general', 'value'=>'Bangladesh\'s #1 Hotel Booking Platform', 'type'=>'text', 'label'=>'Tagline'],
            ['key'=>'support_email',     'group'=>'general', 'value'=>'support@primeaviation.com', 'type'=>'text', 'label'=>'Support Email'],
            ['key'=>'support_phone',     'group'=>'general', 'value'=>'+880 1700 000000', 'type'=>'text', 'label'=>'Support Phone'],
            ['key'=>'maintenance_mode',  'group'=>'general', 'value'=>'0', 'type'=>'boolean', 'label'=>'Maintenance Mode'],
            ['key'=>'new_registrations', 'group'=>'general', 'value'=>'1', 'type'=>'boolean', 'label'=>'Allow New Registrations'],
        ];

        foreach ($defaults as $setting) {
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting + ['description' => '', 'is_public' => false]
            );
        }

        return back()->with('success', 'Default settings seeded successfully.');
    }
}
