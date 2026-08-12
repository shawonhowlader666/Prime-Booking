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
            'site_name'         => SiteSetting::get('site_name', 'PRIME BOOKING'),
            'site_tagline'      => SiteSetting::get('site_tagline', "Bangladesh's #1 Hotel & Flight Platform"),
            'primary_color'     => SiteSetting::get('primary_color', '#1890ff'),
            'default_currency'  => SiteSetting::get('currency', 'BDT'),
            'commission_rate'   => SiteSetting::get('platform_commission', '12'),
            'support_phone'     => SiteSetting::get('support_phone', '+880 1700 000000'),
            'support_email'     => SiteSetting::get('support_email', 'support@primeaviation.com'),
            'support_address'   => SiteSetting::get('support_address', 'Gulshan-2, Dhaka-1212, Bangladesh'),
            'enable_bkash'      => SiteSetting::get('payment_bkash_enabled', '1'),
            'enable_nagad'      => SiteSetting::get('payment_nagad_enabled', '1'),
            'enable_card'       => SiteSetting::get('payment_card_enabled', '1'),
            'mail_host'         => SiteSetting::get('mail_host', 'smtp.mailtrap.io'),
            'mail_port'         => SiteSetting::get('mail_port', '2525'),
            'mail_username'     => SiteSetting::get('mail_username', ''),
            'mail_password'     => SiteSetting::get('mail_password', ''),
            'mail_encryption'   => SiteSetting::get('mail_encryption', 'tls'),
            'mail_from_name'    => SiteSetting::get('mail_from_name', 'Prime Booking'),
            'mail_from_address' => SiteSetting::get('mail_from_address', 'noreply@primebooking.com.bd'),
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

        // 2. Site settings updates
        if ($request->has('site_name'))         SiteSetting::set('site_name', $request->site_name);
        if ($request->has('site_tagline'))      SiteSetting::set('site_tagline', $request->site_tagline);
        if ($request->has('primary_color'))     SiteSetting::set('primary_color', $request->primary_color);
        if ($request->has('default_currency'))  SiteSetting::set('currency', $request->default_currency);
        if ($request->has('commission_rate'))   SiteSetting::set('platform_commission', $request->commission_rate);
        if ($request->has('support_phone'))     SiteSetting::set('support_phone', $request->support_phone);
        if ($request->has('support_email'))     SiteSetting::set('support_email', $request->support_email);
        if ($request->has('support_address'))   SiteSetting::set('support_address', $request->support_address);

        // Payment Gateways
        SiteSetting::set('payment_bkash_enabled', $request->has('enable_bkash') ? '1' : '0');
        SiteSetting::set('payment_nagad_enabled', $request->has('enable_nagad') ? '1' : '0');
        SiteSetting::set('payment_card_enabled', $request->has('enable_card') ? '1' : '0');

        // Mail / SMTP Settings
        if ($request->has('mail_host'))         SiteSetting::set('mail_host', $request->mail_host);
        if ($request->has('mail_port'))         SiteSetting::set('mail_port', $request->mail_port);
        if ($request->has('mail_username'))     SiteSetting::set('mail_username', $request->mail_username);
        if ($request->has('mail_password'))     SiteSetting::set('mail_password', $request->mail_password);
        if ($request->has('mail_encryption'))   SiteSetting::set('mail_encryption', $request->mail_encryption);
        if ($request->has('mail_from_name'))    SiteSetting::set('mail_from_name', $request->mail_from_name);
        if ($request->has('mail_from_address')) SiteSetting::set('mail_from_address', $request->mail_from_address);

        Cache::flush();

        return redirect()->back()->with('success', 'Stockifly SaaS Settings & Profile updated successfully in DB!');
    }
}
