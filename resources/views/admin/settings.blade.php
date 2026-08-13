@extends('layouts.admin')
@section('title', 'Settings — PRIME BOOKING')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0" id="page-title-text">SaaS Settings &amp; System Control Parameters</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-add-primary" style="font-size:13px; height:36px; padding:0 20px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;" onclick="document.getElementById('stockiflySettingsForm').submit()">
                <i class="fa-solid fa-check"></i> <span>Save Settings Changes</span>
            </button>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>System Settings</span>
        <span class="sep">-</span><strong id="breadcrumb-active-tab" style="color:#333;">My Profile &amp; Controls</strong>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3" style="padding:8px 14px; font-size:12.5px;">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="admin-alert error mb-3" style="padding:8px 14px; font-size:12.5px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="stockiflySettingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            {{-- LEFT SIDEBAR TABS --}}
            <div class="col-lg-3">
                <div class="stockifly-card p-2" style="position:sticky; top:80px; min-height:auto;">
                    <div class="nav flex-column nav-pills saas-settings-tabs" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="tab-profile-tab" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="fa-solid fa-user-gear" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">My Profile &amp; Avatar</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Photo, account &amp; security</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-company-tab" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab">
                            <i class="fa-solid fa-building-flag" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Company &amp; Brand</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Logo, branding &amp; theme</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-booking-tab" data-bs-toggle="pill" data-bs-target="#tab-booking" type="button" role="tab">
                            <i class="fa-solid fa-sliders" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Booking Rules &amp; Tax</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Commission &amp; VAT</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-vip-tab" data-bs-toggle="pill" data-bs-target="#tab-vip" type="button" role="tab">
                            <i class="fa-solid fa-crown" style="font-size:14px; width:20px; color:#eab308;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">VIP Loyalty Rules</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Tier discounts &amp; rules</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-payments-tab" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button" role="tab">
                            <i class="fa-solid fa-credit-card" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Payment Vault</strong>
                                <small class="opacity-75" style="font-size:10.5px;">bKash, Nagad, SSLCommerz</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-mail-tab" data-bs-toggle="pill" data-bs-target="#tab-mail" type="button" role="tab">
                            <i class="fa-solid fa-paper-plane" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Mail &amp; SMTP Server</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Email dispatch settings</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-system-tab" data-bs-toggle="pill" data-bs-target="#tab-system" type="button" role="tab">
                            <i class="fa-solid fa-shield-halved" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">System Security &amp; Cache</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Purge cache &amp; logs</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-seo-tab" data-bs-toggle="pill" data-bs-target="#tab-seo" type="button" role="tab">
                            <i class="fa-solid fa-magnifying-glass-chart" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">SEO &amp; Analytics</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Google, Meta &amp; tracking</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-social-tab" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab">
                            <i class="fa-solid fa-share-nodes" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Social Media Links</strong>
                                <small class="opacity-75" style="font-size:10.5px;">FB, WhatsApp, YouTube</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-sms-tab" data-bs-toggle="pill" data-bs-target="#tab-sms" type="button" role="tab">
                            <i class="fa-solid fa-sms" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">SMS Gateway</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Booking SMS notifications</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT TAB PANES --}}
            <div class="col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">

                    {{-- TAB 1: PROFILE & ACCOUNT --}}
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                                <div style="width:58px; height:58px; border-radius:50%; overflow:hidden; background:#f1f5f9; border:2px solid var(--primary); flex-shrink:0;">
                                    <img src="{{ $user?->avatar ?: ('https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'Admin') . '&background=7367f0&color=fff&size=80') }}" class="w-100 h-100" style="object-fit:cover;" alt="Avatar">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1" style="font-size:13.5px;">{{ $user->name ?? 'Administrator' }} (ID #{{ $user->id ?? 1 }})</h6>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*" style="font-size:11.5px; max-width:280px; border-radius:4px;">
                                        <span style="font-size:11px; color:#64748b;">(Upload profile photo from computer or gallery)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">Full Administrator Name</label>
                                    <input type="text" name="name" class="form-control saas-input" value="{{ old('name', $user->name ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Account Email Address</label>
                                    <input type="email" name="email" class="form-control saas-input" value="{{ old('email', $user->email ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Contact Phone Number</label>
                                    <input type="text" name="phone" class="form-control saas-input" value="{{ old('phone', $user->phone ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Change Password</label>
                                    <input type="password" name="new_password" class="form-control saas-input" placeholder="Leave blank to keep current">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: COMPANY & BRAND --}}
                    <div class="tab-pane fade" id="tab-company" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-building me-1"></i> Platform Identity, Logo &amp; Branding
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">Platform Brand Title</label>
                                    <input type="text" name="site_name" class="form-control saas-input" value="{{ old('site_name', $siteSettings['site_name']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Primary Brand Theme Color</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="primary_color" id="primaryColorInput" class="form-control saas-input p-0" value="{{ old('primary_color', $siteSettings['primary_color']) }}" style="width:34px; height:32px; border-radius:4px !important; cursor:pointer;" oninput="document.getElementById('primaryColorHex').value = this.value">
                                        <input type="text" id="primaryColorHex" class="form-control saas-input" value="{{ old('primary_color', $siteSettings['primary_color']) }}" readonly style="font-family:monospace; width:100px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Upload Site Logo (PNG/SVG)</label>
                                    <input type="file" name="site_logo_file" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;">
                                    @if(!empty($siteSettings['site_logo']))
                                        <div class="mt-1"><img src="{{ $siteSettings['site_logo'] }}" height="26" class="border p-1 rounded bg-white"></div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Upload Site Favicon (ICO/PNG)</label>
                                    <input type="file" name="site_favicon_file" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;">
                                    @if(!empty($siteSettings['site_favicon']))
                                        <div class="mt-1"><img src="{{ $siteSettings['site_favicon'] }}" height="22" class="border p-1 rounded bg-white"></div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label class="saas-label">Site Tagline / Subtitle</label>
                                    <input type="text" name="site_tagline" class="form-control saas-input" value="{{ old('site_tagline', $siteSettings['site_tagline']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Support Hotline Phone</label>
                                    <input type="text" name="support_phone" class="form-control saas-input" value="{{ old('support_phone', $siteSettings['support_phone']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Support Email Address</label>
                                    <input type="email" name="support_email" class="form-control saas-input" value="{{ old('support_email', $siteSettings['support_email']) }}">
                                </div>
                                <div class="col-12">
                                    <label class="saas-label">Corporate Office Address</label>
                                    <input type="text" name="support_address" class="form-control saas-input" value="{{ old('support_address', $siteSettings['support_address']) }}">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch pt-1">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="checkMaintenance" {{ $siteSettings['maintenance_mode'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-1" style="font-size:12px;" for="checkMaintenance">Enable Maintenance Mode</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch pt-1">
                                        <input class="form-check-input" type="checkbox" name="new_registrations" id="checkReg" {{ $siteSettings['new_registrations'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-1" style="font-size:12px;" for="checkReg">Allow Public User Registrations</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: BOOKING RULES & TAX --}}
                    <div class="tab-pane fade" id="tab-booking" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-sliders me-1"></i> Booking Taxes, Commission &amp; Rules
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">Platform Vendor Commission (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="platform_commission" class="form-control saas-input" value="{{ old('platform_commission', $siteSettings['platform_commission']) }}" step="0.1">
                                        <span class="input-group-text py-0" style="font-size:11px;">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Government Tax Rate (VAT %)</label>
                                    <div class="input-group">
                                        <input type="number" name="tax_rate" class="form-control saas-input" value="{{ old('tax_rate', $siteSettings['tax_rate']) }}" step="0.1">
                                        <span class="input-group-text py-0" style="font-size:11px;">% VAT</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Min Booking Duration (Nights)</label>
                                    <input type="number" name="min_booking_nights" class="form-control saas-input" value="{{ old('min_booking_nights', $siteSettings['min_booking_nights']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Max Booking Duration (Nights)</label>
                                    <input type="number" name="max_booking_nights" class="form-control saas-input" value="{{ old('max_booking_nights', $siteSettings['max_booking_nights']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Max Guests Allowed Per Order</label>
                                    <input type="number" name="max_guests_per_booking" class="form-control saas-input" value="{{ old('max_guests_per_booking', $siteSettings['max_guests_per_booking']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Free Cancellation Window (Hours)</label>
                                    <input type="number" name="cancellation_free_hours" class="form-control saas-input" value="{{ old('cancellation_free_hours', $siteSettings['cancellation_free_hours']) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: VIP LOYALTY TIERS --}}
                    <div class="tab-pane fade" id="tab-vip" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom" style="color:#d97706;">
                                <i class="fa-solid fa-crown me-1"></i> VIP Loyalty Tier Thresholds &amp; Discounts
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">Silver Tier (Min Bookings)</label>
                                    <input type="number" name="vip_silver_threshold" class="form-control saas-input" value="{{ old('vip_silver_threshold', $siteSettings['vip_silver_threshold']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Silver Tier Discount (%)</label>
                                    <input type="number" name="vip_silver_discount" class="form-control saas-input" value="{{ old('vip_silver_discount', $siteSettings['vip_silver_discount']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Gold Tier (Min Bookings)</label>
                                    <input type="number" name="vip_gold_threshold" class="form-control saas-input" value="{{ old('vip_gold_threshold', $siteSettings['vip_gold_threshold']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Gold Tier Discount (%)</label>
                                    <input type="number" name="vip_gold_discount" class="form-control saas-input" value="{{ old('vip_gold_discount', $siteSettings['vip_gold_discount']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Platinum Tier (Min Bookings)</label>
                                    <input type="number" name="vip_platinum_threshold" class="form-control saas-input" value="{{ old('vip_platinum_threshold', $siteSettings['vip_platinum_threshold']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Platinum Tier Discount (%)</label>
                                    <input type="number" name="vip_platinum_discount" class="form-control saas-input" value="{{ old('vip_platinum_discount', $siteSettings['vip_platinum_discount']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Diamond Tier (Min Bookings)</label>
                                    <input type="number" name="vip_diamond_threshold" class="form-control saas-input" value="{{ old('vip_diamond_threshold', $siteSettings['vip_diamond_threshold']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Diamond Tier Discount (%)</label>
                                    <input type="number" name="vip_diamond_discount" class="form-control saas-input" value="{{ old('vip_diamond_discount', $siteSettings['vip_diamond_discount']) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 5: PAYMENT VAULT --}}
                    <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-credit-card me-1"></i> Payment Gateways &amp; Merchant Integration
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-2.5 bg-light h-100">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" name="enable_bkash" id="checkBkash" {{ $siteSettings['enable_bkash'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" style="font-size:12px;" for="checkBkash">bKash Merchant</label>
                                        </div>
                                        <small class="text-muted d-block" style="font-size:10.5px;">Direct bKash Tokenized API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-2.5 bg-light h-100">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" name="enable_nagad" id="checkNagad" {{ $siteSettings['enable_nagad'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" style="font-size:12px;" for="checkNagad">Nagad Gateway</label>
                                        </div>
                                        <small class="text-muted d-block" style="font-size:10.5px;">Nagad Direct Pay API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-2.5 bg-light h-100">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" name="enable_card" id="checkCard" {{ $siteSettings['enable_card'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" style="font-size:12px;" for="checkCard">SSLCommerz / Cards</label>
                                        </div>
                                        <small class="text-muted d-block" style="font-size:10.5px;">Visa, MasterCard &amp; Banking</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-2.5 bg-light h-100">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" name="enable_stripe" id="checkStripe" {{ $siteSettings['enable_stripe'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" style="font-size:12px;" for="checkStripe">Stripe International</label>
                                        </div>
                                        <small class="text-muted d-block" style="font-size:10.5px;">USD / EUR International Credit Cards</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-2.5 bg-light h-100">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" name="enable_paypal" id="checkPaypal" {{ $siteSettings['enable_paypal'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" style="font-size:12px;" for="checkPaypal">PayPal Checkout</label>
                                        </div>
                                        <small class="text-muted d-block" style="font-size:10.5px;">PayPal Express Checkout Integration</small>
                                    </div>
                                </div>
                            </div>

                            {{-- MERCHANT CREDENTIALS & API KEYS --}}
                            <div class="saas-section-title mt-4 mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-key me-1"></i> Payment Gateway API Credentials &amp; Merchant Keys
                            </div>
                            <div class="row g-3">
                                {{-- bKash API Credentials --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <h6 class="fw-bold mb-2.5 text-danger" style="font-size:13px;"><i class="fa-solid fa-mobile-screen-button me-1"></i> bKash Merchant Tokenized API</h6>
                                        <div class="mb-2">
                                            <label class="saas-label">bKash App Key</label>
                                            <input type="text" name="bkash_app_key" class="form-control saas-input" value="{{ old('bkash_app_key', $siteSettings['bkash_app_key']) }}" placeholder="Enter bKash App Key">
                                        </div>
                                        <div class="mb-2">
                                            <label class="saas-label">bKash App Secret</label>
                                            <input type="password" name="bkash_app_secret" class="form-control saas-input" value="{{ old('bkash_app_secret', $siteSettings['bkash_app_secret']) }}" placeholder="Enter bKash App Secret">
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="saas-label">Merchant Username</label>
                                                <input type="text" name="bkash_username" class="form-control saas-input" value="{{ old('bkash_username', $siteSettings['bkash_username']) }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="saas-label">Merchant Password</label>
                                                <input type="password" name="bkash_password" class="form-control saas-input" value="{{ old('bkash_password', $siteSettings['bkash_password']) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SSLCommerz / Card Credentials --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <h6 class="fw-bold mb-2.5 text-primary" style="font-size:13px;"><i class="fa-solid fa-credit-card me-1"></i> SSLCommerz Merchant Credentials</h6>
                                        <div class="mb-2">
                                            <label class="saas-label">SSLCommerz Store ID</label>
                                            <input type="text" name="sslcommerz_store_id" class="form-control saas-input" value="{{ old('sslcommerz_store_id', $siteSettings['sslcommerz_store_id']) }}" placeholder="e.g. primebookinglive">
                                        </div>
                                        <div class="mb-2">
                                            <label class="saas-label">SSLCommerz Store Password</label>
                                            <input type="password" name="sslcommerz_store_passwd" class="form-control saas-input" value="{{ old('sslcommerz_store_passwd', $siteSettings['sslcommerz_store_passwd']) }}" placeholder="Enter Store Password">
                                        </div>
                                    </div>
                                </div>

                                {{-- Nagad API Credentials --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <h6 class="fw-bold mb-2.5 text-warning" style="font-size:13px;"><i class="fa-solid fa-wallet me-1"></i> Nagad Merchant Direct Pay API</h6>
                                        <div class="mb-2">
                                            <label class="saas-label">Nagad Merchant ID</label>
                                            <input type="text" name="nagad_merchant_id" class="form-control saas-input" value="{{ old('nagad_merchant_id', $siteSettings['nagad_merchant_id']) }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="saas-label">Nagad Public Key</label>
                                            <input type="text" name="nagad_public_key" class="form-control saas-input" value="{{ old('nagad_public_key', $siteSettings['nagad_public_key']) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Stripe & PayPal International --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <h6 class="fw-bold mb-2.5 text-info" style="font-size:13px;"><i class="fa-solid fa-globe me-1"></i> Stripe &amp; PayPal API Credentials</h6>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="saas-label">Stripe Publishable Key</label>
                                                <input type="text" name="stripe_key" class="form-control saas-input" value="{{ old('stripe_key', $siteSettings['stripe_key']) }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="saas-label">Stripe Secret Key</label>
                                                <input type="password" name="stripe_secret" class="form-control saas-input" value="{{ old('stripe_secret', $siteSettings['stripe_secret']) }}">
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="saas-label">PayPal Client ID</label>
                                                <input type="text" name="paypal_client_id" class="form-control saas-input" value="{{ old('paypal_client_id', $siteSettings['paypal_client_id']) }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="saas-label">PayPal Secret Key</label>
                                                <input type="password" name="paypal_secret" class="form-control saas-input" value="{{ old('paypal_secret', $siteSettings['paypal_secret']) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 6: MAIL & SMTP SERVER --}}
                    <div class="tab-pane fade" id="tab-mail" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-paper-plane me-1"></i> Mail &amp; SMTP Dispatch Configuration
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">SMTP Mail Host</label>
                                    <input type="text" name="mail_host" class="form-control saas-input" value="{{ old('mail_host', $siteSettings['mail_host']) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="saas-label">SMTP Port</label>
                                    <input type="text" name="mail_port" class="form-control saas-input" value="{{ old('mail_port', $siteSettings['mail_port']) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="saas-label">Encryption</label>
                                    <select name="mail_encryption" class="form-select saas-input">
                                        <option value="tls" {{ $siteSettings['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $siteSettings['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">SMTP Username</label>
                                    <input type="text" name="mail_username" class="form-control saas-input" value="{{ old('mail_username', $siteSettings['mail_username']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">SMTP Password</label>
                                    <input type="password" name="mail_password" class="form-control saas-input" value="{{ old('mail_password', $siteSettings['mail_password']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Sender Name</label>
                                    <input type="text" name="mail_from_name" class="form-control saas-input" value="{{ old('mail_from_name', $siteSettings['mail_from_name']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Sender Email Address</label>
                                    <input type="email" name="mail_from_address" class="form-control saas-input" value="{{ old('mail_from_address', $siteSettings['mail_from_address']) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 7: SYSTEM SECURITY & CACHE --}}
                    <div class="tab-pane fade" id="tab-system" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-shield-halved me-1"></i> System Maintenance &amp; Security Utilities
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light mb-3">
                                <div>
                                    <strong class="d-block text-dark" style="font-size:12.5px;">Clear Redis &amp; Application View Caches</strong>
                                    <small class="text-muted" style="font-size:11px;">Purges all cached configuration, compiled views, and route caches</small>
                                </div>
                                <button type="button" class="btn btn-outline-warning btn-sm fw-bold px-3" style="font-size:11.5px;" onclick="document.getElementById('flushCacheForm').submit()">
                                    <i class="fa-solid fa-rotate me-1"></i> Flush Cache Now
                                </button>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light">
                                <div>
                                    <strong class="d-block text-dark" style="font-size:12.5px;">Activity Audit Logs</strong>
                                    <small class="text-muted" style="font-size:11px;">Inspect admin actions, logins, and system changes</small>
                                </div>
                                <a href="{{ route('admin.activity.index') }}" class="btn btn-outline-primary btn-sm fw-bold px-3" style="font-size:11.5px;">
                                    <i class="fa-solid fa-shield-halved me-1"></i> View Audit Logs
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 8: SEO & ANALYTICS --}}
                    <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-magnifying-glass-chart me-1"></i> SEO, Google Analytics &amp; Meta Tracking
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="saas-label">Default Meta Page Title</label>
                                    <input type="text" name="seo_meta_title" class="form-control saas-input" value="{{ old('seo_meta_title', $siteSettings['seo_meta_title']) }}" placeholder="e.g. Prime Booking — Bangladesh's Best Hotel Booking Platform">
                                </div>
                                <div class="col-12">
                                    <label class="saas-label">Default Meta Description</label>
                                    <textarea name="seo_meta_description" class="form-control saas-input" rows="2" style="height:auto !important;" placeholder="150-160 character SEO-optimised site description...">{{ old('seo_meta_description', $siteSettings['seo_meta_description']) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Google Analytics Measurement ID</label>
                                    <input type="text" name="google_analytics_id" class="form-control saas-input" value="{{ old('google_analytics_id', $siteSettings['google_analytics_id']) }}" placeholder="e.g. G-XXXXXXXXXX">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Google Search Console Verification Code</label>
                                    <input type="text" name="google_search_console" class="form-control saas-input" value="{{ old('google_search_console', $siteSettings['google_search_console']) }}" placeholder="e.g. google-site-verification=xxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Meta (Facebook) Pixel ID</label>
                                    <input type="text" name="facebook_pixel_id" class="form-control saas-input" value="{{ old('facebook_pixel_id', $siteSettings['facebook_pixel_id']) }}" placeholder="e.g. 123456789012345">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Google Tag Manager Container ID</label>
                                    <input type="text" name="google_tag_manager_id" class="form-control saas-input" value="{{ old('google_tag_manager_id', $siteSettings['google_tag_manager_id']) }}" placeholder="e.g. GTM-XXXXXXX">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 9: SOCIAL MEDIA LINKS --}}
                    <div class="tab-pane fade" id="tab-social" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-share-nodes me-1"></i> Social Media &amp; Communication Channels
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label"><i class="fa-brands fa-facebook text-primary me-1"></i> Facebook Page URL</label>
                                    <input type="url" name="social_facebook" class="form-control saas-input" value="{{ old('social_facebook', $siteSettings['social_facebook']) }}" placeholder="https://facebook.com/primebooking">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label"><i class="fa-brands fa-instagram text-danger me-1"></i> Instagram Profile URL</label>
                                    <input type="url" name="social_instagram" class="form-control saas-input" value="{{ old('social_instagram', $siteSettings['social_instagram']) }}" placeholder="https://instagram.com/primebooking">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label"><i class="fa-brands fa-youtube text-danger me-1"></i> YouTube Channel URL</label>
                                    <input type="url" name="social_youtube" class="form-control saas-input" value="{{ old('social_youtube', $siteSettings['social_youtube']) }}" placeholder="https://youtube.com/@primebooking">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label"><i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp Business Number</label>
                                    <input type="text" name="social_whatsapp" class="form-control saas-input" value="{{ old('social_whatsapp', $siteSettings['social_whatsapp']) }}" placeholder="e.g. +8801700000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label"><i class="fa-brands fa-linkedin text-info me-1"></i> LinkedIn Page URL</label>
                                    <input type="url" name="social_linkedin" class="form-control saas-input" value="{{ old('social_linkedin', $siteSettings['social_linkedin']) }}" placeholder="https://linkedin.com/company/primebooking">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label"><i class="fa-brands fa-twitter text-info me-1"></i> X (Twitter) Profile URL</label>
                                    <input type="url" name="social_twitter" class="form-control saas-input" value="{{ old('social_twitter', $siteSettings['social_twitter']) }}" placeholder="https://x.com/primebooking">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 10: SMS GATEWAY --}}
                    <div class="tab-pane fade" id="tab-sms" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-sms me-1"></i> SMS Gateway &amp; Booking Notification Settings
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">SMS Provider</label>
                                    <select name="sms_provider" class="form-select saas-input">
                                        @php $smsProv = old('sms_provider', $siteSettings['sms_provider']); @endphp
                                        <option value="sslcommerz_sms" {{ $smsProv === 'sslcommerz_sms' ? 'selected' : '' }}>SSLCommerz SMS</option>
                                        <option value="twilio" {{ $smsProv === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                        <option value="nexmo" {{ $smsProv === 'nexmo' ? 'selected' : '' }}>Vonage (Nexmo)</option>
                                        <option value="alpha" {{ $smsProv === 'alpha' ? 'selected' : '' }}>Alpha Net BD</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">SMS Sender Name / Masking</label>
                                    <input type="text" name="sms_sender_id" class="form-control saas-input" value="{{ old('sms_sender_id', $siteSettings['sms_sender_id']) }}" placeholder="e.g. PrimeBooK">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">SMS API Key / Account SID</label>
                                    <input type="text" name="sms_api_key" class="form-control saas-input" value="{{ old('sms_api_key', $siteSettings['sms_api_key']) }}" placeholder="Enter API Key or Account SID">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">SMS API Secret / Auth Token</label>
                                    <input type="password" name="sms_api_secret" class="form-control saas-input" value="{{ old('sms_api_secret', $siteSettings['sms_api_secret']) }}" placeholder="Enter API Secret or Auth Token">
                                </div>
                                <div class="col-12">
                                    <div class="saas-section-title mt-2 mb-2 pb-1 border-bottom">SMS Notification Triggers</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sms_on_booking" id="smsOnBooking" {{ ($siteSettings['sms_on_booking'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="smsOnBooking" style="font-size:12.5px;">On Booking Confirmed</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sms_on_cancelled" id="smsOnCancelled" {{ ($siteSettings['sms_on_cancelled'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="smsOnCancelled" style="font-size:12.5px;">On Booking Cancelled</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sms_on_payment" id="smsOnPayment" {{ ($siteSettings['sms_on_payment'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="smsOnPayment" style="font-size:12.5px;">On Payment Received</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </form>

    <form id="flushCacheForm" action="/admin/system/cache-clear" method="POST" class="d-none">
        @csrf
    </form>

</div>

<style>
/* Exact Stockifly-SaaS Typography & Input Styling */
.saas-label {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.4px !important;
    margin-bottom: 4px !important;
    display: block;
}

.saas-input {
    font-size: 12px !important;
    height: 32px !important;
    padding: 3px 10px !important;
    border-radius: 4px !important;
    border: 1px solid #cbd5e1 !important;
    color: #1e293b !important;
    background-color: #ffffff !important;
    transition: border-color 0.15s ease;
}

.saas-input:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 2px var(--primary-transparent-10) !important;
}

.saas-section-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--primary) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.saas-settings-tabs .nav-link {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 4px !important;
    color: #475569;
    background: transparent;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    margin-bottom: 3px;
}

.saas-settings-tabs .nav-link:hover {
    background: #f1f5f9;
    color: var(--primary);
}

.saas-settings-tabs .nav-link.active {
    background: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(32,103,225,0.25);
}

/* Custom 4px Rounded Color Picker Swatch */
input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 2px !important;
    border-radius: 4px !important;
}
input[type="color"]::-webkit-color-swatch {
    border: none !important;
    border-radius: 3px !important;
}
input[type="color"]::-moz-color-swatch {
    border: none !important;
    border-radius: 3px !important;
}
</style>

<script>
function updateSettingsHeader(tabEl) {
    const titleText = tabEl.querySelector('strong') ? tabEl.querySelector('strong').innerText : 'Settings';
    const breadcrumbTab = document.getElementById('breadcrumb-active-tab');
    const pageTitle = document.getElementById('page-title-text');
    if (breadcrumbTab) breadcrumbTab.innerText = titleText;
    if (pageTitle) pageTitle.innerText = titleText;
}

document.addEventListener("DOMContentLoaded", function() {
    const activeTabId = localStorage.getItem('stockifly_active_settings_tab');
    if (activeTabId) {
        const tabTrigger = document.querySelector(`#${activeTabId}`);
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
            updateSettingsHeader(tabTrigger);
        }
    } else {
        const defaultTab = document.querySelector('.saas-settings-tabs .nav-link.active');
        if (defaultTab) updateSettingsHeader(defaultTab);
    }

    const tabButtons = document.querySelectorAll('.saas-settings-tabs .nav-link');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('stockifly_active_settings_tab', e.target.id);
            updateSettingsHeader(e.target);
        });
    });
});
</script>

@endsection
