@extends('layouts.admin')
@section('title', 'Platform & Profile Settings — Stockifly SaaS')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>System</span>
        <span class="sep">-</span><strong style="color:#333;">Settings &amp; Control Hub</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:4px;">
        <div>
            <h1 class="page-title" style="font-size:18px;">Stockifly SaaS Settings &amp; Control Hub</h1>
            <p class="text-muted mb-0" style="font-size:11.5px;">Manage platform identity, VIP tier rules, booking taxes, payment vault, and mail SMTP configuration</p>
        </div>
        <button class="btn-stockifly-primary" style="height:32px; padding:0 14px; font-size:12px;" onclick="document.getElementById('stockiflySettingsForm').submit()">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
        </button>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3" style="padding:8px 14px; font-size:12.5px;">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-alert error mb-3" style="padding:8px 14px; font-size:12.5px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="stockiflySettingsForm" action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div class="row g-3">
            {{-- LEFT SIDEBAR TABS --}}
            <div class="col-lg-3">
                <div class="stockifly-card p-2" style="position:sticky; top:80px; min-height:auto;">
                    <div class="nav flex-column nav-pills saas-settings-tabs" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="tab-profile-tab" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="fa-solid fa-user-gear" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">My Profile &amp; Account</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Admin credentials &amp; password</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-company-tab" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab">
                            <i class="fa-solid fa-building-flag" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Company &amp; Brand</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Site title, tagline &amp; theme</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-booking-tab" data-bs-toggle="pill" data-bs-target="#tab-booking" type="button" role="tab">
                            <i class="fa-solid fa-sliders" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">Booking Rules &amp; Tax</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Commission %, VAT &amp; cancellation</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-vip-tab" data-bs-toggle="pill" data-bs-target="#tab-vip" type="button" role="tab">
                            <i class="fa-solid fa-crown" style="font-size:14px; width:20px; color:#eab308;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">VIP Loyalty Rules</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Silver, Gold &amp; Diamond tiers</small>
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
                                <small class="opacity-75" style="font-size:10.5px;">Email dispatch configuration</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-system-tab" data-bs-toggle="pill" data-bs-target="#tab-system" type="button" role="tab">
                            <i class="fa-solid fa-shield-halved" style="font-size:14px; width:20px;"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block" style="font-size:12.5px; line-height:1.2;">System Security &amp; Cache</strong>
                                <small class="opacity-75" style="font-size:10.5px;">Purge cache &amp; database log</small>
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
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'Admin') }}&background=1890ff&color=fff&size=80" class="rounded-circle shadow-sm" style="width:48px; height:48px;" alt="Avatar">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size:14px;">{{ $user->name ?? 'Administrator' }}</h6>
                                    <small class="text-muted d-block" style="font-size:11px;">Super Admin Account | ID: #{{ $user->id ?? 1 }}</small>
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
                                    <label class="saas-label">Change Account Password</label>
                                    <input type="password" name="new_password" class="form-control saas-input" placeholder="Leave blank to keep current">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: COMPANY & BRAND --}}
                    <div class="tab-pane fade" id="tab-company" role="tabpanel">
                        <div class="stockifly-card p-3 mb-3">
                            <div class="saas-section-title mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-building me-1"></i> Platform Identity &amp; Branding
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="saas-label">Platform Brand Title</label>
                                    <input type="text" name="site_name" class="form-control saas-input" value="{{ old('site_name', $siteSettings['site_name']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="saas-label">Primary Brand Theme Color</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" name="primary_color" class="form-control form-control-color saas-input p-1" value="{{ old('primary_color', $siteSettings['primary_color']) }}" style="width:40px;">
                                        <input type="text" class="form-control saas-input" value="{{ old('primary_color', $siteSettings['primary_color']) }}" readonly style="font-family:monospace;">
                                    </div>
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
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="checkMaintenance" {{ $siteSettings['maintenance_mode'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-1" style="font-size:12px;" for="checkMaintenance">Enable System Maintenance Mode</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch pt-2">
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
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const activeTab = localStorage.getItem('stockifly_active_settings_tab');
    if (activeTab) {
        const tabTrigger = document.querySelector(`#${activeTab}`);
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    const tabButtons = document.querySelectorAll('.saas-settings-tabs .nav-link');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('stockifly_active_settings_tab', e.target.id);
        });
    });
});
</script>

@endsection
