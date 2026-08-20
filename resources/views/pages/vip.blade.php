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

/* 1. Main User Status Hero Card (Ultra-Modern High-Elevation 3D Shadow) */
.agoda-vip-status-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(15, 23, 42, 0.06), 0 2px 4px rgba(15, 23, 42, 0.03);
    border: none;
    padding: 34px 38px 30px 38px;
    margin-bottom: 24px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.agoda-vip-status-card:hover {
    box-shadow: 0 25px 55px -15px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(15, 23, 42, 0.08), 0 4px 8px rgba(15, 23, 42, 0.04);
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
    box-shadow: 0 4px 14px rgba(92, 92, 214, 0.3);
}

.vip-badge-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 3px;
    overflow: hidden;
    height: 19px;
    font-size: 11px;
    line-height: 1;
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
    padding: 0 8px 0 4px;
    height: 100%;
    display: flex;
    align-items: center;
    font-weight: 700;
    font-size: 11px;
    margin-left: -3px;
    letter-spacing: 0.2px;
}

/* Stepper Track */
.vip-stepper-track-container {
    position: relative;
    padding: 22px 0 8px 0;
}
.vip-stepper-dashed-line {
    position: absolute;
    top: 37px;
    left: 45px;
    right: 45px;
    height: 2px;
    border-top: 2px dashed #cbd5e1;
    z-index: 1;
}
.vip-stepper-node {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}
.vip-step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    transition: all 0.2s ease;
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
    background: #f0f7ff;
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13.5px;
    color: #1e3a8a;
    border: 1px solid #dbeafe;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.agoda-vip-notice-badge {
    background: #e11d48;
    color: #ffffff;
    font-size: 10.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 4px;
    line-height: 1.2;
    margin-top: 1px;
}

/* 3. Illustration & Explain Box */
.agoda-vip-explain-box {
    background: #283344;
    border-radius: 16px;
    color: #ffffff;
    padding: 44px 40px 36px 40px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(40, 51, 68, 0.15);
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
    border-radius: 20px;
    box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(15, 23, 42, 0.05), 0 2px 4px rgba(15, 23, 42, 0.02);
    border: none;
    padding: 40px 36px;
    margin-bottom: 32px;
}
.vip-table {
    width: 100%;
    border-collapse: collapse;
}
.vip-table th, .vip-table td {
    padding: 15px 14px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
}
.vip-table tr:hover td {
    background-color: #fafbfc;
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
    border-radius: 20px;
    box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(15, 23, 42, 0.05), 0 2px 4px rgba(15, 23, 42, 0.02);
    border: none;
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.vip-btn-search {
    background-color: #1877f2;
    color: #ffffff;
    font-weight: 700;
    font-size: 15.5px;
    border-radius: 999px;
    padding: 13px 44px;
    border: none;
    transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 4px 14px rgba(24, 119, 242, 0.35);
}
.vip-btn-search:hover {
    background-color: #166fe5;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(24, 119, 242, 0.45);
}
</style>

<div class="py-4" style="background-color: #f4f6fa; min-height: 90vh;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 24px 0 36px;">
        <div class="row g-4 justify-content-start">
            
            <!-- Left White Sidebar Navigation (1:1 Exact Match of Agoda Live) -->
            <div class="col-lg-3 col-md-4" style="max-width: 270px; padding-right: 12px;">
                <x-user-sidebar activePage="vip" />
            </div>

            <!-- Right Column: AgodaVIP Loyalty Dashboard & Benefits Track -->
            <div class="col-lg-9 col-md-8" style="padding-left: 16px;">

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

        {{-- ── 3. WHAT IS AGODAVIP? EXPLAIN BOX (Clean Crisp Card) ── --}}
        <div class="card border p-4 mb-4 shadow-xs" style="border-radius: 16px !important; background: #ffffff; border-color: #e2e8f0 !important;">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2 class="fw-bold mb-2" style="font-size: 24px; color: #0f172a; letter-spacing: -0.3px;">What is AgodaVIP?</h2>
                    <p style="font-size: 14.5px; line-height: 1.6; color: #475569; margin-bottom: 0;">
                        AgodaVIP is our loyalty program for rewarding our most loyal customers with amazing deals. Once you qualify, you'll join automatically.
                    </p>
                </div>
                <div class="col-md-5 text-center mt-3 mt-md-0">
                    {{-- 100% Authentic Agoda Podium Stage Vector SVG --}}
                    <svg viewBox="0 0 280 140" style="max-width: 250px; width: 100%; height: auto; overflow: visible;">
                        {{-- Podium Stage Bases --}}
                        <rect x="95" y="85" width="90" height="55" rx="4" fill="#ecc43a" />
                        <text x="140" y="120" fill="#78350f" font-size="18" font-weight="900" text-anchor="middle">1</text>
                        
                        <rect x="25" y="100" width="70" height="40" rx="4" fill="#cbd5e1" />
                        <text x="60" y="126" fill="#475569" font-size="16" font-weight="900" text-anchor="middle">2</text>
                        
                        <rect x="185" y="110" width="70" height="30" rx="4" fill="#d97706" />
                        <text x="220" y="130" fill="#ffffff" font-size="14" font-weight="900" text-anchor="middle">3</text>

                        {{-- 1st Place Purple Mascot (Gold Medal Winner) --}}
                        <g transform="translate(110, 35)">
                            <circle cx="30" cy="30" r="24" fill="#8b5cf6" />
                            <circle cx="23" cy="26" r="2.5" fill="#1e1b4b" />
                            <circle cx="37" cy="26" r="2.5" fill="#1e1b4b" />
                            <path d="M 24 34 Q 30 40 36 34" stroke="#1e1b4b" stroke-width="2" fill="none" stroke-linecap="round" />
                            <circle cx="18" cy="32" r="2.5" fill="#f472b6" opacity="0.6" />
                            <circle cx="42" cy="32" r="2.5" fill="#f472b6" opacity="0.6" />
                            <path d="M 12 38 L 2 55 L 8 58 Z" fill="#ef4444" />
                            <circle cx="4" cy="58" r="7" fill="#fbbf24" stroke="#d97706" stroke-width="1.5" />
                            <text x="4" y="61" fill="#78350f" font-size="7" font-weight="900" text-anchor="middle">★</text>
                            <path d="M 12 36 Q 4 45 6 52" stroke="#6d28d9" stroke-width="3" fill="none" stroke-linecap="round" />
                        </g>

                        {{-- 2nd Place Cyan Mascot (Silver Medal Winner) --}}
                        <g transform="translate(35, 55)">
                            <circle cx="25" cy="25" r="20" fill="#06b6d4" />
                            <circle cx="20" cy="22" r="2" fill="#083344" />
                            <circle cx="30" cy="22" r="2" fill="#083344" />
                            <path d="M 21 28 Q 25 33 29 28" stroke="#083344" stroke-width="1.8" fill="none" stroke-linecap="round" />
                            <circle cx="25" cy="36" r="5" fill="#e2e8f0" stroke="#94a3b8" stroke-width="1" />
                            <path d="M 18 45 L 18 50" stroke="#0e7490" stroke-width="2.5" stroke-linecap="round" />
                            <path d="M 32 45 L 32 50" stroke="#0e7490" stroke-width="2.5" stroke-linecap="round" />
                        </g>

                        {{-- 3rd Place Green Mascot (Bronze Winner) --}}
                        <g transform="translate(195, 72)">
                            <circle cx="25" cy="22" r="17" fill="#10b981" />
                            <circle cx="21" cy="19" r="1.8" fill="#022c22" />
                            <circle cx="29" cy="19" r="1.8" fill="#022c22" />
                            <path d="M 22 25 Q 25 28 28 25" stroke="#022c22" stroke-width="1.5" fill="none" stroke-linecap="round" />
                            <path d="M 19 38 L 19 42" stroke="#047857" stroke-width="2.5" stroke-linecap="round" />
                            <path d="M 31 38 L 31 42" stroke="#047857" stroke-width="2.5" stroke-linecap="round" />
                        </g>
                    </svg>
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
                            <th style="vertical-align: middle;"></th>
                            <th style="vertical-align: middle; min-width: 90px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1.5" style="height: 60px;">
                                    <span class="fw-bold text-dark" style="font-size: 13.5px; white-space: nowrap;">VIP Bronze</span>
                                    <span style="width: 26px; height: 26px; border-radius: 50%; background: #ba6d4a; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">★</span>
                                </div>
                            </th>
                            <th style="vertical-align: middle; min-width: 90px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1.5" style="height: 60px;">
                                    <span class="text-muted" style="font-size: 13px; white-space: nowrap;">VIP Silver <i class="fa-solid fa-lock" style="font-size: 10px;"></i></span>
                                    <span style="width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">★</span>
                                </div>
                            </th>
                            <th style="vertical-align: middle; min-width: 90px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1.5" style="height: 60px;">
                                    <span class="text-muted" style="font-size: 13px; white-space: nowrap;">VIP Gold <i class="fa-solid fa-lock" style="font-size: 10px;"></i></span>
                                    <span style="width: 26px; height: 26px; border-radius: 50%; background: #fef08a; color: #d97706; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">★</span>
                                </div>
                            </th>
                            <th style="vertical-align: middle; min-width: 90px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1.5" style="height: 60px;">
                                    <span class="text-muted" style="font-size: 13px; white-space: nowrap;">VIP Platinum <i class="fa-solid fa-lock" style="font-size: 10px;"></i></span>
                                    <span style="width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">★</span>
                                </div>
                            </th>
                            <th style="vertical-align: middle; min-width: 90px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1.5" style="height: 60px;">
                                    <span class="text-muted" style="font-size: 13px; white-space: nowrap;">VIP Diamond <i class="fa-solid fa-lock" style="font-size: 10px;"></i></span>
                                    <span style="width: 26px; height: 26px; border-radius: 50%; background: #f3e8ff; color: #9333ea; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">★</span>
                                </div>
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

        {{-- ── 5. READY TO BOOK A VIP DEAL? WITH TELESCOPE MASCOT ── --}}
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

            {{-- Exact Agoda Mascot with Telescope & Hotel Search Preview --}}
            <div class="d-inline-flex align-items-center justify-content-center position-relative p-4 border rounded-3 bg-white mb-4 shadow-xs" style="max-width: 540px; width: 100%; min-height: 160px; background: #fafafa;">
                
                {{-- Yellow Mascot with Telescope --}}
                <svg viewBox="0 0 100 90" style="width: 90px; height: 80px; flex-shrink: 0; margin-right: 15px;">
                    {{-- Body --}}
                    <circle cx="45" cy="45" r="30" fill="#f59e0b" />
                    {{-- Eyes & Smile --}}
                    <circle cx="36" cy="40" r="3" fill="#451a03" />
                    <circle cx="50" cy="40" r="3" fill="#451a03" />
                    <path d="M 38 48 Q 44 54 50 48" stroke="#451a03" stroke-width="2" fill="none" stroke-linecap="round" />
                    {{-- Cheeks --}}
                    <circle cx="30" cy="46" r="3" fill="#f97316" opacity="0.6" />
                    <circle cx="56" cy="46" r="3" fill="#f97316" opacity="0.6" />
                    {{-- Telescope Tripod & Lens --}}
                    <path d="M 70 42 L 88 28 L 92 34 L 72 48 Z" fill="#475569" />
                    <circle cx="90" cy="31" r="5" fill="#38bdf8" opacity="0.8" />
                    {{-- Tripod Legs --}}
                    <line x1="72" y1="48" x2="65" y2="78" stroke="#64748b" stroke-width="2" />
                    <line x1="72" y1="48" x2="80" y2="78" stroke="#64748b" stroke-width="2" />
                    {{-- Mascot Hands holding tripod --}}
                    <circle cx="68" cy="46" r="4" fill="#d97706" />
                    {{-- Mascot Legs --}}
                    <line x1="38" y1="75" x2="38" y2="84" stroke="#b45309" stroke-width="3" stroke-linecap="round" />
                    <line x1="52" y1="75" x2="52" y2="84" stroke="#b45309" stroke-width="3" stroke-linecap="round" />
                </svg>

                {{-- Magnified Deal Card --}}
                <div class="border rounded-3 p-3 bg-white text-start shadow-sm" style="flex: 1; border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <strong style="font-size: 13.5px; color: #1e293b;">Agoda Hotel</strong>
                        <div>
                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 11px;">100</span>
                            <strong style="color: #dc2626; font-size: 16px; margin-left: 4px;">85</strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="vip-badge-pill" style="height: 16px; font-size: 9.5px;">
                            <div class="vip-tag" style="padding: 0 4px;">AgodaVIP★</div>
                        </div>
                        <span class="badge" style="background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; font-size: 10px; font-weight: 700;">15% OFF</span>
                    </div>
                </div>

            </div>

        </div>

        {{-- ── 6. VIP PROGRAM DISCLAIMER ACCORDION (1:1 Agoda Closed by Default) ── --}}
        <div class="vip-disclaimer-box" style="padding: 14px 20px; border-radius: 8px; background: #ffffff; border: 1px solid #e2e8f0; margin-bottom: 28px;">
            <details class="vip-disclaimer-details">
                <summary class="d-flex align-items-center justify-content-between fw-bold text-dark" style="cursor: pointer; font-size: 13.5px; outline: none; list-style: none;">
                    <span style="color: #334155; font-weight: 600;">VIP Program Disclaimer</span>
                    <i class="fa-solid fa-chevron-down text-secondary disclaimer-chevron" style="font-size: 11px; transition: transform 0.2s ease;"></i>
                </summary>
                <div class="mt-2.5 pt-2.5 border-top text-secondary" style="font-size: 12.5px; line-height: 1.6; color: #64748b;">
                    <p class="mb-1.5">The discounts shown in the VIP Program table represent the maximum discount that may be offered to VIP members. The maximum discount possible is not necessarily always offered.</p>
                    <p class="mb-1.5">VIP discounts apply only to room offers marked with a “VIP” badge. The discount stated represents the final price (after applying any applicable Cashback) to VIP members against the final price (after applying any applicable Cashback) to non-Agoda members by hotels under identical conditions. Cashback refers to the cashback rewards provided by Agoda to bookers as a loyalty gift under the separate Agoda Cashback Rewards Program.</p>
                    <p class="mb-1.5">Different VIP tiers may receive the same discount for the same room offer. The level of discount will be decided by the participating hotel and/or Agoda.</p>
                    <p class="mb-0">Please note that the discount available to VIP members under the same booking conditions may also be subject to other applicable promotion offered by Agoda, participating hotels and/or third parties, and other relevant factors as determined by Agoda.</p>
                </div>
            </details>
        </div>

        {{-- ── 7. START A NEW SEARCH BUTTON ── --}}
        <div class="text-center mb-2">
            <a href="{{ route('search.index') }}" class="vip-btn-search">
                Start a new search
            </a>
        </div>

            </div>
        </div>
    </div>
</div>
@endsection
