<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $company = config('company');
        $siteSettings = [
            'site_name'        => SiteSetting::get('site_name', 'Prime Aviation'),
            'default_currency' => SiteSetting::get('currency', 'BDT'),
            'commission_rate'  => SiteSetting::get('platform_commission', '12'),
            'support_phone'    => SiteSetting::get('support_phone', '+880 1700 000000'),
            'support_email'    => SiteSetting::get('support_email', 'support@primeaviation.com'),
            'enable_bkash'     => SiteSetting::get('payment_bkash_enabled', '1'),
            'enable_nagad'     => SiteSetting::get('payment_nagad_enabled', '1'),
            'enable_card'      => SiteSetting::get('payment_card_enabled', '1'),
        ];
        return view('admin.settings', compact('company', 'siteSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name'        => 'required|string|max:255',
            'default_currency' => 'required|string',
            'commission_rate'  => 'required|numeric|min:0|max:100',
            'support_phone'    => 'required|string',
            'support_email'    => 'required|email',
            'enable_bkash'     => 'nullable',
            'enable_nagad'     => 'nullable',
            'enable_card'      => 'nullable',
        ]);

        SiteSetting::set('site_name', $validated['site_name']);
        SiteSetting::set('currency', $validated['default_currency']);
        SiteSetting::set('platform_commission', $validated['commission_rate']);
        SiteSetting::set('support_phone', $validated['support_phone']);
        SiteSetting::set('support_email', $validated['support_email']);
        SiteSetting::set('payment_bkash_enabled', $request->has('enable_bkash') ? '1' : '0');
        SiteSetting::set('payment_nagad_enabled', $request->has('enable_nagad') ? '1' : '0');
        SiteSetting::set('payment_card_enabled', $request->has('enable_card') ? '1' : '0');

        Cache::flush();

        return redirect()->back()->with('success', 'System settings & platform configurations updated successfully in DB!');
    }
}

