@extends('layouts.main', ['activePage' => 'hotels'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'SSLCommerz Payment Gateway | Prime Booking')

@section('content')
<style>
.ssl-container { min-height: 85vh; background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 20px 16px; }
.ssl-box { width: 100%; max-width: 480px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 12px 32px rgba(0,0,0,0.08); overflow: hidden; }
.ssl-header { background: #006eb4; padding: 20px; color: #fff; display: flex; align-items: center; justify-content: space-between; }
.ssl-title { font-size: 20px; font-weight: 800; }
.ssl-body { padding: 24px; }
.card-tab { display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
.card-tab button { background: none; border: none; padding: 10px 16px; font-weight: 700; font-size: 13px; color: #64748b; border-bottom: 2px solid transparent; margin-bottom: -2px; cursor: pointer; }
.card-tab button.active { color: #006eb4; border-bottom-color: #006eb4; }
.ssl-btn { background: #006eb4; color: #fff; border: none; width: 100%; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 800; cursor: pointer; transition: background 0.2s; }
.ssl-btn:hover { background: #00568e; }
</style>

<div class="ssl-container">
    <div class="ssl-box">
        <div class="ssl-header">
            <div>
                <div class="ssl-title">SSLCOMMERZ</div>
                <div style="font-size: 11px; opacity: 0.85;">Secure EasyCheckout Gateway</div>
            </div>
            <div class="text-end">
                <div style="font-size: 11px; opacity: 0.85;">PAYABLE AMOUNT</div>
                <div style="font-size: 20px; font-weight: 800;">{{ CurrencyService::format($booking->amount) }}</div>
            </div>
        </div>

        <div class="ssl-body">
            <div class="card-tab">
                <button class="active"><i class="fa-solid fa-credit-card me-1"></i> Cards & Net Banking</button>
            </div>

            <form action="{{ route('payment.ssl.sandbox-execute', $booking->booking_reference) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size:11px;">Card / Method Type</label>
                    <select name="card_type" class="form-select border-2 fw-bold" style="border-color:#e2e8f0;">
                        <option value="visa">Visa Debit / Credit Card</option>
                        <option value="mastercard">MasterCard Credit Card</option>
                        <option value="amex">American Express Card</option>
                        <option value="dbbl">Dutch-Bangla Bank / Rocket</option>
                        <option value="citybank">City Bank City Touch</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size:11px;">Card Number</label>
                    <input type="text" name="card_number" class="form-control border-2 fw-bold" value="4000 1234 5678 9010" placeholder="4000 0000 0000 0000" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size:11px;">Expiry Date</label>
                        <input type="text" name="expiry" class="form-control border-2 fw-bold" value="12/28" placeholder="MM/YY" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size:11px;">CVV / CVC</label>
                        <input type="password" name="cvv" class="form-control border-2 fw-bold" value="123" placeholder="123" maxlength="4" required>
                    </div>
                </div>

                <div class="p-2.5 rounded-3 mb-3 text-center" style="background:#eff6ff; border:1px solid #bfdbfe; font-size:12px; color:#1d4ed8;">
                    <i class="fa-solid fa-shield-halved me-1"></i> 256-Bit SSL Encrypted Payment
                </div>

                <button type="submit" class="ssl-btn">
                    PAY {{ CurrencyService::format($booking->amount) }} NOW
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
