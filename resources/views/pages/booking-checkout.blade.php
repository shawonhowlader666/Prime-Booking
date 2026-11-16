@extends('layouts.main', ['activePage' => 'hotels'])

@php
    use Carbon\Carbon;
    $in  = Carbon::parse($checkIn);
    $out = Carbon::parse($checkOut);
    $money = fn ($v) => '৳ ' . number_format((float) $v);
    $nameParts = explode(' ', trim((string) old('guest_name', $user?->name ?? '')), 2);
    $refundable = (bool) ($room->free_cancellation ?? $property->free_cancellation ?? false);
    $policyText = trim((string) ($room->cancellation_policy ?: $property->cancellation_policy));
@endphp

@section('title', 'Secure Booking — ' . $property->name . ' | Prime Booking')
@section('extra_meta')<meta name="robots" content="noindex, nofollow">@endsection

@section('content')
<style>
    .co-page { background: #f5f7fa; padding: 24px 0 64px; }
    .co-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 16px; }
    .co-card h2 { font-size: 17px; font-weight: 800; margin: 0 0 14px; color: #0f172a; }
    .co-steps { display: flex; gap: 8px; align-items: center; font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 16px; flex-wrap: wrap; }
    .co-steps .dot { width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #e2e8f0; color: #475569; font-size: 12px; }
    .co-steps .on { color: #1d4ed8; } .co-steps .on .dot { background: #1d4ed8; color: #fff; }
    .co-hold { background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
    .co-pay label, .co-addon label { display: flex; gap: 12px; align-items: flex-start; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 8px; cursor: pointer; }
    .co-pay input:checked + div strong { color: #1d4ed8; }
    .co-pay label:has(input:checked), .co-addon label:has(input:checked) { border-color: #1d4ed8; background: #eff6ff; }
    .co-sum-row { display: flex; justify-content: space-between; gap: 12px; font-size: 14px; padding: 5px 0; color: #334155; }
    .co-sum-row.neg span:last-child { color: #15803d; font-weight: 700; }
    .co-total { border-top: 1px solid #e2e8f0; margin-top: 8px; padding-top: 12px; font-size: 18px; font-weight: 800; color: #0f172a; }
    .co-thumb { width: 96px; height: 96px; object-fit: cover; border-radius: 10px; flex-shrink: 0; background: #e2e8f0; }
    .co-btn { background: #1d4ed8; color: #fff; border: 0; width: 100%; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 800; }
    .co-btn:disabled { opacity: .65; }
    .co-msg { font-size: 13px; margin-top: 6px; }
    @media (min-width: 992px) { .co-sticky { position: sticky; top: 16px; } }
</style>

<div class="co-page">
<div class="container" style="max-width: 1080px;">

    <div class="co-steps" aria-label="Checkout progress">
        <span class="on"><span class="dot">1</span> Your details</span> <span>›</span>
        <span><span class="dot">2</span> Payment</span> <span>›</span>
        <span><span class="dot">3</span> Confirmation</span>
    </div>

    <div class="co-hold" id="coHold" role="status">
        <i class="fa-regular fa-clock me-1"></i>
        We are holding this room for you for <span id="coHoldTime" data-seconds="{{ (int) $holdSeconds }}">--:--</span>.
    </div>

    <div id="coError" class="alert alert-danger d-none" role="alert"></div>
    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <form id="coForm" method="POST" action="{{ route('booking.store', $property->id) }}" novalidate>
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">
        <input type="hidden" name="check_in" value="{{ $checkIn }}">
        <input type="hidden" name="check_out" value="{{ $checkOut }}">
        <input type="hidden" name="rooms" value="{{ $rooms }}">
        <input type="hidden" name="hold_token" value="{{ $holdToken }}">
        <input type="hidden" name="idempotency_token" value="{{ $idempotencyToken }}">
        <input type="hidden" name="guest_name" id="coGuestName">

        <div class="row g-3">
            <div class="col-lg-7">

                <div class="co-card">
                    <h2>Who is the lead guest?</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold" for="coFirst">First name</label>
                            <input class="form-control" id="coFirst" autocomplete="given-name" maxlength="50" required value="{{ $nameParts[0] ?? '' }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold" for="coLast">Last name</label>
                            <input class="form-control" id="coLast" autocomplete="family-name" maxlength="49" required value="{{ $nameParts[1] ?? '' }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold" for="coEmail">Email</label>
                            <input class="form-control" type="email" name="guest_email" id="coEmail" autocomplete="email" maxlength="150" required value="{{ old('guest_email', $user?->email) }}">
                            <div class="form-text">Your confirmation is sent here.</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold" for="coPhone">Mobile number</label>
                            <input class="form-control" type="tel" name="guest_phone" id="coPhone" autocomplete="tel" maxlength="20" required placeholder="01XXXXXXXXX" value="{{ old('guest_phone', $user?->phone) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold" for="coGuests">Guests</label>
                            <select class="form-select" name="guests" id="coGuests">
                                @for ($g = 1; $g <= ($maxGuests ?? 20); $g++)
                                    <option value="{{ $g }}" @selected($g === min($guests, $maxGuests ?? $guests))>{{ $g }} guest{{ $g > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="co-card">
                    <h2>Special requests <span class="text-muted fw-normal small">(optional)</span></h2>
                    <textarea class="form-control" name="special_requests" rows="3" maxlength="500" placeholder="High floor, late arrival, twin beds… Requests are subject to availability.">{{ old('special_requests') }}</textarea>
                </div>

                @if (!empty($addonCatalog))
                <div class="co-card co-addon">
                    <h2>Add to your stay</h2>
                    @foreach ($addonCatalog as $key => $addon)
                        <label>
                            <input class="form-check-input mt-1 co-recalc" type="checkbox" name="addons[]" value="{{ $key }}">
                            <div class="flex-grow-1 d-flex justify-content-between gap-2">
                                <span>{{ $addon['name'] }}</span>
                                <strong>{{ $money($addon['price']) }}</strong>
                            </div>
                        </label>
                    @endforeach
                </div>
                @endif

                <div class="co-card">
                    <h2>Promo code</h2>
                    <div class="input-group">
                        <input class="form-control text-uppercase" name="coupon_code" id="coCoupon" maxlength="50" placeholder="Enter code" value="{{ $couponCode }}" autocomplete="off">
                        <button class="btn btn-outline-primary fw-bold" type="button" id="coCouponApply">Apply</button>
                    </div>
                    <div class="co-msg" id="coCouponMsg">
                        @if ($quote['coupon']['valid'])<span class="text-success">{{ $quote['coupon']['message'] }}</span>@endif
                    </div>

                    @auth
                        @if ($quote['points']['balance'] >= $quote['points']['min'] && $quote['points']['point_value'] > 0)
                            <hr>
                            <label class="form-label small fw-bold" for="coPoints">
                                Use reward points
                                <span class="text-muted fw-normal">— you have {{ number_format($quote['points']['balance']) }} points (1 point = {{ $money($quote['points']['point_value']) }})</span>
                            </label>
                            <div class="input-group">
                                <input class="form-control" type="number" name="redeem_points" id="coPoints" min="0" max="{{ $quote['points']['balance'] }}" step="1" value="0">
                                <button class="btn btn-outline-primary fw-bold" type="button" id="coPointsApply">Apply</button>
                            </div>
                            <div class="co-msg text-muted" id="coPointsMsg">Minimum {{ number_format($quote['points']['min']) }} points per redemption.</div>
                        @endif
                    @endauth
                </div>

                <div class="co-card co-pay">
                    <h2>How would you like to pay?</h2>
                    @foreach ($paymentMethods as $code => $method)
                        <label>
                            <input class="form-check-input mt-1" type="radio" name="payment_method" value="{{ $code }}" @checked($loop->first)>
                            <div>
                                <strong>{{ $method['label'] }}</strong>
                                <div class="small text-muted">{{ $method['hint'] }}</div>
                            </div>
                        </label>
                    @endforeach
                    <div class="small text-muted mt-2">
                        <i class="fa-solid fa-lock me-1"></i>
                        Card and wallet details are entered on the payment provider's secure page. Prime Booking never sees or stores them.
                    </div>
                </div>

                <button class="co-btn" type="submit" id="coSubmit">
                    <span id="coSubmitText">Complete booking · {{ $money($quote['total']) }}</span>
                </button>
                <p class="small text-muted mt-2 mb-0">
                    By completing this booking you agree to the
                    <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms</a> and
                    <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a>.
                </p>
            </div>

            <div class="col-lg-5">
                <div class="co-sticky">
                    <div class="co-card">
                        <div class="d-flex gap-3">
                            <img class="co-thumb" src="{{ $room->image_url }}" alt="{{ $room->name }}" loading="lazy">
                            <div>
                                <div class="fw-bold" style="font-size: 16px;">{{ $property->name }}</div>
                                <div class="small text-muted">{{ collect([$property->address, $property->city])->filter()->unique()->implode(', ') }}</div>
                                @if ((int) $property->star_rating > 0)
                                    <div class="small text-warning" aria-label="{{ (int) $property->star_rating }} star hotel">{{ str_repeat('★', min(5, (int) $property->star_rating)) }}</div>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row small g-2">
                            <div class="col-6">
                                <div class="text-muted">Check-in</div>
                                <div class="fw-bold">{{ $in->format('D, d M Y') }}</div>
                                <div class="text-muted">from {{ $property->checkin_time ?: '14:00' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">Check-out</div>
                                <div class="fw-bold">{{ $out->format('D, d M Y') }}</div>
                                <div class="text-muted">until {{ $property->checkout_time ?: '12:00' }}</div>
                            </div>
                        </div>
                        <hr>
                        <div class="small">
                            <div class="fw-bold">{{ $rooms }} × {{ $room->name }}</div>
                            <div class="text-muted">
                                {{ $quote['nights'] }} night{{ $quote['nights'] > 1 ? 's' : '' }}
                                @if ($room->bed_type) · {{ $room->bed_type }} @endif
                                @if ($room->breakfast_included) · Breakfast included @endif
                            </div>
                            <div class="mt-2 {{ $refundable ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $refundable ? 'Free cancellation before check-in day' : 'Non-refundable rate' }}
                            </div>
                            @if ($policyText !== '')
                                <div class="text-muted mt-1">{{ \Illuminate\Support\Str::limit($policyText, 220) }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="co-card" aria-live="polite">
                        <h2>Price details</h2>
                        <div class="co-sum-row">
                            <span id="sumRoomLabel">{{ $rooms }} room{{ $rooms > 1 ? 's' : '' }} × {{ $quote['nights'] }} night{{ $quote['nights'] > 1 ? 's' : '' }}</span>
                            <span id="sumSubtotal">{{ $money($quote['subtotal']) }}</span>
                        </div>
                        <div class="co-sum-row neg {{ $quote['coupon']['discount'] > 0 ? '' : 'd-none' }}" id="rowCoupon">
                            <span>Promo <span id="sumCouponCode">{{ $quote['coupon']['code'] }}</span></span>
                            <span id="sumCoupon">− {{ $money($quote['coupon']['discount']) }}</span>
                        </div>
                        <div class="co-sum-row neg {{ $quote['vip']['discount'] > 0 ? '' : 'd-none' }}" id="rowVip">
                            <span>VIP <span id="sumVipTier">{{ $quote['vip']['tier'] }}</span> discount</span>
                            <span id="sumVip">− {{ $money($quote['vip']['discount']) }}</span>
                        </div>
                        <div class="co-sum-row neg d-none" id="rowPoints">
                            <span>Reward points</span>
                            <span id="sumPoints"></span>
                        </div>
                        <div class="co-sum-row d-none" id="rowAddons">
                            <span>Add-ons</span>
                            <span id="sumAddons"></span>
                        </div>
                        <div class="co-sum-row">
                            <span>Taxes &amp; fees (<span id="sumTaxRate">{{ rtrim(rtrim(number_format($quote['tax_rate'], 2), '0'), '.') }}</span>%)</span>
                            <span id="sumTax">{{ $money($quote['tax_amount']) }}</span>
                        </div>
                        <div class="co-sum-row co-total">
                            <span>Total</span>
                            <span id="sumTotal">{{ $money($quote['total']) }}</span>
                        </div>
                        @auth
                            <div class="small text-muted mt-2 {{ $quote['earnable_points'] > 0 ? '' : 'd-none' }}" id="rowEarn">
                                You will earn <strong id="sumEarn">{{ number_format($quote['earnable_points']) }}</strong> reward points on this stay.
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>

<script>
(function () {
    'use strict';

    var form      = document.getElementById('coForm');
    var submitBtn = document.getElementById('coSubmit');
    var submitTxt = document.getElementById('coSubmitText');
    var errorBox  = document.getElementById('coError');
    var csrf      = form.querySelector('input[name="_token"]').value;
    var quoteUrl  = @json(route('checkout.quote'));
    var hotelUrl  = @json(route('hotels.show', $property->id));
    var base      = {
        property_id: {{ (int) $property->id }},
        room_id: {{ (int) $room->id }},
        check_in: @json($checkIn),
        check_out: @json($checkOut),
        rooms: {{ (int) $rooms }}
    };

    function money(v) { return '৳ ' + Math.round(Number(v) || 0).toLocaleString('en-US'); }
    function byId(id) { return document.getElementById(id); }
    function toggle(id, show) { var el = byId(id); if (el) el.classList.toggle('d-none', !show); }
    function setText(id, text) { var el = byId(id); if (el) el.textContent = text; }

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
        errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function payload() {
        var addons = Array.prototype.map.call(form.querySelectorAll('input[name="addons[]"]:checked'), function (el) { return el.value; });
        var points = byId('coPoints');
        return Object.assign({}, base, {
            guests: parseInt(byId('coGuests').value, 10) || 1,
            coupon_code: byId('coCoupon').value.trim(),
            addons: addons,
            redeem_points: points ? (parseInt(points.value, 10) || 0) : 0
        });
    }

    function render(q) {
        setText('sumSubtotal', money(q.subtotal));
        toggle('rowCoupon', q.coupon.discount > 0);
        setText('sumCouponCode', q.coupon.code || '');
        setText('sumCoupon', '− ' + money(q.coupon.discount));
        toggle('rowVip', q.vip.discount > 0);
        setText('sumVipTier', q.vip.tier || '');
        setText('sumVip', '− ' + money(q.vip.discount));
        toggle('rowPoints', q.points.discount > 0);
        setText('sumPoints', '− ' + money(q.points.discount) + ' (' + q.points.redeemed + ' pts)');
        toggle('rowAddons', q.addons_total > 0);
        setText('sumAddons', money(q.addons_total));
        setText('sumTax', money(q.tax_amount));
        setText('sumTotal', money(q.total));
        toggle('rowEarn', q.earnable_points > 0);
        setText('sumEarn', Number(q.earnable_points || 0).toLocaleString('en-US'));
        submitTxt.textContent = 'Complete booking · ' + money(q.total);

        var couponMsg = byId('coCouponMsg');
        if (byId('coCoupon').value.trim() !== '' && q.coupon.message) {
            couponMsg.innerHTML = '';
            var span = document.createElement('span');
            span.className = q.coupon.valid ? 'text-success' : 'text-danger';
            span.textContent = q.coupon.message;
            couponMsg.appendChild(span);
        } else {
            couponMsg.textContent = '';
        }

        var pointsMsg = byId('coPointsMsg');
        if (pointsMsg && q.points.requested > 0) {
            pointsMsg.textContent = q.points.message
                || (q.points.redeemed < q.points.requested
                    ? 'Applied ' + q.points.redeemed + ' points (the most this booking can use).'
                    : 'Applied ' + q.points.redeemed + ' points.');
            if (q.points.redeemed !== q.points.requested) byId('coPoints').value = q.points.redeemed;
        }
    }

    var quoteSeq = 0;
    function refreshQuote() {
        var seq = ++quoteSeq;
        return fetch(quoteUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload())
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
          .then(function (res) {
              if (seq !== quoteSeq) return;
              if (res.ok && res.data.quote) render(res.data.quote);
          })
          .catch(function () { /* the server recalculates on submit regardless */ });
    }

    byId('coCouponApply').addEventListener('click', refreshQuote);
    byId('coCoupon').addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); refreshQuote(); } });
    byId('coGuests').addEventListener('change', refreshQuote);
    if (byId('coPointsApply')) byId('coPointsApply').addEventListener('click', refreshQuote);
    Array.prototype.forEach.call(form.querySelectorAll('.co-recalc'), function (el) { el.addEventListener('change', refreshQuote); });

    // Hold countdown: when it runs out the room goes back on sale, so send the guest back to re-check.
    (function () {
        var el = byId('coHoldTime');
        var left = parseInt(el.getAttribute('data-seconds'), 10) || 0;
        function tick() {
            if (left <= 0) {
                byId('coHold').innerHTML = 'Your hold on this room has expired. <a href="' + window.location.href + '">Refresh to check availability again</a>.';
                submitBtn.disabled = true;
                return;
            }
            var m = Math.floor(left / 60), s = left % 60;
            el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            left--;
            setTimeout(tick, 1000);
        }
        tick();
    })();

    var submitting = false;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (submitting) return;

        var first = byId('coFirst').value.trim(), last = byId('coLast').value.trim();
        var email = byId('coEmail').value.trim(), phone = byId('coPhone').value.trim();

        if (!first || !last) { showError('Please enter the lead guest\'s first and last name.'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('Please enter a valid email address.'); return; }
        if (!/^\+?[0-9\s\-()]{7,20}$/.test(phone)) { showError('Please enter a valid mobile number.'); return; }
        if (!form.querySelector('input[name="payment_method"]:checked')) { showError('Please choose a payment method.'); return; }

        byId('coGuestName').value = (first + ' ' + last).trim();
        errorBox.classList.add('d-none');

        submitting = true;
        submitBtn.disabled = true;
        var originalText = submitTxt.textContent;
        submitTxt.textContent = 'Processing your booking…';

        var body = payload();
        body.guest_name = byId('coGuestName').value;
        body.guest_email = email;
        body.guest_phone = phone;
        body.special_requests = form.querySelector('textarea[name="special_requests"]').value.trim();
        body.payment_method = form.querySelector('input[name="payment_method"]:checked').value;
        body.hold_token = form.querySelector('input[name="hold_token"]').value;
        body.idempotency_token = form.querySelector('input[name="idempotency_token"]').value;

        fetch(form.action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        }).then(function (r) { return r.json().catch(function () { return {}; }).then(function (d) { return { status: r.status, data: d }; }); })
          .then(function (res) {
              if (res.data && res.data.success && res.data.redirect_url) {
                  submitTxt.textContent = 'Redirecting…';
                  window.location.assign(res.data.redirect_url);
                  return;
              }

              var message = (res.data && res.data.message) || 'Your booking could not be completed. Please try again.';
              if (res.data && res.data.errors) {
                  var firstKey = Object.keys(res.data.errors)[0];
                  if (firstKey) message = res.data.errors[firstKey][0];
              }
              if (res.status === 419) message = 'Your session expired. Please refresh the page and try again.';
              if (res.status === 429) message = 'Too many attempts. Please wait a minute and try again.';

              showError(message);
              submitting = false;
              submitBtn.disabled = false;
              submitTxt.textContent = originalText;
              refreshQuote();
          })
          .catch(function () {
              showError('Network error while completing your booking. Please check your connection and try again — you will not be charged twice.');
              submitting = false;
              submitBtn.disabled = false;
              submitTxt.textContent = originalText;
          });
    });
})();
</script>
@endsection
