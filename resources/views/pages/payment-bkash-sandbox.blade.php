@extends('layouts.main', ['activePage' => 'hotels'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'bKash Payment Gateway | Prime Booking')

@section('content')
<style>
.bkash-modal-container { min-height: 85vh; background: #eadede; display: flex; align-items: center; justify-content: center; padding: 20px 16px; }
.bkash-box { width: 100%; max-width: 420px; background: #e2136e; border-radius: 16px; box-shadow: 0 12px 32px rgba(226, 19, 110, 0.3); overflow: hidden; color: #fff; }
.bkash-header { padding: 24px 24px 16px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); }
.bkash-logo-text { font-size: 28px; font-weight: 900; letter-spacing: -1px; }
.bkash-body { padding: 24px; background: #fff; color: #1e293b; border-radius: 20px 20px 0 0; margin-top: 10px; }
.bkash-merchant-info { text-align: center; margin-bottom: 20px; }
.bkash-amount { font-size: 32px; font-weight: 900; color: #e2136e; }
.bkash-input { border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 16px; font-weight: 700; width: 100%; text-align: center; letter-spacing: 2px; }
.bkash-input:focus { border-color: #e2136e; outline: none; }
.bkash-pay-btn { background: #e2136e; color: #fff; border: none; width: 100%; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 800; cursor: pointer; transition: background 0.2s; }
.bkash-pay-btn:hover { background: #c20d5c; }
</style>

<div class="bkash-modal-container">
    <div class="bkash-box">
        <div class="bkash-header">
            <div class="bkash-logo-text">bKash</div>
            <div style="font-size: 12px; opacity: 0.9;">Direct Payment Checkout Gateway</div>
        </div>

        <div class="bkash-body">
            <div class="bkash-merchant-info">
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">MERCHANT: PRIME BOOKING</div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-top: 2px;">Ref: {{ $booking->booking_reference }}</div>
                <div class="bkash-amount mt-2">{{ CurrencyService::format($booking->amount) }}</div>
            </div>

            <form action="{{ route('payment.bkash.sandbox-execute', $booking->booking_reference) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size:11px;">Your bKash Account Number</label>
                    <input type="tel" name="bkash_number" class="bkash-input" value="{{ $booking->guest_phone ?: '01700000000' }}" placeholder="017XXXXXXXX" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size:11px;">bKash PIN (Sandbox Auto-Approved)</label>
                    <input type="password" name="bkash_pin" class="bkash-input" value="12345" placeholder="•••••" maxlength="5" required>
                </div>

                <div class="p-2.5 rounded-3 mb-3 text-center" style="background:#fdf2f8; border:1px solid #fbcfe8; font-size:12px; color:#be185d;">
                    <i class="fa-solid fa-lock me-1"></i> Sandbox Test Mode — Any PIN will confirm booking!
                </div>

                <button type="submit" class="bkash-pay-btn">
                    CONFIRM bKASH PAYMENT
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('booking.form', $booking->property_id) }}" class="text-muted small text-decoration-none">
                    Cancel & Return to Booking
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
