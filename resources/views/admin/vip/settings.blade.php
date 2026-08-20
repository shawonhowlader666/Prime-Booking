@extends('layouts.admin')
@section('title', 'VIP Loyalty Program Settings | PRIME BOOKING Admin')

@section('content')
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">VIP Loyalty Program</strong>
        <span class="sep">-</span><span style="color:#666;">Tier Rules &amp; Discounts</span>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title"><i class="fa-solid fa-crown text-warning me-2"></i> VIP Loyalty Program Rules &amp; Tier Discounts</h1>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('vip') }}" target="_blank" class="btn-table-action primary" style="padding:6px 14px;">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live VIP Page
            </a>
            <a href="{{ route('admin.vip.members') }}" class="btn-table-action" style="padding:6px 14px;">
                <i class="fa-solid fa-users me-1"></i> View VIP Member Roster
            </a>
        </div>
    </div>
</div>

<div class="page-content-area">
    <div style="max-width:1000px; margin:0 auto;">

        @if(session('success'))
            <div class="admin-alert success mb-3">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.vip.settings.update') }}" method="POST">
            @csrf

            {{-- 1. TIER QUALIFICATION THRESHOLDS --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-sliders me-1"></i> Tier Qualification Rules (2-Year Dynamic Window)
                </div>
                <p class="text-muted" style="font-size:12.5px; margin-bottom:18px;">
                    Set the required number of completed bookings and eligible spend within the last 24 months for automatic tier upgrades.
                </p>

                <div class="row g-3">
                    {{-- Silver --}}
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <span class="badge bg-secondary mb-2" style="font-size:12px;">★ VIP Silver</span>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">Min Bookings</label>
                                <input type="number" name="vip_silver_threshold" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_silver_threshold'] ?? 2 }}" required min="1">
                            </div>
                            <small class="text-muted d-block">Default: 2 Bookings</small>
                        </div>
                    </div>

                    {{-- Gold --}}
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <span class="badge bg-warning text-dark mb-2" style="font-size:12px;">★ VIP Gold</span>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">Min Bookings</label>
                                <input type="number" name="vip_gold_threshold" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_gold_threshold'] ?? 5 }}" required min="1">
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">OR Spend ($)</label>
                                <input type="number" name="vip_gold_spend" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_gold_spend'] ?? 200 }}" required min="0">
                            </div>
                        </div>
                    </div>

                    {{-- Platinum --}}
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <span class="badge bg-dark text-white mb-2" style="font-size:12px;">★ VIP Platinum</span>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">Min Bookings</label>
                                <input type="number" name="vip_platinum_threshold" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_platinum_threshold'] ?? 10 }}" required min="1">
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">OR Spend ($)</label>
                                <input type="number" name="vip_platinum_spend" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_platinum_spend'] ?? 400 }}" required min="0">
                            </div>
                        </div>
                    </div>

                    {{-- Diamond --}}
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <span class="badge mb-2" style="background:#9333ea; color:#fff; font-size:12px;">★ VIP Diamond</span>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">Min Bookings</label>
                                <input type="number" name="vip_diamond_threshold" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_diamond_threshold'] ?? 15 }}" required min="1">
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-dark fw-bold">AND Spend ($)</label>
                                <input type="number" name="vip_diamond_spend" class="form-control text-center fw-bold" value="{{ $vipSettings['vip_diamond_spend'] ?? 1500 }}" required min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TIER DISCOUNT PERCENTAGES --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-percent me-1"></i> Automatic Checkout Discount Percentages
                </div>
                <p class="text-muted" style="font-size:12.5px; margin-bottom:18px;">
                    VIP members automatically receive these maximum discount rates during hotel &amp; resort bookings.
                </p>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">VIP Silver Discount (%)</label>
                        <div class="input-group">
                            <input type="number" name="vip_silver_discount" class="form-control fw-bold text-center" value="{{ $vipSettings['vip_silver_discount'] ?? 12 }}" min="0" max="100" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">VIP Gold Discount (%)</label>
                        <div class="input-group">
                            <input type="number" name="vip_gold_discount" class="form-control fw-bold text-center" value="{{ $vipSettings['vip_gold_discount'] ?? 18 }}" min="0" max="100" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">VIP Platinum Discount (%)</label>
                        <div class="input-group">
                            <input type="number" name="vip_platinum_discount" class="form-control fw-bold text-center" value="{{ $vipSettings['vip_platinum_discount'] ?? 25 }}" min="0" max="100" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">VIP Diamond Discount (%)</label>
                        <div class="input-group">
                            <input type="number" name="vip_diamond_discount" class="form-control fw-bold text-center" value="{{ $vipSettings['vip_diamond_discount'] ?? 25 }}" min="0" max="100" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="submit" class="btn-add-primary" style="padding:8px 24px; font-size:13.5px;">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save VIP Configuration
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
