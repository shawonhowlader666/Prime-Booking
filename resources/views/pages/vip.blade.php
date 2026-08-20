@extends('layouts.main', ['activePage' => 'vip'])

@section('title', 'AgodaVIP - Exclusive Member Rewards & Deals | Prime Booking')
@section('meta_description', 'Unlock exclusive VIP discounts, insider deals, free breakfast and room upgrades with the PrimeVIP Loyalty Program.')

@section('content')
@php
    $user = auth()->user();
    $currency = \App\Helpers\CurrencyHelper::current();
    
    // Dynamic Booking Count in last 2 years (High-performance SQL query)
    $userBookings = $user ? \App\Models\Booking::where(function($q) use ($user) {
        $q->where('user_id', $user->id)->orWhere('guest_email', $user->email);
    })->where('created_at', '>=', now()->subYears(2))->whereNotIn('booking_status', ['cancelled'])->count() : 0;

    // Dynamic Spend in last 2 years
    $userSpend = $user ? (float) \App\Models\Booking::where(function($q) use ($user) {
        $q->where('user_id', $user->id)->orWhere('guest_email', $user->email);
    })->where('created_at', '>=', now()->subYears(2))->whereIn('payment_status', ['paid', 'completed'])->sum('total_amount') : 0;

    // Thresholds
    $silverReq    = $vipThresholds['silver'] ?? 2;
    $goldReq      = $vipThresholds['gold'] ?? 5;
    $goldSpend    = 200;
    $platReq      = $vipThresholds['platinum'] ?? 10;
    $platSpend    = 400;
    $diamondReq   = $vipThresholds['diamond'] ?? 15;
    $diamondSpend = 1500;

    // Determine Active Tier
    if ($userBookings >= $diamondReq && $userSpend >= $diamondSpend) {
        $currentTier = 'Diamond';
        $tierNameFull = 'AgodaVIP Diamond';
        $activeBadgeColor = '#9333ea';
    } elseif ($userBookings >= $platReq || $userSpend >= $platSpend) {
        $currentTier = 'Platinum';
        $tierNameFull = 'AgodaVIP Platinum';
        $activeBadgeColor = '#64748b';
    } elseif ($userBookings >= $goldReq || $userSpend >= $goldSpend) {
        $currentTier = 'Gold';
        $tierNameFull = 'AgodaVIP Gold';
        $activeBadgeColor = '#d97706';
    } elseif ($userBookings >= $silverReq) {
        $currentTier = 'Silver';
        $tierNameFull = 'AgodaVIP Silver';
        $activeBadgeColor = '#475569';
    } else {
        $currentTier = 'Bronze';
        $tierNameFull = 'AgodaVIP Bronze';
        $activeBadgeColor = '#ba6d4a';
    }
@endphp

<style>
/* Agoda 1:1 VIP Exact Pixel-Perfect Styling */
.vip-page-wrapper {
    background-color: #f7f9fa;
    min-height: 90vh;
    padding: 36px 0 70px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.vip-container {
    max-width: 940px;
    margin: 0 auto;
    padding: 0 16px;
}

/* 1. Main User Status Hero Card */
.agoda-vip-status-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #edf2f7;
    padding: 32px 36px 28px 36px;
    margin-bottom: 24px;
}

.vip-avatar-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background-color: #5c5cd6;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    font-weight: 700;
    flex-shrink: 0;
}

.vip-badge-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 3px;
    overflow: hidden;
    height: 18px;
    font-size: 10.5px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.12);
}
.vip-badge-pill .vip-tag {
    background-color: #1e2430;
    color: #ffffff;
    padding: 0 6px 0 5px;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 3px;
    font-weight: 800;
    clip-path: polygon(0 0, 100% 0, 84% 100%, 0 100%);
    padding-right: 9px;
}
.vip-badge-pill .vip-tier-name {
    background-color: #ba6d4a;
    color: #ffffff;
    padding: 0 7px 0 4px;
    height: 100%;
    display: flex;
    align-items: center;
    font-weight: 700;
    font-size: 11px;
    margin-left: -3px;
}

/* Stepper Track */
.vip-stepper-track-container {
    position: relative;
    padding: 20px 0 5px 0;
}
.vip-stepper-dashed-line {
    position: absolute;
    top: 36px;
    left: 45px;
    right: 45px;
    height: 2px;
    border-top: 2px dashed #e2e8f0;
    z-index: 1;
}
.vip-stepper-node {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}
.vip-step-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.vip-step-circle.active-bronze {
    background: #ba6d4a;
    border-color: #ba6d4a;
    color: #ffffff;
}
.vip-step-circle.active-tier {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}

/* 2. Notice Callout Pill */
.agoda-vip-notice-banner {
    background: #eff6ff;
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13.5px;
    color: #1e3a8a;
    border: 1px solid #dbeafe;
}
.agoda-vip-notice-badge {
    background: #e11d48;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 4px;
    line-height: 1.2;
    margin-top: 2px;
}

/* 3. Illustration & Explain Box */
.agoda-vip-explain-box {
    background: #283344;
    border-radius: 16px;
    color: #ffffff;
    padding: 44px 40px 36px 40px;
    margin-bottom: 36px;
    position: relative;
    overflow: hidden;
}
.agoda-vip-explain-box::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.4;
    pointer-events: none;
}

/* 4. Comparison Table */
.vip-comparison-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #edf2f7;
    padding: 40px 36px;
    margin-bottom: 32px;
}
.vip-table {
    width: 100%;
    border-collapse: collapse;
}
.vip-table th, .vip-table td {
    padding: 14px 14px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
}
.vip-table th {
    text-align: center;
    font-weight: 700;
    color: #334155;
    background: #ffffff;
    border-bottom: 2px solid #e2e8f0;
}
.vip-table td {
    color: #475569;
}
.vip-table td:first-child {
    font-weight: 500;
    color: #1e293b;
    width: 35%;
}
.vip-table td:not(:first-child) {
    text-align: center;
    width: 13%;
}

/* 5. Booking Promo Showcase */
.vip-promo-showcase-box {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #edf2f7;
    padding: 40px 36px;
    margin-bottom: 24px;
    text-align: center;
}

/* 6. Disclaimer Accordion */
.vip-disclaimer-box {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 18px 24px;
    margin-bottom: 36px;
}
.vip-btn-search {
    background-color: #1877f2;
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    border-radius: 999px;
    padding: 12px 42px;
    border: none;
    transition: background 0.15s ease, transform 0.1s ease;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 2px 8px rgba(24, 119, 242, 0.25);
}
.vip-btn-search:hover {
    background-color: #166fe5;
    color: #ffffff;
    transform: translateY(-1px);
}
</style>

<div class="vip-page-wrapper">
    <div class="vip-container">

        {{-- ── 1. MAIN USER STATUS HERO CARD (1:1 Agoda Exact) ── --}}
        <div class="agoda-vip-status-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="vip-avatar-circle">
                    {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <h3 class="fw-bold mb-1" style="font-size: 21px; color: #1e293b;">Hi {{ $user->name ?? 'Shawon' }}</h3>
                    <div class="vip-badge-pill">
                        <div class="vip-tag"><span style="font-size: 7.5px;">★</span>VIP</div>
                        <div class="vip-tier-name" style="background-color: {{ $activeBadgeColor }};">{{ $currentTier }}</div>
                    </div>
                </div>
            </div>

            {{-- 3 Key Metric Columns (Status / Expires On / Eligible Spend) --}}
            <div class="row g-0 py-3 border-top border-bottom text-center mb-4">
                <div class="col-4 border-end">
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">Your status</div>
                    <div class="fw-bold text-dark" style="font-size: 21px; letter-spacing: -0.2px;">{{ $tierNameFull }}</div>
                </div>
                <div class="col-4 border-end">
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">Status expires on</div>
                    <div class="fw-bold text-dark" style="font-size: 18px;">—</div>
                </div>
                <div class="col-4">
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">Eligible spend in last 2 years</div>
                    <div class="fw-bold text-dark" style="font-size: 21px;">${{ number_format($userSpend) }}</div>
                </div>
            </div>

            {{-- Progress Title & Booking Count --}}
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="fw-semibold" style="font-size: 13.5px; color: #475569;">Progress to AgodaVIP Status</div>
                <div style="font-size: 13px; color: #1e293b; font-weight: 700;">
                    {{ $userBookings }}/15 bookings completed in last 2 years 
                    <i class="fa-solid fa-circle-info text-secondary ms-1" style="font-size: 12px; cursor: pointer;" title="Completed bookings within the last 24 months count towards VIP tier progression."></i>
                </div>
            </div>

            {{-- Stepper Progress Track (Dashed Line & 5 Nodes) --}}
            <div class="vip-stepper-track-container">
                <div class="vip-stepper-dashed-line"></div>
                <div class="d-flex justify-content-between align-items-start">
                    
                    {{-- Bronze Node --}}
                    <div class="vip-stepper-node">
                        <div class="vip-step-circle active-bronze">
                            <i class="fa-solid fa-star" style="font-size: 10px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">Bronze</div>
                        <div class="fw-bold" style="font-size: 12px; color: #16a34a;">Member</div>
                    </div>

                    {{-- Silver Node --}}
                    <div class="vip-stepper-node">
                        <div class="vip-step-circle {{ $userBookings >= 2 ? 'active-tier' : '' }}">
                            <i class="fa-solid fa-star" style="font-size: 10px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">VIP Silver</div>
                        <div style="font-size: 12px; color: #64748b;">2 bookings</div>
                    </div>

                    {{-- Gold Node --}}
                    <div class="vip-stepper-node">
                        <div class="vip-step-circle {{ $userBookings >= 5 || $userSpend >= 200 ? 'active-tier' : '' }}">
                            <i class="fa-solid fa-star" style="font-size: 10px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">VIP Gold</div>
                        <div style="font-size: 12px; color: #64748b;">5 bookings</div>
                        <div style="font-size: 11px; color: #94a3b8;">or</div>
                        <div style="font-size: 12px; color: #64748b;">$200 spent</div>
                    </div>

                    {{-- Platinum Node --}}
                    <div class="vip-stepper-node">
                        <div class="vip-step-circle {{ $userBookings >= 10 || $userSpend >= 400 ? 'active-tier' : '' }}">
                            <i class="fa-solid fa-star" style="font-size: 10px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">VIP Platinum</div>
                        <div style="font-size: 12px; color: #64748b;">10 bookings</div>
                        <div style="font-size: 11px; color: #94a3b8;">or</div>
                        <div style="font-size: 12px; color: #64748b;">$400 spent</div>
                    </div>

                    {{-- Diamond Node --}}
                    <div class="vip-stepper-node">
                        <div class="vip-step-circle {{ $userBookings >= 15 && $userSpend >= 1500 ? 'active-tier' : '' }}">
                            <i class="fa-solid fa-star" style="font-size: 10px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">VIP Diamond</div>
                        <div style="font-size: 12px; color: #64748b;">15 bookings</div>
                        <div style="font-size: 11px; color: #94a3b8;">and</div>
                        <div style="font-size: 12px; color: #64748b;">$1500 spent</div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── 2. NEW PROMO NOTICE BANNER ── --}}
        <div class="agoda-vip-notice-banner">
            <span class="agoda-vip-notice-badge">New</span>
            <div style="line-height: 1.45;">
                <strong style="color: #1d4ed8; font-size: 13.5px;">Book Flights and Activities to upgrade your VIP level faster <i class="fa-solid fa-square-arrow-up-right ms-0.5"></i></strong><br>
                <span style="color: #4b5563; font-size: 13px;">Now in addition to stays, Flights and Activities bookings, completed in last 2 years, count towards your progress VIP 🎉</span>
            </div>
        </div>

        {{-- ── 3. WHAT IS AGODAVIP? EXPLAIN BOX WITH ILLUSTRATION ── --}}
        <div class="agoda-vip-explain-box">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2 class="fw-bold mb-3" style="font-size: 26px; color: #ffffff; letter-spacing: -0.3px;">What is AgodaVIP?</h2>
                    <p style="font-size: 15px; line-height: 1.6; color: #e2e8f0; margin-bottom: 0;">
                        AgodaVIP is our loyalty program for rewarding our most loyal customers with amazing deals. Once you qualify, you'll join automatically.
                    </p>
                </div>
                <div class="col-md-5 text-center mt-3 mt-md-0">
                    <div class="d-inline-flex align-items-end gap-3 p-3" style="background: rgba(255,255,255,0.06); border-radius: 16px;">
                        {{-- Purple Winner Character --}}
                        <div style="text-align: center;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: #9333ea; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; box-shadow: 0 4px 12px rgba(147, 51, 234, 0.4);">
                                <span style="font-size: 18px;">🥇</span>
                            </div>
                            <div style="width: 50px; height: 38px; background: #eab308; border-radius: 4px 4px 0 0; color: #000; font-weight: 800; font-size: 12px; display: flex; align-items: center; justify-content: center;">1</div>
                        </div>
                        {{-- Blue 2nd Character --}}
                        <div style="text-align: center;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: #0284c7; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4);">
                                <span style="font-size: 16px;">🥈</span>
                            </div>
                            <div style="width: 44px; height: 26px; background: #94a3b8; border-radius: 4px 4px 0 0; color: #000; font-weight: 800; font-size: 11px; display: flex; align-items: center; justify-content: center;">2</div>
                        </div>
                        {{-- Green 3rd Character --}}
                        <div style="text-align: center;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);">
                                <span style="font-size: 14px;">🥉</span>
                            </div>
                            <div style="width: 40px; height: 18px; background: #d97706; border-radius: 4px 4px 0 0; color: #fff; font-weight: 800; font-size: 10px; display: flex; align-items: center; justify-content: center;">3</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 4. BENEFITS COMPARISON TABLE ── --}}
        <div class="vip-comparison-card">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark mb-2" style="font-size: 22px;">What are the benefits of AgodaVIP?</h3>
                <p class="text-secondary mx-auto mb-0" style="max-width: 700px; font-size: 14px; line-height: 1.6;">
                    You get access to exclusive deals, which are highlighted by the 
                    <span class="vip-badge-pill" style="vertical-align: middle; margin: 0 2px;">
                        <span class="vip-tag">★ VIP</span>
                        <span class="vip-tier-name" style="background-color: #ba6d4a;">Deals</span>
                    </span> 
                    badge. Every time you book one of these, you're getting a special low price that others don't. And we're adding VIP deals on flights soon too!
                </p>
            </div>

            <div class="table-responsive">
                <table class="vip-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <div class="fw-bold text-dark mb-1" style="font-size: 14px;">VIP Bronze</div>
                                <div style="width: 26px; height: 26px; border-radius: 50%; background: #ba6d4a; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin: 0 auto;">★</div>
                            </th>
                            <th>
                                <div class="text-muted mb-1" style="font-size: 13.5px;">VIP Silver <i class="fa-solid fa-lock" style="font-size: 10px;"></i></div>
                                <div style="width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin: 0 auto;">★</div>
                            </th>
                            <th>
                                <div class="text-muted mb-1" style="font-size: 13.5px;">VIP Gold <i class="fa-solid fa-lock" style="font-size: 10px;"></i></div>
                                <div style="width: 26px; height: 26px; border-radius: 50%; background: #fef08a; color: #d97706; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin: 0 auto;">★</div>
                            </th>
                            <th>
                                <div class="text-muted mb-1" style="font-size: 13.5px;">VIP Platinum <i class="fa-solid fa-lock" style="font-size: 10px;"></i></div>
                                <div style="width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin: 0 auto;">★</div>
                            </th>
                            <th>
                                <div class="text-muted mb-1" style="font-size: 13.5px;">VIP Diamond <i class="fa-solid fa-lock" style="font-size: 10px;"></i></div>
                                <div style="width: 26px; height: 26px; border-radius: 50%; background: #f3e8ff; color: #9333ea; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin: 0 auto;">★</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Best price guarantee</td>
                            <td><i class="fa-solid fa-check text-dark fw-bold" style="font-size: 15px;"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>Insider deals</td>
                            <td><i class="fa-solid fa-check text-dark fw-bold" style="font-size: 15px;"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>VIP deals up to 12% off</td>
                            <td><i class="fa-solid fa-xmark text-secondary" style="font-size: 14px;"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>VIP deals up to 18% off</td>
                            <td><i class="fa-solid fa-xmark text-secondary" style="font-size: 14px;"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>VIP deals up to 25% off</td>
                            <td><i class="fa-solid fa-xmark text-secondary" style="font-size: 14px;"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>Free breakfast and other perks on selected properties!</td>
                            <td><i class="fa-solid fa-xmark text-secondary" style="font-size: 14px;"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>Priority Customer Support</td>
                            <td><i class="fa-solid fa-xmark text-secondary" style="font-size: 14px;"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                        <tr>
                            <td>Special Limited-Time Benefits</td>
                            <td><i class="fa-solid fa-xmark text-secondary" style="font-size: 14px;"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-xmark text-muted"></i></td>
                            <td><i class="fa-solid fa-check text-muted"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center text-muted mt-3" style="font-size: 12.5px;">
                Sign in and book to achieve VIP status! Your status will be valid for 6 months and will automatically renew if you make the required bookings to retain status within that time.
            </div>
        </div>

        {{-- ── 5. READY TO BOOK A VIP DEAL? ── --}}
        <div class="vip-promo-showcase-box">
            <h3 class="fw-bold text-dark mb-2" style="font-size: 21px;">Ready to book a VIP deal?</h3>
            <p class="text-secondary mb-4" style="font-size: 14.5px;">
                Just look for the 
                <span class="vip-badge-pill" style="vertical-align: middle; margin: 0 2px;">
                    <span class="vip-tag">★ VIP</span>
                    <span class="vip-tier-name" style="background-color: #ba6d4a;">Deals</span>
                </span> 
                badge and save!
            </p>

            {{-- Mock Agoda Search Card Diagram with Mascot Looking through Telescope --}}
            <div class="d-inline-block position-relative p-4 border rounded-3 bg-light mb-4" style="max-width: 520px; width: 100%;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-weight: 700; font-size: 14px; color: #1e293b;">Agoda Hotel</span>
                    </div>
                    <div>
                        <span style="text-decoration: line-through; color: #94a3b8; font-size: 11px;">100</span>
                        <strong style="color: #d91b42; font-size: 16px; margin-left: 4px;">85</strong>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="vip-badge-pill">
                        <div class="vip-tag">AgodaVIP★</div>
                    </div>
                    <span class="badge bg-danger" style="font-size: 10.5px;">15% OFF</span>
                </div>
            </div>

            <p class="text-muted fst-italic mx-auto mb-0" style="max-width: 680px; font-size: 12px; line-height: 1.55;">
                VIP benefits are subject to change and are not guaranteed on an ongoing basis. Agoda reserves the right to modify or withdraw any VIP benefits at its sole discretion.
            </p>
        </div>

        {{-- ── 6. VIP PROGRAM DISCLAIMER ACCORDION (1:1 Matching Screenshot) ── --}}
        <div class="vip-disclaimer-box">
            <details open>
                <summary class="d-flex align-items-center justify-content-between fw-bold text-dark" style="cursor: pointer; font-size: 14.5px; outline: none; list-style: none;">
                    <span>VIP Program Disclaimer</span>
                    <i class="fa-solid fa-chevron-up text-secondary" style="font-size: 12px;"></i>
                </summary>
                <div class="mt-3 pt-3 border-top text-secondary" style="font-size: 13px; line-height: 1.65; color: #475569;">
                    <p class="mb-2">The discounts shown in the VIP Program table represent the maximum discount that may be offered to VIP members. The maximum discount possible is not necessarily always offered.</p>
                    <p class="mb-2">VIP discounts apply only to room offers marked with a “VIP” badge. The discount stated represents the final price (after applying any applicable Cashback) to VIP members against the final price (after applying any applicable Cashback) to non-Agoda members by hotels under identical conditions. Cashback refers to the cashback rewards provided by Agoda to bookers as a loyalty gift under the separate Agoda Cashback Rewards Program.</p>
                    <p class="mb-2">Different VIP tiers may receive the same discount for the same room offer. The level of discount will be decided by the participating hotel and/or Agoda.</p>
                    <p class="mb-0">Please note that the discount available to VIP members under the same booking conditions may also be subject to other applicable promotion offered by Agoda, participating hotels and/or third parties, and other relevant factors as determined by Agoda.</p>
                </div>
            </details>
        </div>

        {{-- ── 7. START A NEW SEARCH BUTTON ── --}}
        <div class="text-center">
            <a href="{{ route('search.index') }}" class="vip-btn-search">
                Start a new search
            </a>
        </div>

    </div>
</div>
@endsection
