@extends('layouts.main', ['activePage' => 'hotels'])

@php
    use App\Services\CurrencyService;
    $user = auth()->user();
@endphp

@section('title', 'Secure Booking — ' . $property->name . ' | Prime Booking')
@section('meta_description', 'Complete your booking for ' . $property->name . '. Secure checkout with bKash, Nagad, and Card payment.')

@section('content')
<style>
/* ── Agoda-style Checkout Page ── */
.checkout-page { background: #f1f5f9; min-height: 100vh; padding-bottom: 60px; }
.step-bar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 14px 0; }
.step-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; }
.step-num { width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
.step-num.active { background: #2067e1; color: #fff; }
.step-num.done   { background: #16a34a; color: #fff; }
.step-num.future { background: #e2e8f0; color: #94a3b8; }
.step-label.active { color: #2067e1; }
.step-label.future { color: #94a3b8; }

.section-card { background: #fff; border-radius: 14px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.section-title { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
.form-label-pro { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 5px; }
.form-control-pro { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 14px; transition: border-color 0.2s; }
.form-control-pro:focus { border-color: #2067e1; box-shadow: 0 0 0 3px rgba(32,103,225,0.1); outline: none; }

/* Payment method selector */
.pay-option { border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; }
.pay-option:hover { border-color: #2067e1; background: #f0f6ff; }
.pay-option.selected { border-color: #2067e1; background: #f0f6ff; }
.pay-option input[type=radio] { display: none; }
.pay-logo { width: 44px; height: 28px; object-fit: contain; border-radius: 4px; }

/* Order summary box */
.summary-box { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); position: sticky; top: 80px; }
.summary-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 5px 0; color: #475569; }
.summary-row.total { font-size: 15px; font-weight: 700; color: #0f172a; border-top: 1px solid #e2e8f0; margin-top: 8px; padding-top: 12px; }
.place-order-btn { background: #2067e1; color: #fff; border: none; border-radius: 10px; padding: 16px; width: 100%; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s, transform 0.1s; letter-spacing: 0.3px; }
.place-order-btn:hover { background: #1a52c0; transform: translateY(-1px); }
.place-order-btn:disabled { background: #94a3b8; cursor: not-allowed; transform: none; }

/* Addon checkbox */
.addon-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; cursor: pointer; transition: all 0.2s; }
.addon-card:hover, .addon-card.checked { border-color: #2067e1; background: #f0f6ff; }

/* Trust badges */
.trust-badge { display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #475569; }

@media (max-width: 991px) {
    .summary-box { position: static; }
}
</style>

{{-- Step Progress Bar --}}
<div class="step-bar">
    <div style="max-width:1100px; margin:0 auto; padding:0 16px; display:flex; align-items:center; justify-content:center; gap:12px;">
        <div class="step-item">
            <div class="step-num done"><i class="fa-solid fa-check" style="font-size:10px;"></i></div>
            <span class="step-label active">Choose Room</span>
        </div>
        <i class="fa-solid fa-chevron-right" style="font-size:9px; color:#94a3b8;"></i>
        <div class="step-item">
            <div class="step-num active">2</div>
            <span class="step-label active">Your Details</span>
        </div>
        <i class="fa-solid fa-chevron-right" style="font-size:9px; color:#94a3b8;"></i>
        <div class="step-item">
            <div class="step-num future">3</div>
            <span class="step-label future">Payment</span>
        </div>
        <i class="fa-solid fa-chevron-right" style="font-size:9px; color:#94a3b8; display:none;" id="step4chevron"></i>
        <div class="step-item">
            <div class="step-num future">4</div>
            <span class="step-label future">Confirmation</span>
        </div>
    </div>
</div>

<div class="checkout-page">
<div style="max-width:1100px; margin:0 auto; padding:24px 16px;">

    {{-- Urgency strip --}}
    <div class="d-flex align-items-center gap-3 bg-red-50 rounded-3 p-3 mb-4" style="background:#fff5f5; border:1px solid #fecaca; border-radius:10px;">
        <i class="fa-solid fa-fire-flame-curved text-danger fs-5"></i>
        <div>
            <strong class="text-danger" style="font-size:13px;">High demand in {{ $property->city ?: 'Bangladesh' }}!</strong>
            <span class="text-secondary ms-2" style="font-size:12px;">{{ rand(8,24) }} people are viewing this property right now.</span>
        </div>
        <div class="ms-auto text-danger fw-bold" id="urgencyTimer" style="font-size:13px; font-family:monospace;">14:59</div>
    </div>

    <form action="{{ route('booking.store', $property->id) }}" method="POST" id="bookingForm">
    @csrf
    {{-- Hidden fields --}}
    <input type="hidden" name="room_id"   value="{{ $selectedRoom?->id }}">
    <input type="hidden" name="check_in"  value="{{ $checkIn }}">
    <input type="hidden" name="check_out" value="{{ $checkOut }}">
    <input type="hidden" name="guests"    value="{{ $guests }}">

    <div class="row g-4">

        {{-- ── LEFT COLUMN ── --}}
        <div class="col-lg-8">

            {{-- Property mini-card --}}
            <div class="section-card d-flex gap-3 align-items-center">
                <img src="{{ $property->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80' }}"
                     alt="{{ $property->name }}"
                     style="width:90px; height:68px; object-fit:cover; border-radius:10px; flex-shrink:0;">
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark mb-0" style="font-size:15px;">{{ $property->name }}</div>
                    <div class="text-muted" style="font-size:12px;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $property->city }}</div>
                    @if($selectedRoom)
                    <div class="mt-1"><span class="badge" style="background:#f0f6ff; color:#2067e1; font-size:11px; border:1px solid #bfdbfe;"><i class="fa-solid fa-bed me-1"></i>{{ $selectedRoom->name }}</span></div>
                    @endif
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="text-muted" style="font-size:11px;">{{ $nights }} nights</div>
                    <div class="fw-bold text-primary" style="font-size:17px;">{{ CurrencyService::format($pricePerNight) }}</div>
                    <div class="text-muted" style="font-size:10px;">per night</div>
                </div>
            </div>

            {{-- ── Guest Details ── --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:28px;height:28px;background:#2067e1;font-size:12px;font-weight:700;">1</span>
                    Guest Details
                </div>

                @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px;">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label-pro">Full Name *</label>
                        <input type="text" name="guest_name" class="form-control form-control-pro"
                               value="{{ old('guest_name', $user?->name) }}"
                               placeholder="e.g. Shawon Rahman" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label-pro">Email Address *</label>
                        <input type="email" name="guest_email" class="form-control form-control-pro"
                               value="{{ old('guest_email', $user?->email) }}"
                               placeholder="your@email.com" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label-pro">Phone Number *</label>
                        <input type="tel" name="guest_phone" class="form-control form-control-pro"
                               value="{{ old('guest_phone', $user?->phone ?? '') }}"
                               placeholder="+880 1XXX-XXXXXX" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label-pro">Nationality</label>
                        <select name="nationality" class="form-select form-control-pro">
                            <option value="BD" selected>🇧🇩 Bangladesh</option>
                            <option value="US">🇺🇸 United States</option>
                            <option value="GB">🇬🇧 United Kingdom</option>
                            <option value="IN">🇮🇳 India</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-pro">Special Requests <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="special_requests" class="form-control form-control-pro" rows="2"
                                  placeholder="e.g. High floor room, early check-in, anniversary decoration...">{{ old('special_requests') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── Dates & Guests Summary ── --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:28px;height:28px;background:#2067e1;font-size:12px;font-weight:700;">2</span>
                    Stay Details
                </div>
                <div class="row g-3" style="font-size:13px;">
                    <div class="col-sm-4">
                        <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="text-muted mb-1" style="font-size:11px; font-weight:600; text-transform:uppercase;">Check-in</div>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($checkIn)->format('D, d M Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">From 2:00 PM</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="text-muted mb-1" style="font-size:11px; font-weight:600; text-transform:uppercase;">Check-out</div>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($checkOut)->format('D, d M Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">Until 12:00 noon</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="text-muted mb-1" style="font-size:11px; font-weight:600; text-transform:uppercase;">Guests & Nights</div>
                            <div class="fw-bold text-dark">{{ $guests }} Guest{{ $guests > 1 ? 's' : '' }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $nights }} night{{ $nights > 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Add-ons ── --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:28px;height:28px;background:#2067e1;font-size:12px;font-weight:700;">3</span>
                    Enhance Your Stay
                    <span class="badge bg-danger ms-auto" style="font-size:10px;">Popular</span>
                </div>
                <div class="row g-3">
                    @foreach($addons as $key => $addon)
                    <div class="col-sm-6 col-lg-12 col-xl-6">
                        <label class="addon-card d-flex align-items-center gap-3 m-0" id="addon_label_{{ $key }}">
                            <input type="checkbox" name="addons[]" value="{{ $key }}"
                                   id="addon_{{ $key }}"
                                   class="addon-check"
                                   data-price="{{ $addon['price'] }}"
                                   style="width:18px;height:18px;accent-color:#2067e1;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark" style="font-size:13px;">{{ $addon['name'] }}</div>
                                <div class="text-primary fw-bold" style="font-size:14px;">+{{ CurrencyService::format($addon['price']) }}</div>
                            </div>
                            @php
                            $addonIcons = ['airport_transfer' => 'fa-van-shuttle text-purple-600', 'daily_breakfast' => 'fa-utensils text-success', 'spa_package' => 'fa-spa text-pink-500'];
                            @endphp
                            <i class="fa-solid {{ $addonIcons[$key] ?? 'fa-plus' }}" style="font-size:20px; color:#6366f1; flex-shrink:0;"></i>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Payment Method ── --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:28px;height:28px;background:#2067e1;font-size:12px;font-weight:700;">4</span>
                    Payment Method
                    <i class="fa-solid fa-shield-halved text-success ms-auto" style="font-size:18px;"></i>
                </div>

                <div class="d-flex flex-column gap-3" id="paymentOptions">
                    {{-- bKash --}}
                    <label class="pay-option" id="pay_label_bkash">
                        <input type="radio" name="payment_method" value="bkash" id="pay_bkash">
                        <div style="width:44px;height:28px;background:#e2136e;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="color:#fff;font-size:10px;font-weight:800;">bKash</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:13px;">bKash Mobile Banking</div>
                            <div class="text-muted" style="font-size:11px;">Pay instantly with your bKash account</div>
                        </div>
                        <span class="badge bg-success ms-auto" style="font-size:10px;">Instant</span>
                    </label>

                    {{-- Nagad --}}
                    <label class="pay-option" id="pay_label_nagad">
                        <input type="radio" name="payment_method" value="nagad" id="pay_nagad">
                        <div style="width:44px;height:28px;background:#f7941d;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="color:#fff;font-size:10px;font-weight:800;">Nagad</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:13px;">Nagad Digital Financial Service</div>
                            <div class="text-muted" style="font-size:11px;">Fast payment with Nagad</div>
                        </div>
                    </label>

                    {{-- Card --}}
                    <label class="pay-option" id="pay_label_card">
                        <input type="radio" name="payment_method" value="card" id="pay_card">
                        <div style="width:44px;height:28px;background:#1a1f36;border-radius:6px;display:flex;align-items:center;justify-content:center;gap:2px;flex-shrink:0;">
                            <i class="fa-brands fa-cc-visa" style="color:#fff;font-size:14px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:13px;">Debit / Credit Card</div>
                            <div class="text-muted" style="font-size:11px;">Visa, Mastercard, AmEx accepted</div>
                        </div>
                    </label>

                    {{-- SSLCommerz --}}
                    <label class="pay-option" id="pay_label_ssl">
                        <input type="radio" name="payment_method" value="sslcommerz" id="pay_ssl">
                        <div style="width:44px;height:28px;background:#006eb4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="color:#fff;font-size:9px;font-weight:800;">SSL</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:13px;">SSLCommerz — All Banks</div>
                            <div class="text-muted" style="font-size:11px;">Internet banking, DBBL, Dutch-Bangla</div>
                        </div>
                    </label>

                    {{-- Cash / Pay at Hotel --}}
                    <label class="pay-option" id="pay_label_cash">
                        <input type="radio" name="payment_method" value="cash" id="pay_cash">
                        <div style="width:44px;height:28px;background:#16a34a;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid fa-money-bill" style="color:#fff;font-size:14px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:13px;">Pay at Hotel</div>
                            <div class="text-muted" style="font-size:11px;">Pay cash upon check-in</div>
                        </div>
                        <span class="badge bg-warning text-dark ms-auto" style="font-size:10px;">No Card Needed</span>
                    </label>
                </div>
            </div>

            {{-- ── Terms ── --}}
            <div class="section-card py-3">
                <label class="d-flex align-items-start gap-3" style="cursor:pointer;">
                    <input type="checkbox" id="termsCheck" required style="width:18px;height:18px;margin-top:2px;accent-color:#2067e1;">
                    <span class="text-secondary" style="font-size:12.5px; line-height:1.6;">
                        I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-primary">Terms & Conditions</a>
                        and <a href="{{ route('privacy') }}" target="_blank" class="text-primary">Privacy Policy</a>.
                        I understand the cancellation and refund policies of this property.
                    </span>
                </label>
            </div>

        </div>

        {{-- ── RIGHT: Order Summary ── --}}
        <div class="col-lg-4">
            <div class="summary-box">
                <div class="fw-bold text-dark mb-3" style="font-size:15px;">Price Summary</div>

                <div class="summary-row">
                    <span>{{ CurrencyService::format($pricePerNight) }} × {{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span>
                    <span>{{ CurrencyService::format($subtotal) }}</span>
                </div>
                <div class="summary-row" id="addons_row" style="display:none;">
                    <span>Add-ons</span>
                    <span id="addons_total">৳ 0</span>
                </div>
                <div class="summary-row text-success" id="discount_row" style="display:none;">
                    <span><i class="fa-solid fa-tag me-1"></i> Promo (<span id="coupon_badge_text"></span>)</span>
                    <span id="discount_amount_text">- ৳ 0</span>
                </div>
                <div class="summary-row">
                    <span>Taxes & Fees (7.5%)</span>
                    <span id="tax_display">{{ CurrencyService::format($taxAmount) }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="grand_total" class="text-primary">{{ CurrencyService::format($totalPrice) }}</span>
                </div>

                {{-- ── Promo Code Box ── --}}
                <div class="mt-3 p-2.5 rounded-3" style="background:#f8fafc; border:1px dashed #cbd5e1;">
                    <label class="form-label mb-1 fw-bold text-dark" style="font-size:11.5px; text-transform:uppercase;">
                        <i class="fa-solid fa-gift text-primary me-1"></i> Have a Promo Code?
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="coupon_input" name="coupon_code" class="form-control" placeholder="e.g. PRIME10, EID2026" style="text-transform:uppercase; font-size:12px; font-weight:700;">
                        <button type="button" id="apply_coupon_btn" class="btn btn-primary fw-bold px-3" style="font-size:12px;">Apply</button>
                    </div>
                    <div id="coupon_feedback" class="mt-1" style="font-size:11.5px; display:none;"></div>
                </div>

                <div class="mt-3 p-2 rounded-3 text-success d-flex align-items-center gap-2" style="background:#f0fdf4; border:1px solid #bbf7d0; font-size:12px;">
                    <i class="fa-solid fa-tag"></i>
                    <span>You save <strong>{{ CurrencyService::format(($property->original_price ?? $pricePerNight * 1.15) * $nights - $subtotal) }}</strong> vs rack rate!</span>
                </div>

                <button type="submit" class="place-order-btn mt-3" id="placeOrderBtn">
                    <i class="fa-solid fa-lock me-2"></i>CONFIRM BOOKING
                </button>

                <div class="mt-3 d-flex flex-column gap-2">
                    <div class="trust-badge"><i class="fa-solid fa-shield-halved text-success"></i>256-bit SSL Secure Payment</div>
                    <div class="trust-badge"><i class="fa-solid fa-rotate-left text-primary"></i>Free cancellation on eligible rates</div>
                    <div class="trust-badge"><i class="fa-solid fa-headset text-warning"></i>24/7 Customer Support</div>
                </div>

                <div class="mt-3 text-center">
                    <div class="text-muted mb-1" style="font-size:11px; font-weight:600; text-transform:uppercase;">Accepted Payments</div>
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <div style="background:#e2136e;color:#fff;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:800;">bKash</div>
                        <div style="background:#f7941d;color:#fff;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:800;">Nagad</div>
                        <i class="fa-brands fa-cc-visa" style="font-size:20px;color:#1a1f36;"></i>
                        <i class="fa-brands fa-cc-mastercard" style="font-size:20px;color:#eb001b;"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </form>
</div>
</div>

<script>
(function () {
    // ── Payment option visual selection ──────────────────────────────────
    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.pay-option').forEach(el => el.classList.remove('selected'));
            this.closest('.pay-option').classList.add('selected');
        });
    });

    // ── Addon & Discount price calculator ────────────────────────────────
    const subtotal          = {{ $subtotal }};
    const taxRate           = 0.075;
    const propertyId        = {{ $property->id }};
    let appliedDiscount     = 0;
    let appliedCouponCode   = '';

    const addonsRow         = document.getElementById('addons_row');
    const addonsTotal       = document.getElementById('addons_total');
    const discountRow       = document.getElementById('discount_row');
    const couponBadgeText   = document.getElementById('coupon_badge_text');
    const discountAmountText= document.getElementById('discount_amount_text');
    const taxDisplay        = document.getElementById('tax_display');
    const grandTotal        = document.getElementById('grand_total');
    const couponInput       = document.getElementById('coupon_input');
    const applyCouponBtn    = document.getElementById('apply_coupon_btn');
    const couponFeedback    = document.getElementById('coupon_feedback');

    function formatBDT(n) {
        return '৳ ' + Math.round(n).toLocaleString('en-BD');
    }

    function recalculate() {
        let addons = 0;
        document.querySelectorAll('.addon-check:checked').forEach(function(cb) {
            addons += parseFloat(cb.dataset.price) || 0;
        });

        const netBase = Math.max(0, subtotal - appliedDiscount + addons);
        const tax     = Math.round(netBase * taxRate);
        const total   = netBase + tax;

        if (addons > 0) {
            addonsRow.style.display = 'flex';
            addonsTotal.textContent = formatBDT(addons);
        } else {
            addonsRow.style.display = 'none';
        }

        if (appliedDiscount > 0) {
            discountRow.style.display = 'flex';
            couponBadgeText.textContent = appliedCouponCode;
            discountAmountText.textContent = '- ' + formatBDT(appliedDiscount);
        } else {
            discountRow.style.display = 'none';
        }

        taxDisplay.textContent  = formatBDT(tax);
        grandTotal.textContent  = formatBDT(total);
    }

    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const code = couponInput.value.trim();
            if (!code) {
                couponFeedback.style.display = 'block';
                couponFeedback.style.color = '#dc2626';
                couponFeedback.textContent = 'Please enter a coupon code.';
                return;
            }

            applyCouponBtn.disabled = true;
            applyCouponBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            fetch('{{ route("coupon.validate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    subtotal: subtotal,
                    property_id: propertyId
                })
            })
            .then(res => res.json())
            .then(data => {
                applyCouponBtn.disabled = false;
                applyCouponBtn.textContent = 'Apply';
                couponFeedback.style.display = 'block';

                if (data.valid) {
                    appliedDiscount = parseFloat(data.discount) || 0;
                    appliedCouponCode = data.code;
                    couponFeedback.style.color = '#16a34a';
                    couponFeedback.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> ' + data.message;
                    couponInput.readOnly = true;
                    applyCouponBtn.style.display = 'none';
                } else {
                    appliedDiscount = 0;
                    appliedCouponCode = '';
                    couponFeedback.style.color = '#dc2626';
                    couponFeedback.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i> ' + (data.message || 'Invalid coupon.');
                }
                recalculate();
            })
            .catch(err => {
                applyCouponBtn.disabled = false;
                applyCouponBtn.textContent = 'Apply';
                couponFeedback.style.display = 'block';
                couponFeedback.style.color = '#dc2626';
                couponFeedback.textContent = 'Could not validate coupon. Please try again.';
            });
        });
    }

    document.querySelectorAll('.addon-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            this.closest('.addon-card').classList.toggle('checked', this.checked);
            recalculate();
        });
    });

    // ── Urgency countdown timer ──────────────────────────────────────────
    let seconds = 14 * 60 + 59;
    const timerEl = document.getElementById('urgencyTimer');
    setInterval(function() {
        if (seconds <= 0) { seconds = 14 * 60 + 59; return; }
        seconds--;
        const m = String(Math.floor(seconds / 60)).padStart(2,'0');
        const s = String(seconds % 60).padStart(2,'0');
        if (timerEl) timerEl.textContent = m + ':' + s;
    }, 1000);

    // ── Form submit guard ────────────────────────────────────────────────
    const form = document.getElementById('bookingForm');
    const btn  = document.getElementById('placeOrderBtn');
    const terms= document.getElementById('termsCheck');

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!document.querySelector('input[name="payment_method"]:checked')) {
                e.preventDefault();
                alert('Please select a payment method to continue.');
                return;
            }
            if (!terms.checked) {
                e.preventDefault();
                alert('Please agree to the Terms & Conditions.');
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Processing...';
        });
    }
})();
</script>
@endsection
