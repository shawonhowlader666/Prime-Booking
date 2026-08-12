@extends('layouts.admin')
@section('title', 'Platform & Profile Settings — Stockifly SaaS')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>System</span>
        <span class="sep">-</span><strong style="color:#333;">Settings &amp; Profile</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <div>
            <h1 class="page-title">Stockifly Platform Settings &amp; Admin Profile</h1>
            <p class="text-muted mb-0" style="font-size:12px;">Manage admin credentials, brand identity, payment gateways, and mail SMTP configuration</p>
        </div>
        <button class="btn-stockifly-primary" onclick="document.getElementById('stockiflySettingsForm').submit()">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
        </button>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-alert error mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="stockiflySettingsForm" action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- LEFT SIDEBAR TABS --}}
            <div class="col-lg-3">
                <div class="stockifly-card p-2" style="position:sticky; top:80px;">
                    <div class="nav flex-column nav-pills saas-settings-tabs" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="tab-profile-tab" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="fa-solid fa-user-gear"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block">My Profile &amp; Account</strong>
                                <small class="opacity-75">Admin credentials &amp; password</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-company-tab" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab">
                            <i class="fa-solid fa-building-flag"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block">Company &amp; Brand</strong>
                                <small class="opacity-75">Site name, logo &amp; theme</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-currency-tab" data-bs-toggle="pill" data-bs-target="#tab-currency" type="button" role="tab">
                            <i class="fa-solid fa-coins"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block">Currency &amp; Rates</strong>
                                <small class="opacity-75">BDT symbol &amp; commission</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-payments-tab" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button" role="tab">
                            <i class="fa-solid fa-credit-card"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block">Payment Gateways</strong>
                                <small class="opacity-75">bKash, Nagad, Cards &amp; Vault</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-mail-tab" data-bs-toggle="pill" data-bs-target="#tab-mail" type="button" role="tab">
                            <i class="fa-solid fa-paper-plane"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block">Mail &amp; SMTP Server</strong>
                                <small class="opacity-75">Email dispatch configuration</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-system-tab" data-bs-toggle="pill" data-bs-target="#tab-system" type="button" role="tab">
                            <i class="fa-solid fa-shield-halved"></i>
                            <div class="text-start ms-2">
                                <strong class="d-block">System &amp; Security</strong>
                                <small class="opacity-75">Cache flush &amp; database</small>
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
                        <div class="stockifly-card p-4 mb-3">
                            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'Admin') }}&background=1890ff&color=fff&size=100" class="rounded-circle shadow-sm" style="width:64px; height:64px;" alt="Avatar">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">{{ $user->name ?? 'Administrator' }}</h5>
                                    <span class="badge bg-primary-subtle text-primary fw-bold">Super Admin Role</span>
                                    <small class="text-muted d-block mt-1">ID: #{{ $user->id ?? 1 }} | Joined: {{ $user->created_at ? $user->created_at->format('M Y') : 'System' }}</small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Full Administrator Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" placeholder="Enter full name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Account Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" placeholder="admin@primebooking.com.bd">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Contact Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+880 1700 000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Change Account Password</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: COMPANY & BRAND --}}
                    <div class="tab-pane fade" id="tab-company" role="tabpanel">
                        <div class="stockifly-card p-4 mb-3">
                            <div class="form-section-title mb-3 pb-2 border-bottom fw-bold text-primary">
                                <i class="fa-solid fa-building me-1"></i> Platform Identity &amp; Branding
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Platform Brand Name</label>
                                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $siteSettings['site_name']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Primary Brand Color</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $siteSettings['primary_color']) }}" style="width:48px; height:34px; padding:2px;">
                                        <input type="text" class="form-control" value="{{ old('primary_color', $siteSettings['primary_color']) }}" readonly style="font-family:monospace;">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Site Subtitle / Tagline</label>
                                    <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $siteSettings['site_tagline']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Support Hotline Number</label>
                                    <input type="text" name="support_phone" class="form-control" value="{{ old('support_phone', $siteSettings['support_phone']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Support Email Address</label>
                                    <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $siteSettings['support_email']) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Corporate Office Address</label>
                                    <input type="text" name="support_address" class="form-control" value="{{ old('support_address', $siteSettings['support_address']) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: CURRENCY & RATES --}}
                    <div class="tab-pane fade" id="tab-currency" role="tabpanel">
                        <div class="stockifly-card p-4 mb-3">
                            <div class="form-section-title mb-3 pb-2 border-bottom fw-bold text-primary">
                                <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Currency &amp; Commission Configuration
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Default Platform Currency</label>
                                    <select name="default_currency" class="form-select">
                                        <option value="BDT" {{ $siteSettings['default_currency'] === 'BDT' ? 'selected' : '' }}>BDT (৳ — Bangladeshi Taka)</option>
                                        <option value="USD" {{ $siteSettings['default_currency'] === 'USD' ? 'selected' : '' }}>USD ($ — US Dollar)</option>
                                        <option value="EUR" {{ $siteSettings['default_currency'] === 'EUR' ? 'selected' : '' }}>EUR (€ — Euro)</option>
                                        <option value="SAR" {{ $siteSettings['default_currency'] === 'SAR' ? 'selected' : '' }}>SAR (﷼ — Saudi Riyal)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Platform Vendor Commission Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="commission_rate" class="form-control" value="{{ old('commission_rate', $siteSettings['commission_rate']) }}" min="0" max="100">
                                        <span class="input-group-text">% Per Booking</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: PAYMENT GATEWAYS --}}
                    <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                        <div class="stockifly-card p-4 mb-3">
                            <div class="form-section-title mb-3 pb-2 border-bottom fw-bold text-primary">
                                <i class="fa-solid fa-credit-card me-1"></i> Payment Gateways &amp; Merchant Vault
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="enable_bkash" id="checkBkash" {{ $siteSettings['enable_bkash'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="checkBkash">bKash Merchant</label>
                                        </div>
                                        <small class="text-muted d-block">Enable bKash Checkout &amp; Direct Pay API</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="enable_nagad" id="checkNagad" {{ $siteSettings['enable_nagad'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="checkNagad">Nagad Direct</label>
                                        </div>
                                        <small class="text-muted d-block">Enable Nagad Payment Gateway Integration</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="enable_card" id="checkCard" {{ $siteSettings['enable_card'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="checkCard">SSLCommerz / Cards</label>
                                        </div>
                                        <small class="text-muted d-block">Enable Visa, MasterCard &amp; Internet Banking</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 5: MAIL & SMTP SERVER --}}
                    <div class="tab-pane fade" id="tab-mail" role="tabpanel">
                        <div class="stockifly-card p-4 mb-3">
                            <div class="form-section-title mb-3 pb-2 border-bottom fw-bold text-primary">
                                <i class="fa-solid fa-paper-plane me-1"></i> Mail &amp; SMTP Configuration
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SMTP Mail Host</label>
                                    <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', $siteSettings['mail_host']) }}" placeholder="smtp.mailtrap.io">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">SMTP Port</label>
                                    <input type="text" name="mail_port" class="form-control" value="{{ old('mail_port', $siteSettings['mail_port']) }}" placeholder="587">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Encryption</label>
                                    <select name="mail_encryption" class="form-select">
                                        <option value="tls" {{ $siteSettings['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $siteSettings['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SMTP Username</label>
                                    <input type="text" name="mail_username" class="form-control" value="{{ old('mail_username', $siteSettings['mail_username']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SMTP Password</label>
                                    <input type="password" name="mail_password" class="form-control" value="{{ old('mail_password', $siteSettings['mail_password']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Sender Name</label>
                                    <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $siteSettings['mail_from_name']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Sender Email Address</label>
                                    <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $siteSettings['mail_from_address']) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 6: SYSTEM & SECURITY --}}
                    <div class="tab-pane fade" id="tab-system" role="tabpanel">
                        <div class="stockifly-card p-4 mb-3">
                            <div class="form-section-title mb-3 pb-2 border-bottom fw-bold text-primary">
                                <i class="fa-solid fa-shield-halved me-1"></i> System Maintenance &amp; Cache Utility
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light mb-3">
                                <div>
                                    <strong class="d-block text-dark">Clear System Cache &amp; Compiled Views</strong>
                                    <small class="text-muted">Purges all cached configuration, routes, and compiled views</small>
                                </div>
                                <button type="button" class="btn btn-outline-warning btn-sm fw-bold px-3" onclick="document.getElementById('flushCacheForm').submit()">
                                    <i class="fa-solid fa-rotate me-1"></i> Flush Cache Now
                                </button>
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
.saas-settings-tabs .nav-link {
    display: flex;
    align-items: center;
    padding: 12px 14px;
    border-radius: 4px !important;
    color: #475569;
    background: transparent;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    margin-bottom: 4px;
}
.saas-settings-tabs .nav-link:hover {
    background: #f8fafc;
    color: var(--primary);
}
.saas-settings-tabs .nav-link.active {
    background: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(32,103,225,0.25);
}
.saas-settings-tabs .nav-link i {
    font-size: 18px;
    width: 24px;
    text-align: center;
}
</style>

@endsection
