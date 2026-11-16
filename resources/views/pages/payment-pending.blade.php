@extends('layouts.main', ['activePage' => 'bookings'])

@php
    use App\Services\CurrencyService;
    $amount = (float) ($booking->total_price ?? $booking->total_amount ?? 0);
    $messages = [
        'failed'        => ['danger',  'Payment was not completed', 'Your payment did not go through. No money was taken for this attempt. You can try again below.'],
        'cancelled'     => ['warning', 'Payment cancelled', 'You cancelled the payment. Your room is still held for a short time — you can pay again below.'],
        'gateway_error' => ['danger',  'Payment gateway unavailable', 'We could not reach the payment provider. Please try again in a moment or choose another method.'],
        'expired'       => ['secondary', 'Reservation expired', 'The payment window for this reservation has closed and the room was released. Please make a new booking.'],
        'review'        => ['warning', 'Payment received — under review', 'We received your payment after the reservation had been released and the room is no longer available. Our team will contact you and refund the full amount.'],
    ];
    [$tone, $title, $text] = $messages[$state] ?? ['info', 'Complete your payment', 'Your room is reserved. Complete the payment to confirm your booking.'];
    if (!$canPay && !in_array($state, ['review', 'expired'], true)) {
        [$tone, $title, $text] = $messages['expired'];
    }
@endphp

@section('title', 'Complete your payment — ' . $booking->booking_reference . ' | Prime Booking')
@section('extra_meta')<meta name="robots" content="noindex, nofollow">@endsection

@section('content')
<div class="container py-5" style="max-width: 560px;">
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="alert alert-{{ $tone }} mb-4" role="alert">
                <div class="fw-bold mb-1">{{ $title }}</div>
                <div class="small">{{ $text }}</div>
            </div>

            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Booking reference</div>
                    <div class="fw-bold fs-5">{{ $booking->booking_reference }}</div>
                </div>
                <div class="text-end">
                    <div class="text-muted small text-uppercase fw-bold">Amount due</div>
                    <div class="fw-bold fs-5">{{ CurrencyService::format($amount) }}</div>
                </div>
            </div>

            <div class="border rounded-3 p-3 mb-4 small">
                <div class="fw-bold">{{ $booking->property?->name ?? 'Hotel stay' }}</div>
                <div class="text-muted">{{ $booking->room?->name }}</div>
                <div class="text-muted">
                    {{ optional($booking->check_in)->format('D, d M Y') }} → {{ optional($booking->check_out)->format('D, d M Y') }}
                    · {{ max(1, (int) $booking->nights) }} night(s) · {{ max(1, (int) $booking->rooms_count) }} room(s)
                </div>
            </div>

            @if ($canPay)
                @if ($secondsRemaining !== null)
                    <div class="text-center small text-muted mb-3">
                        Room held for <span id="payCountdown" class="fw-bold text-dark" data-seconds="{{ $secondsRemaining }}">--:--</span>
                    </div>
                @endif

                <a href="{{ route('payment.ssl.sandbox-redirect', $booking->booking_reference) }}" class="btn btn-primary w-100 fw-bold py-2 mb-2">
                    <i class="fa-solid fa-credit-card me-1"></i> Pay with Card / Net Banking / Mobile Wallet
                </a>
                @if ($bkashAvailable)
                    <a href="{{ route('payment.bkash.sandbox-redirect', $booking->booking_reference) }}" class="btn btn-outline-danger w-100 fw-bold py-2">
                        Pay with bKash
                    </a>
                @endif
            @else
                <a href="{{ $booking->property_id ? route('hotels.show', $booking->property_id) : route('home') }}" class="btn btn-primary w-100 fw-bold py-2">
                    Back to hotel
                </a>
            @endif

            <div class="text-center mt-3">
                <a href="{{ route('contact') }}" class="small text-muted">Need help? Contact support</a>
            </div>
        </div>
    </div>
</div>

@if ($canPay && $secondsRemaining !== null)
<script>
(function () {
    var el = document.getElementById('payCountdown');
    if (!el) return;
    var left = parseInt(el.getAttribute('data-seconds'), 10) || 0;
    function tick() {
        if (left <= 0) { window.location.reload(); return; }
        var m = Math.floor(left / 60), s = left % 60;
        el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        left--;
        setTimeout(tick, 1000);
    }
    tick();
})();
</script>
@endif
@endsection
