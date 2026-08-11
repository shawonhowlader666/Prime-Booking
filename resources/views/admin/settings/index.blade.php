@extends('layouts.admin')
@section('title', 'Platform Settings — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 style="font-size:19px;font-weight:700;margin:0;">Platform Settings</h1>
        <p style="font-size:12px;color:#8c8c8c;margin:0;">VIP tiers, booking rules, payments, platform info</p>
    </div>
    @if(collect($groups)->flatten()->isEmpty())
    <form action="{{ route('admin.site-settings.seed') }}" method="POST">
        @csrf
        <button type="submit" class="btn-stockifly-primary">
            <i class="fa-solid fa-seedling me-1"></i> Seed Default Settings
        </button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="admin-alert success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.site-settings.update') }}" method="POST">
    @csrf

    <div class="row g-3">

        {{-- VIP Tiers --}}
        <div class="col-lg-6">
            <div class="stockifly-card h-100">
                <div class="card-header-stockifly mb-2">
                    <i class="fa-solid fa-crown me-1" style="color:#f5c518;"></i> VIP / Loyalty Tiers
                </div>
                <div style="font-size:11px;color:#8c8c8c;margin-bottom:10px;">
                    Set minimum bookings (in last 2 years) to reach each tier.
                </div>

                @php
                    $tiers = [
                        ['key'=>'vip_silver_threshold',   'label'=>'🥈 Silver', 'color'=>'#94a3b8'],
                        ['key'=>'vip_gold_threshold',     'label'=>'🥇 Gold',   'color'=>'#f5c518'],
                        ['key'=>'vip_platinum_threshold', 'label'=>'💎 Platinum','color'=>'#a78bfa'],
                        ['key'=>'vip_diamond_threshold',  'label'=>'💠 Diamond', 'color'=>'#38bdf8'],
                    ];
                    $discounts = [
                        ['key'=>'vip_bronze_discount',   'label'=>'🥉 Bronze Discount %'],
                        ['key'=>'vip_silver_discount',   'label'=>'🥈 Silver Discount %'],
                        ['key'=>'vip_gold_discount',     'label'=>'🥇 Gold Discount %'],
                        ['key'=>'vip_platinum_discount', 'label'=>'💎 Platinum Discount %'],
                        ['key'=>'vip_diamond_discount',  'label'=>'💠 Diamond Discount %'],
                    ];
                @endphp

                <table class="table table-sm mb-3">
                    <thead><tr><th>Tier</th><th>Min Bookings</th></tr></thead>
                    <tbody>
                    @foreach($tiers as $tier)
                    @php $setting = $groups['vip']->firstWhere('key', $tier['key']); @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $tier['label'] }}</td>
                        <td>
                            <input type="number" name="settings[{{ $tier['key'] }}]"
                                class="form-control form-control-sm" style="width:80px;"
                                value="{{ $setting?->value ?? '' }}" min="0" max="1000">
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                <div style="font-size:11px;font-weight:700;margin-bottom:6px;color:#555;">Member Discounts</div>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Tier</th><th>Discount %</th></tr></thead>
                    <tbody>
                    @foreach($discounts as $d)
                    @php $s = $groups['vip']->firstWhere('key', $d['key']); @endphp
                    <tr>
                        <td>{{ $d['label'] }}</td>
                        <td>
                            <input type="number" name="settings[{{ $d['key'] }}]"
                                class="form-control form-control-sm" style="width:80px;"
                                value="{{ $s?->value ?? '0' }}" min="0" max="100" step="0.5">
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Booking Rules --}}
        <div class="col-lg-6">
            <div class="stockifly-card mb-3">
                <div class="card-header-stockifly mb-2">
                    <i class="fa-solid fa-calendar-check me-1"></i> Booking Rules
                </div>
                @php
                    $bookingFields = [
                        ['key'=>'platform_commission',     'label'=>'Platform Commission %',       'type'=>'number','step'=>'0.5'],
                        ['key'=>'tax_rate',                'label'=>'Tax / VAT Rate %',            'type'=>'number','step'=>'0.5'],
                        ['key'=>'min_booking_nights',      'label'=>'Min Booking Nights',          'type'=>'number','step'=>'1'],
                        ['key'=>'max_booking_nights',      'label'=>'Max Booking Nights',          'type'=>'number','step'=>'1'],
                        ['key'=>'cancellation_free_hours', 'label'=>'Free Cancellation (hours)',   'type'=>'number','step'=>'1'],
                    ];
                @endphp
                <table class="table table-sm mb-0">
                    <thead><tr><th>Rule</th><th>Value</th></tr></thead>
                    <tbody>
                    @foreach($bookingFields as $f)
                    @php $s = $groups['booking']->firstWhere('key', $f['key']); @endphp
                    <tr>
                        <td>{{ $f['label'] }}</td>
                        <td>
                            <input type="{{ $f['type'] }}" step="{{ $f['step'] }}"
                                name="settings[{{ $f['key'] }}]"
                                class="form-control form-control-sm" style="width:90px;"
                                value="{{ $s?->value ?? '' }}">
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Payment Methods --}}
            <div class="stockifly-card">
                <div class="card-header-stockifly mb-2">
                    <i class="fa-solid fa-credit-card me-1"></i> Payment Methods
                </div>
                @php
                    $payments = [
                        ['key'=>'payment_bkash_enabled', 'label'=>'📱 bKash'],
                        ['key'=>'payment_nagad_enabled', 'label'=>'📱 Nagad'],
                        ['key'=>'payment_card_enabled',  'label'=>'💳 Card / Bank'],
                        ['key'=>'payment_stripe_enabled','label'=>'💳 Stripe'],
                        ['key'=>'payment_paypal_enabled','label'=>'🅿️ PayPal'],
                    ];
                @endphp
                @foreach($payments as $p)
                @php $s = $groups['payment']->firstWhere('key', $p['key']); @endphp
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="settings[{{ $p['key'] }}]"
                        value="1" id="{{ $p['key'] }}"
                        {{ (bool)($s?->value ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $p['key'] }}" style="font-size:13px;">
                        {{ $p['label'] }}
                    </label>
                </div>
                @endforeach

                <div class="mt-2">
                    <label class="form-label-sm">Default Currency</label>
                    @php $cur = $groups['payment']->firstWhere('key','currency'); @endphp
                    <select name="settings[currency]" class="form-select form-select-sm" style="width:120px;">
                        <option value="BDT" @selected(($cur?->value ?? 'BDT') == 'BDT')>BDT (৳)</option>
                        <option value="USD" @selected(($cur?->value) == 'USD')>USD ($)</option>
                        <option value="EUR" @selected(($cur?->value) == 'EUR')>EUR (€)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- General Platform Info --}}
        <div class="col-12">
            <div class="stockifly-card">
                <div class="card-header-stockifly mb-2">
                    <i class="fa-solid fa-building me-1"></i> Platform Information
                </div>
                <div class="row g-2">
                    @php
                        $generalFields = [
                            ['key'=>'site_name',     'label'=>'Site Name'],
                            ['key'=>'site_tagline',  'label'=>'Tagline'],
                            ['key'=>'support_email', 'label'=>'Support Email'],
                            ['key'=>'support_phone', 'label'=>'Support Phone'],
                        ];
                    @endphp
                    @foreach($generalFields as $f)
                    @php $s = $groups['general']->firstWhere('key', $f['key']); @endphp
                    <div class="col-md-3">
                        <label class="form-label-sm">{{ $f['label'] }}</label>
                        <input type="text" name="settings[{{ $f['key'] }}]"
                            class="form-control form-control-sm"
                            value="{{ $s?->value ?? '' }}">
                    </div>
                    @endforeach

                    <div class="col-md-3 d-flex align-items-end">
                        @php $maint = $groups['general']->firstWhere('key','maintenance_mode'); @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="settings[maintenance_mode]"
                                value="1" id="maintenance_mode"
                                {{ (bool)($maint?->value ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode" style="color:#dc2626;font-weight:600;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Maintenance Mode
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn-stockifly-primary">
            <i class="fa-solid fa-save me-1"></i> Save All Settings
        </button>
        @if(collect($groups)->flatten()->isEmpty())
        <a href="{{ route('admin.site-settings.seed') }}" class="btn btn-outline-secondary ms-2">Seed Defaults</a>
        @endif
    </div>
</form>
@endsection
