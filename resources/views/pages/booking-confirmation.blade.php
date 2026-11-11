@extends('layouts.main', ['activePage' => 'bookings'])

@php
    use App\Services\CurrencyService;
    use Carbon\Carbon;
    $user = auth()->user();
    $guestName = $booking->guest_name ?? ($user?->name ?? 'raj howlader');
    $parts = explode(' ', trim($guestName), 2);
    $firstName = $parts[0] ?? 'raj';
    $lastName  = $parts[1] ?? 'H.';
@endphp

@section('title', 'Booking is confirmed! — ' . $booking->booking_reference . ' | Prime Booking')
@section('meta_description', 'Your booking for ' . ($booking->property?->name ?? 'Hotel') . ' is confirmed. E-voucher generated.')

@section('content')
<style>
/* Hide default master search header, master footer, and floating widgets on checkout confirmation */
header.navbar,
footer,
.floating-whatsapp-btn,
#mainHeader,
.mobile-bottom-nav,
#socialProofToaster,
.social-proof-toaster,
#recentlyViewedTrigger,
.recently-viewed-trigger,
#recentlyViewedDrawer,
.recently-viewed-drawer,
.pb-card-top-rewards,
.pb-card-bottom-app,
.pb-bottom-fixed-btn,
#pbStickySideTab {
    display: none !important;
}

:root {
    --ag-primary: #1772eb;
    --ag-success-green: #00875a;
    --ag-bg-page: #f8f9fb;
    --ag-card-bg: #ffffff;
    --ag-border-light: #e2e8f0;
    --ag-text-main: #2a2a2e;
    --ag-text-muted: #6b7280;
    --ag-radius-sm: 4px;
    --ag-radius-md: 8px;
    --ag-radius-lg: 12px;
}

body {
    background-color: var(--ag-bg-page) !important;
    color: var(--ag-text-main) !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
}

/* ── Agoda Top Header ── */
.agoda-checkout-header {
    background: var(--ag-card-bg);
    border-bottom: 1px solid var(--ag-border-light);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.agoda-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.agoda-brand-logo {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-decoration: none;
    cursor: pointer;
}

.agoda-brand-text {
    font-size: 27px;
    font-weight: 800;
    letter-spacing: -0.6px;
    color: #2a2a2e;
    line-height: 1;
}

.agoda-brand-dots {
    display: flex;
    gap: 3.5px;
    margin-top: 4px;
}

.agoda-brand-dots span {
    width: 7.5px;
    height: 7.5px;
    border-radius: 50%;
    display: inline-block;
}
.agoda-brand-dots span:nth-child(1) { background-color: #ff2938; }
.agoda-brand-dots span:nth-child(2) { background-color: #fdb812; }
.agoda-brand-dots span:nth-child(3) { background-color: #00b057; }
.agoda-brand-dots span:nth-child(4) { background-color: #af38b1; }
.agoda-brand-dots span:nth-child(5) { background-color: #00aae0; }

/* Stepper - All 3 Steps Completed */
.agoda-stepper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.agoda-step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ag-success-green);
}

.agoda-step-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
    background: var(--ag-success-green);
}

.agoda-step-divider {
    width: 44px;
    height: 2px;
    background-color: var(--ag-success-green);
}

.agoda-user-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    user-select: none;
}

.agoda-user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #6a4ccf;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.agoda-user-name {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ag-text-main);
}

/* ── Confirmation Page Body ── */
.confirmation-wrapper {
    max-width: 800px;
    margin: 32px auto 60px;
    padding: 0 16px;
}

.confirm-card {
    background: #ffffff;
    border-radius: var(--ag-radius-md);
    border: 1px solid var(--ag-border-light);
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
    text-align: center;
    padding: 36px 28px;
}

.success-check-circle {
    width: 72px;
    height: 72px;
    background: #e6f7ef;
    color: var(--ag-success-green);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.confirm-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--ag-text-main);
    margin-bottom: 6px;
}

.confirm-subtitle {
    font-size: 14px;
    color: var(--ag-text-muted);
    margin-bottom: 16px;
}

.booking-ref-badge {
    display: inline-block;
    background: #f1f5f9;
    border: 1.5px dashed #94a3b8;
    padding: 8px 24px;
    border-radius: 8px;
    font-size: 17px;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: var(--ag-primary);
    font-family: monospace;
    margin-bottom: 24px;
}

.confirmed-details-box {
    background: #f8fafc;
    border: 1px solid var(--ag-border-light);
    border-radius: var(--ag-radius-sm);
    padding: 20px 24px;
    text-align: left;
    margin-bottom: 24px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eef2f6;
    font-size: 14px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--ag-text-muted);
}

.detail-val {
    font-weight: 600;
    color: var(--ag-text-main);
}

.confirm-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 24px;
}

.btn-agoda-primary {
    background: var(--ag-primary);
    color: #ffffff;
    border: none;
    border-radius: var(--ag-radius-sm);
    padding: 13px 26px;
    font-size: 14.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-agoda-primary:hover {
    background: #1259b8;
    color: #ffffff;
    transform: translateY(-1px);
}

.btn-agoda-secondary {
    background: #ffffff;
    color: var(--ag-primary);
    border: 1.5px solid var(--ag-primary);
    border-radius: var(--ag-radius-sm);
    padding: 12px 22px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-agoda-secondary:hover {
    background: #f0f7ff;
    color: var(--ag-primary);
}

.agoda-checkout-footer {
    border-top: 1px solid var(--ag-border-light);
    background: #ffffff;
    padding: 24px 0;
    text-align: center;
    color: var(--ag-text-muted);
    font-size: 12px;
}

@media (max-width: 768px) {
    .agoda-stepper {
        display: none;
    }
}
</style>

{{-- Agoda Header with all 3 steps completed --}}
<header class="agoda-checkout-header">
    <div class="agoda-header-inner">
        <a href="{{ route('home') }}" class="d-flex align-items-center" aria-label="Prime Booking Home" style="text-decoration:none;">
            <x-logo height="34" />
        </a>

        <nav class="agoda-stepper">
            <div class="agoda-step-item">
                <span class="agoda-step-num"><i class="fa-solid fa-check"></i></span>
                <span>Customer information</span>
            </div>
            <div class="agoda-step-divider"></div>
            <div class="agoda-step-item">
                <span class="agoda-step-num"><i class="fa-solid fa-check"></i></span>
                <span>Payment information</span>
            </div>
            <div class="agoda-step-divider"></div>
            <div class="agoda-step-item">
                <span class="agoda-step-num"><i class="fa-solid fa-check"></i></span>
                <span>Booking is confirmed!</span>
            </div>
        </nav>

        <div class="agoda-user-badge">
            <div class="agoda-user-avatar">{{ strtoupper(substr($firstName, 0, 1)) }}</div>
            <span class="agoda-user-name">{{ $firstName }} {{ strtoupper(substr($lastName, 0, 1)) }}.</span>
        </div>
    </div>
</header>

<main class="confirmation-wrapper">
    <div class="confirm-card">
        <div class="success-check-circle">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1 class="confirm-title">Your booking is confirmed! 🎉</h1>
        <p class="confirm-subtitle">
            We have sent your confirmation voucher to <strong style="color:var(--ag-text-main);">{{ $booking->guest_email }}</strong>
        </p>

        <div class="booking-ref-badge">
            Booking ID: {{ $booking->booking_reference }}
        </div>

        <div class="confirmed-details-box">
            <div class="detail-row">
                <span class="detail-label">Lead Guest:</span>
                <span class="detail-val">{{ $booking->guest_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Property:</span>
                <span class="detail-val">{{ $booking->property?->name ?? 'Hotel' }}</span>
            </div>
            @if($booking->room)
            <div class="detail-row">
                <span class="detail-label">Room Type:</span>
                <span class="detail-val">1 x {{ $booking->room->name }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Check-in:</span>
                <span class="detail-val">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M j, Y') }} (from 02:00 PM)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Check-out:</span>
                <span class="detail-val">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M j, Y') }} (until 12:00 PM)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-val">{{ strtoupper($booking->payment_method ?? 'bKash') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-val" style="color:var(--ag-success-green); font-size:16px;">
                    {{ CurrencyService::format($booking->total_price ?? $booking->total_amount ?? 10508) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Cancellation:</span>
                <span class="detail-val" style="color:var(--ag-text-muted);">Non-refundable deal</span>
            </div>
        </div>

        <div class="confirm-actions">
            <a href="{{ route('booking.voucher', $booking->booking_reference) }}" target="_blank" class="btn-agoda-primary">
                <i class="fa-solid fa-download"></i> View &amp; Print Hotel Voucher
            </a>

            @php
                $voucherUrl = route('booking.voucher', $booking->booking_reference);
                $waShareText = urlencode("🎉 *Prime Booking Reservation Confirmed!*" . "\n\n" .
                    "🏨 *Hotel:* " . ($booking->property?->name ?? 'Hotel') . "\n" .
                    "🔖 *Booking Ref:* #" . $booking->booking_reference . "\n" .
                    "👤 *Guest:* " . $booking->guest_name . "\n" .
                    "📅 *Check-in:* " . \Carbon\Carbon::parse($booking->check_in)->format('d M Y') . "\n" .
                    "📄 *E-Voucher:* " . $voucherUrl);
            @endphp
            <a href="https://api.whatsapp.com/send?text={{ $waShareText }}" target="_blank" class="btn-agoda-secondary" style="color:#16a34a; border-color:#16a34a;">
                <i class="fa-brands fa-whatsapp fs-5"></i> Share on WhatsApp
            </a>

            <a href="{{ route('home') }}" class="btn-agoda-secondary">
                <i class="fa-solid fa-house"></i> Book Another Stay
            </a>
        </div>
    </div>
</main>

<footer class="agoda-checkout-footer">
    <div class="container">
        <p class="mb-1">All material herein © 2026 Prime Booking Bangladesh. All Rights Reserved.</p>
        <p class="mb-0 text-muted" style="font-size:11px;">
            Prime Booking is the leading online travel, hotel and resort booking platform across Bangladesh.
        </p>
    </div>
</footer>
@endsection
