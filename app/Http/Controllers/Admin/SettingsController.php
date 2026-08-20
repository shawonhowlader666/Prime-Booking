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
            'site_logo'                 => SiteSetting::get('site_logo', asset('assets/img/logo.png')),
            'site_favicon'              => SiteSetting::get('site_favicon', asset('favicon.ico')),
            'default_currency'          => SiteSetting::get('currency', 'BDT'),
            'default_language'          => SiteSetting::get('default_language', 'en'),
            'timezone'                  => SiteSetting::get('timezone', 'Asia/Dhaka'),
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

            // Payments & Gateway Toggles
            'enable_bkash'              => SiteSetting::get('payment_bkash_enabled', '1'),
            'enable_nagad'              => SiteSetting::get('payment_nagad_enabled', '1'),
            'enable_card'               => SiteSetting::get('payment_card_enabled', '1'),
            'enable_stripe'             => SiteSetting::get('payment_stripe_enabled', '0'),
            'enable_paypal'             => SiteSetting::get('payment_paypal_enabled', '0'),

            // Payment Merchant API Credentials
            'bkash_app_key'             => SiteSetting::get('bkash_app_key', ''),
            'bkash_app_secret'          => SiteSetting::get('bkash_app_secret', ''),
            'bkash_username'            => SiteSetting::get('bkash_username', ''),
            'bkash_password'            => SiteSetting::get('bkash_password', ''),
            'nagad_merchant_id'         => SiteSetting::get('nagad_merchant_id', ''),
            'nagad_public_key'          => SiteSetting::get('nagad_public_key', ''),
            'sslcommerz_store_id'       => SiteSetting::get('sslcommerz_store_id', ''),
            'sslcommerz_store_passwd'   => SiteSetting::get('sslcommerz_store_passwd', ''),
            'stripe_key'                => SiteSetting::get('stripe_key', ''),
            'stripe_secret'             => SiteSetting::get('stripe_secret', ''),
            'paypal_client_id'          => SiteSetting::get('paypal_client_id', ''),
            'paypal_secret'             => SiteSetting::get('paypal_secret', ''),

            // SMTP Mail Server
            'mail_host'                 => SiteSetting::get('mail_host', 'smtp.mailtrap.io'),
            'mail_port'                 => SiteSetting::get('mail_port', '2525'),
            'mail_username'             => SiteSetting::get('mail_username', ''),
            'mail_password'             => SiteSetting::get('mail_password', ''),
            'mail_encryption'           => SiteSetting::get('mail_encryption', 'tls'),
            'mail_from_name'            => SiteSetting::get('mail_from_name', 'Prime Booking'),
            'mail_from_address'         => SiteSetting::get('mail_from_address', 'noreply@primebooking.com.bd'),

            // SEO & Analytics
            'seo_meta_title'            => SiteSetting::get('seo_meta_title', 'Prime Booking — Bangladesh\'s Best Hotel & Flight Platform'),
            'seo_meta_description'      => SiteSetting::get('seo_meta_description', 'Book hotels, tour packages and airport transfers across Bangladesh. Best prices guaranteed.'),
            'google_analytics_id'       => SiteSetting::get('google_analytics_id', ''),
            'google_search_console'     => SiteSetting::get('google_search_console', ''),
            'facebook_pixel_id'         => SiteSetting::get('facebook_pixel_id', ''),
            'google_tag_manager_id'     => SiteSetting::get('google_tag_manager_id', ''),

            // Social Media Links
            'social_facebook'           => SiteSetting::get('social_facebook', ''),
            'social_instagram'          => SiteSetting::get('social_instagram', ''),
            'social_youtube'            => SiteSetting::get('social_youtube', ''),
            'social_whatsapp'           => SiteSetting::get('social_whatsapp', ''),
            'social_linkedin'           => SiteSetting::get('social_linkedin', ''),
            'social_twitter'            => SiteSetting::get('social_twitter', ''),

            // SMS Gateway & Dynamic Templates
            'sms_provider'                  => SiteSetting::get('sms_provider', 'bulksmsbd'),
            'sms_api_url'                   => SiteSetting::get('sms_api_url', 'http://bulksmsbd.net/api/smsapi'),
            'sms_sender_id'                 => SiteSetting::get('sms_sender_id', 'PrimeBooking'),
            'sms_api_key'                   => SiteSetting::get('sms_api_key', ''),
            'sms_api_secret'                => SiteSetting::get('sms_api_secret', ''),
            'sms_on_booking'                => SiteSetting::get('sms_on_booking', '1'),
            'sms_on_cancelled'              => SiteSetting::get('sms_on_cancelled', '1'),
            'sms_on_payment'                => SiteSetting::get('sms_on_payment', '1'),
            'sms_template_guest_confirmed'  => SiteSetting::get('sms_template_guest_confirmed', "Dear {guest_name}, your booking at {property_name} is CONFIRMED! Ref: {booking_ref}. Check-in: {check_in}. Total: {total_price}. Thank you for choosing PRIME BOOKING!"),
            'sms_template_vendor_alert'     => SiteSetting::get('sms_template_vendor_alert', "PRIME BOOKING Alert: New Reservation #{booking_ref} received for {room_name}. Guest: {guest_name} ({guest_phone}). Check-in: {check_in}."),
            'sms_template_payment_paid'     => SiteSetting::get('sms_template_payment_paid', "Dear {guest_name}, payment received for Booking #{booking_ref}. Status: PAID. Thank you!"),
        ];

        return view('admin.settings', compact('user', 'siteSettings'));
    }

    public function update(Request $request)
    {
        // 1. Profile & Avatar updates
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

        // Profile Avatar Upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $path = $request->file('avatar')->store('uploads/avatars', 'public');
            $user->avatar = asset('storage/' . $path);
        }
        $user->save();

        // Logo & Favicon File Uploads
        if ($request->hasFile('site_logo_file') && $request->file('site_logo_file')->isValid()) {
            $path = $request->file('site_logo_file')->store('uploads/branding', 'public');
            SiteSetting::set('site_logo', asset('storage/' . $path));
        }
        if ($request->hasFile('site_favicon_file') && $request->file('site_favicon_file')->isValid()) {
            $path = $request->file('site_favicon_file')->store('uploads/branding', 'public');
            SiteSetting::set('site_favicon', asset('storage/' . $path));
        }

        // 2. All SiteSettings text keys
        $keys = [
            'site_name', 'site_tagline', 'primary_color', 'support_phone', 'support_email', 'support_address',
            'platform_commission', 'tax_rate', 'min_booking_nights', 'max_booking_nights',
            'max_guests_per_booking', 'cancellation_free_hours',
            'vip_silver_threshold', 'vip_gold_threshold', 'vip_platinum_threshold', 'vip_diamond_threshold',
            'vip_silver_discount', 'vip_gold_discount', 'vip_platinum_discount', 'vip_diamond_discount',
            'bkash_app_key', 'bkash_app_secret', 'bkash_username', 'bkash_password',
            'nagad_merchant_id', 'nagad_public_key',
            'sslcommerz_store_id', 'sslcommerz_store_passwd',
            'stripe_key', 'stripe_secret', 'paypal_client_id', 'paypal_secret',
            'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption',
            'mail_from_name', 'mail_from_address',
            // SEO & Analytics
            'seo_meta_title', 'seo_meta_description',
            'google_analytics_id', 'google_search_console', 'facebook_pixel_id', 'google_tag_manager_id',
            // Social Media
            'social_facebook', 'social_instagram', 'social_youtube',
            'social_whatsapp', 'social_linkedin', 'social_twitter',
            // SMS Gateway & Templates
            'sms_provider', 'sms_api_url', 'sms_sender_id', 'sms_api_key', 'sms_api_secret',
            'sms_template_guest_confirmed', 'sms_template_vendor_alert', 'sms_template_payment_paid',
        ];

        foreach ($keys as $k) {
            if ($request->has($k)) {
                SiteSetting::set($k, $request->input($k));
            }
        }

        if ($request->has('default_currency')) {
            SiteSetting::set('currency', $request->default_currency);
        }

        // Boolean Toggles
        SiteSetting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        SiteSetting::set('new_registrations', $request->has('new_registrations') ? '1' : '0');
        SiteSetting::set('payment_bkash_enabled', $request->has('enable_bkash') ? '1' : '0');
        SiteSetting::set('payment_nagad_enabled', $request->has('enable_nagad') ? '1' : '0');
        SiteSetting::set('payment_card_enabled', $request->has('enable_card') ? '1' : '0');
        SiteSetting::set('payment_stripe_enabled', $request->has('enable_stripe') ? '1' : '0');
        SiteSetting::set('payment_paypal_enabled', $request->has('enable_paypal') ? '1' : '0');
        SiteSetting::set('sms_on_booking', $request->has('sms_on_booking') ? '1' : '0');
        SiteSetting::set('sms_on_cancelled', $request->has('sms_on_cancelled') ? '1' : '0');
        SiteSetting::set('sms_on_payment', $request->has('sms_on_payment') ? '1' : '0');

        Cache::flush();

        return redirect()->back()->with('success', 'Settings & SMS Gateway Configuration updated successfully!');
    }

    /** Send live test SMS from Admin settings */
    public function sendTestSms(Request $request, \App\Services\NotificationService $notificationService)
    {
        $request->validate([
            'test_phone' => 'required|string|min:11',
            'test_msg'   => 'required|string|max:160',
        ]);

        $sent = $notificationService->sendSms($request->test_phone, $request->test_msg);

        if ($sent) {
            return response()->json(['success' => true, 'message' => "Test SMS dispatched to {$request->test_phone} successfully."]);
        }
        return response()->json(['success' => false, 'message' => "Could not send SMS. Please check API Key and Gateway URL in settings."], 422);
    }
}
