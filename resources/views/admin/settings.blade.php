@extends('layouts.admin')
@section('title', 'System Settings & Currency | Prime Aviation Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Config</span>
        <span class="sep">-</span><strong style="color:#333;">Currency &amp; System</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">System Settings &amp; Currency Configuration</h1>
        <button class="btn-export-pdf" onclick="document.getElementById('settingsForm').submit()">
            <i class="fa-solid fa-check"></i> Save Settings
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

    <div class="row g-3">
        {{-- Settings Form --}}
        <div class="col-lg-8">
            <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                {{-- Currency & Commission --}}
                <div class="form-card mb-3">
                    <div class="form-section-title">
                        <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Platform Currency &amp; Commission Settings
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Platform Currency <span style="color:#ff4d4f;">*</span></label>
                            <select name="default_currency" class="form-select">
                                <option value="BDT" selected>BDT (৳ — Bangladeshi Taka)</option>
                                <option value="USD">USD ($ — US Dollar)</option>
                                <option value="EUR">EUR (€ — Euro)</option>
                                <option value="SAR">SAR (﷼ — Saudi Riyal)</option>
                                <option value="AED">AED (د.إ — UAE Dirham)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Platform Commission Rate (%) <span style="color:#ff4d4f;">*</span></label>
                            <div style="display:flex;">
                                <input type="number" name="commission_rate" class="form-control" value="12" min="0" max="100" style="border-radius:6px 0 0 6px; border-right:none;">
                                <span class="input-group-text" style="border-radius:0 6px 6px 0; border-left:none;">% Per Booking</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Platform Branding --}}
                <div class="form-card mb-3">
                    <div class="form-section-title">
                        <i class="fa-solid fa-sliders me-1"></i> Platform Branding &amp; Support Info
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Platform Title / Brand Name <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="site_name" class="form-control" value="Prime Aviation &amp; Booking">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Support Contact Phone <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="support_phone" class="form-control" value="+880 1770 887733">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Support Email Address <span style="color:#ff4d4f;">*</span></label>
                            <input type="email" name="support_email" class="form-control" value="support@primeavn.com">
                        </div>
                    </div>
                </div>

                {{-- Payment Gateways --}}
                <div class="form-card mb-3">
                    <div class="form-section-title">
                        <i class="fa-solid fa-credit-card me-1"></i> Active Payment Gateway Control
                    </div>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach([
                            ['enable_bkash', 'bkashCheck', 'bKash Merchant Gateway (MFS)', '#e2136e'],
                            ['enable_nagad', 'nagadCheck', 'Nagad Direct Pay (MFS)', '#f7941e'],
                            ['enable_card', 'cardCheck', 'Visa / Mastercard / Amex Credit Cards', '#1890ff'],
                        ] as $gw)
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:#fafafa; border:1px solid #f0f0f0; border-radius:6px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:8px; height:8px; background:{{ $gw[3] }}; border-radius:50%;"></div>
                                <span style="font-size:13px; font-weight:600; color:#334155;">{{ $gw[2] }}</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="{{ $gw[0] }}" value="1" id="{{ $gw[1] }}" checked style="width:36px; height:18px; cursor:pointer;">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Save button (bottom) --}}
                <div style="display:flex; justify-content:flex-end; gap:8px;">
                    <button type="submit" class="btn-add-primary" style="padding: 7px 28px; font-size:13px;">
                        Save System Settings <i class="fa-solid fa-check ms-1"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- System Health Card --}}
        <div class="col-lg-4">
            <div class="data-table-card mb-3">
                <div class="data-table-card-header">
                    <h6>System Health &amp; Cache</h6>
                    <span class="live-feed-badge">Live Status</span>
                </div>
                <div style="padding:16px;">
                    @foreach([
                        ['Laravel Framework', 'v11.x', 'active'],
                        ['PHP Engine', 'v8.2.12', 'active'],
                        ['MySQL Database', 'Connected', 'confirmed'],
                        ['Cache Status', 'Healthy', 'confirmed'],
                        ['Queue Worker', 'Running', 'active'],
                    ] as $sys)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:7px 0; border-bottom:1px solid #f0f0f0; font-size:12.5px;">
                        <span style="color:#8c8c8c;">{{ $sys[0] }}</span>
                        <span class="badge-status {{ $sys[2] }}">{{ $sys[1] }}</span>
                    </div>
                    @endforeach
                    <div style="margin-top:14px;">
                        <button type="button" class="btn-table-action danger" style="width:100%; justify-content:center; padding:8px;" onclick="alert('System caches cleared successfully!');">
                            Clear System Cache <i class="fa-solid fa-broom ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- SaaS Plan Summary --}}
            <div class="data-table-card">
                <div class="data-table-card-header">
                    <h6>Active SaaS Plans</h6>
                </div>
                <div style="padding:14px;">
                    @foreach([
                        ['Starter', 'BDT 2,500/mo', '3 vendors', '#28c76f'],
                        ['Professional', 'BDT 6,000/mo', '7 vendors', '#1890ff'],
                        ['Enterprise', 'BDT 15,000/mo', '2 vendors', '#7367f0'],
                    ] as $plan)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:12.5px;">
                        <div>
                            <strong style="color:#1e293b; font-size:13px; display:block;">{{ $plan[0] }}</strong>
                            <span style="color:#8c8c8c; font-size:11px;">{{ $plan[2] }}</span>
                        </div>
                        <span style="font-weight:700; color:{{ $plan[3] }}; font-size:12.5px;">{{ $plan[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
