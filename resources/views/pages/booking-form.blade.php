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
                <div class="section-title d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:28px;height:28px;background:#2067e1;font-size:12px;font-weight:700;">4</span>
                        <span>Select Payment Method</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-success" style="font-size:12px; font-weight:600;">
                        <i class="fa-solid fa-shield-halved fs-6"></i> 256-Bit SSL Encrypted
                    </div>
                </div>

                <div class="d-flex flex-column gap-3" id="paymentOptions">
                    
                    {{-- 1. bKash Mobile Banking --}}
                    <label class="pay-option selected position-relative d-flex align-items-center gap-3" id="pay_label_bkash" style="border: 2px solid #2067e1; background: #f8faff; border-radius: 12px; padding: 14px 18px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="payment_method" value="bkash" id="pay_bkash" checked style="width:18px; height:18px; accent-color:#e2136e; flex-shrink:0;">
                        {{-- Authentic Official bKash Vector Logo Badge --}}
                        <div style="width:72px; height:40px; background:#e2136e; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 3px 8px rgba(226,19,110,0.28); padding:4px;">
                            <svg viewBox="0 0 110 60" width="58" height="32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M38.5 12.5L24.8 26.2L30.2 2L38.5 12.5Z" fill="#ffffff"/>
                                <path d="M9.5 12.5L23.2 26.2L17.8 2L9.5 12.5Z" fill="#ffffff"/>
                                <path d="M24 28L24 55L11 41.5L24 28Z" fill="#ffffff"/>
                                <path d="M25.6 28L38.6 41.5L25.6 55L25.6 28Z" fill="#ffffff"/>
                                <text x="46" y="38" fill="#ffffff" font-size="22" font-weight="900" font-family="'Plus Jakarta Sans', system-ui, -apple-system, sans-serif" letter-spacing="-0.5">bKash</text>
                            </svg>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="text-dark" style="font-size:14px; font-weight:700;">bKash Mobile Banking</strong>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size:10px; font-weight:700;">Most Popular in BD</span>
                            </div>
                            <div class="text-secondary" style="font-size:11.5px; margin-top:2px;">Instant checkout via bKash App or USSD dial · 0% Gateway Charge</div>
                        </div>
                        <span class="badge bg-success text-white fw-bold px-2.5 py-1.5 ms-auto flex-shrink-0" style="font-size:10.5px; border-radius:6px; box-shadow: 0 2px 6px rgba(22,163,74,0.2);">
                            <i class="fa-solid fa-bolt me-1"></i> Instant
                        </span>
                    </label>

                    {{-- 2. Nagad Digital Financial Service --}}
                    <label class="pay-option position-relative d-flex align-items-center gap-3" id="pay_label_nagad" style="border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; cursor: pointer; transition: all 0.2s; background:#ffffff;">
                        <input type="radio" name="payment_method" value="nagad" id="pay_nagad" style="width:18px; height:18px; accent-color:#f7941d; flex-shrink:0;">
                        {{-- Authentic Official Nagad Vector Logo Badge --}}
                        <div style="width:72px; height:40px; background:linear-gradient(135deg, #f7941d 0%, #ed1c24 100%); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 3px 8px rgba(247,148,29,0.28); padding:4px;">
                            <svg viewBox="0 0 120 60" width="58" height="32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="20" cy="30" r="14" fill="#ffffff" fill-opacity="0.95"/>
                                <path d="M14 24C17 21 23 21 26 24C29 27 29 33 26 36C23 39 17 39 14 36" stroke="#ed1c24" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="20" cy="30" r="4.5" fill="#f7941d"/>
                                <text x="40" y="38" fill="#ffffff" font-size="20" font-weight="900" font-family="'Plus Jakarta Sans', system-ui, -apple-system, sans-serif" letter-spacing="-0.5">Nagad</text>
                            </svg>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="text-dark" style="font-size:14px; font-weight:700;">Nagad Digital Payment</strong>
                                <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-30" style="font-size:10px; font-weight:600;">Post Office Dfs</span>
                            </div>
                            <div class="text-secondary" style="font-size:11.5px; margin-top:2px;">Fast payment with Nagad account or App · Secure PIN checkout</div>
                        </div>
                        <span class="badge bg-light text-secondary border px-2.5 py-1.5 ms-auto flex-shrink-0" style="font-size:10.5px; border-radius:6px;">
                            <i class="fa-solid fa-shield-check text-warning me-1"></i> Verified
                        </span>
                    </label>

                    {{-- 3. Debit & Credit Cards (Visa, Mastercard, Amex, UnionPay) --}}
                    <label class="pay-option position-relative d-flex align-items-center gap-3" id="pay_label_card" style="border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; cursor: pointer; transition: all 0.2s; background:#ffffff;">
                        <input type="radio" name="payment_method" value="card" id="pay_card" style="width:18px; height:18px; accent-color:#1a1f36; flex-shrink:0;">
                        {{-- Authentic Combined Cards Vector Badge --}}
                        <div style="width:72px; height:40px; background:#0f172a; border-radius:8px; display:flex; align-items:center; justify-content:center; gap:4px; flex-shrink:0; box-shadow:0 3px 8px rgba(15,23,42,0.25); padding:3px 6px;">
                            <svg viewBox="0 0 36 22" width="28" height="17" fill="none">
                                <rect width="36" height="22" rx="3" fill="#1434CB"/>
                                <text x="3" y="16" fill="#ffffff" font-size="12" font-style="italic" font-weight="900" font-family="'Plus Jakarta Sans', sans-serif">VISA</text>
                            </svg>
                            <svg viewBox="0 0 32 22" width="24" height="17" fill="none">
                                <circle cx="10" cy="11" r="8" fill="#EB001B"/>
                                <circle cx="22" cy="11" r="8" fill="#F79E1B" fill-opacity="0.88"/>
                            </svg>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="text-dark" style="font-size:14px; font-weight:700;">Debit / Credit Card (Local &amp; Global)</strong>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:10px; font-weight:600;">Visa · Master · AmEx</span>
                            </div>
                            <div class="text-secondary" style="font-size:11.5px; margin-top:2px;">Support all Bangladeshi &amp; International Visa, MasterCard, UnionPay, American Express</div>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 ms-auto flex-shrink-0">
                            <span class="d-inline-flex align-items-center justify-content-center px-1.5 py-0.5 rounded border shadow-xs" style="background:#ffffff; height:24px; border-color:#cbd5e1 !important;">
                                <i class="fa-brands fa-cc-visa text-primary" style="font-size:18px;"></i>
                            </span>
                            <span class="d-inline-flex align-items-center justify-content-center px-1.5 py-0.5 rounded border shadow-xs" style="background:#ffffff; height:24px; border-color:#cbd5e1 !important;">
                                <i class="fa-brands fa-cc-mastercard text-danger" style="font-size:18px;"></i>
                            </span>
                            <span class="d-inline-flex align-items-center justify-content-center px-1.5 py-0.5 rounded border shadow-xs" style="background:#ffffff; height:24px; border-color:#cbd5e1 !important;">
                                <i class="fa-brands fa-cc-amex text-info" style="font-size:18px;"></i>
                            </span>
                        </div>
                    </label>

                    {{-- 4. SSLCommerz Internet Banking & Multi-Bank --}}
                    <label class="pay-option position-relative d-flex align-items-center gap-3" id="pay_label_ssl" style="border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; cursor: pointer; transition: all 0.2s; background:#ffffff;">
                        <input type="radio" name="payment_method" value="sslcommerz" id="pay_ssl" style="width:18px; height:18px; accent-color:#006eb4; flex-shrink:0;">
                        {{-- Authentic Official SSLCommerz Logo Badge --}}
                        <div style="width:72px; height:40px; background:linear-gradient(135deg, #005691 0%, #0077b6 100%); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 3px 8px rgba(0,110,180,0.28); padding:4px;">
                            <div class="d-flex align-items-center gap-1">
                                <i class="fa-solid fa-shield-halved text-white" style="font-size:12px;"></i>
                                <span style="color:#ffffff; font-size:12px; font-weight:900; letter-spacing:0.8px; font-family:'Plus Jakarta Sans',sans-serif;">SSL</span>
                            </div>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="text-dark" style="font-size:14px; font-weight:700;">SSLCommerz — All Bangladeshi Banks</strong>
                                <span class="badge bg-info bg-opacity-10 text-dark border border-info border-opacity-25" style="font-size:10px; font-weight:600;">30+ Banks</span>
                            </div>
                            <div class="text-secondary" style="font-size:11.5px; margin-top:2px;">Dutch-Bangla NexusPay, Rocket, CityTouch, BRAC Bank, Islami Bank, EBL, MTB, UCB</div>
                        </div>
                        <span class="badge bg-light text-primary border px-2.5 py-1.5 ms-auto flex-shrink-0" style="font-size:10.5px; font-weight:600;">
                            Net Banking
                        </span>
                    </label>

                    {{-- 5. Pay at Hotel / Cash on Arrival --}}
                    <label class="pay-option position-relative d-flex align-items-center gap-3" id="pay_label_cash" style="border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; cursor: pointer; transition: all 0.2s; background:#ffffff;">
                        <input type="radio" name="payment_method" value="cash" id="pay_cash" style="width:18px; height:18px; accent-color:#16a34a; flex-shrink:0;">
                        {{-- Emerald Hotel Reception Badge --}}
                        <div style="width:72px; height:40px; background:linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 3px 8px rgba(16,185,129,0.28);">
                            <i class="fa-solid fa-hotel text-white fs-5"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="text-dark" style="font-size:14px; font-weight:700;">Pay at Hotel / Front Desk</strong>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size:10px; font-weight:700;">No Prepayment</span>
                            </div>
                            <div class="text-secondary" style="font-size:11.5px; margin-top:2px;">Reserve your room today · Pay cash or card at the property during check-in</div>
                        </div>
                        <span class="badge bg-warning bg-opacity-20 text-dark fw-bold border border-warning border-opacity-30 px-2.5 py-1.5 ms-auto flex-shrink-0" style="font-size:10.5px;">
                            0% Advance
                        </span>
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
                @if(isset($vipDiscountAmount) && $vipDiscountAmount > 0)
                <div class="summary-row text-success fw-bold" style="background:#f0fdf4; padding:6px 8px; border-radius:6px; border:1px solid #bbf7d0;">
                    <span><i class="fa-solid fa-crown text-warning me-1"></i> {{ $vipStats['tier_name_full'] ?? 'AgodaVIP' }} ({{ $vipStats['discount_percent'] }}% OFF)</span>
                    <span>- {{ CurrencyService::format($vipDiscountAmount) }}</span>
                </div>
                @endif

                @if(isset($primaryPointsmax) && $earnedMiles > 0)
                <div class="summary-row fw-bold" style="background:#eef2ff; color:#4338ca; padding:6px 8px; border-radius:6px; border:1px solid #c7d2fe;">
                    <span><i class="fa-solid fa-plane-departure me-1"></i> PointsMAX ({{ $primaryPointsmax['program'] }})</span>
                    <span>+{{ number_format($earnedMiles) }} Miles</span>
                </div>
                @endif
                @if(isset($earnablePoints) && $earnablePoints > 0)
                <div class="summary-row fw-bold" style="background:#fffbeb; color:#b45309; padding:6px 8px; border-radius:6px; border:1px solid #fef08a;">
                    <span><i class="fa-solid fa-coins text-warning me-1"></i> Earn Prime Rewards</span>
                    <span>+{{ number_format($earnablePoints) }} Pts (৳{{ number_format($earnablePoints * ($rewardSummary['point_value'] ?? 10)) }})</span>
                </div>
                @endif
                <div class="summary-row text-success" id="discount_row" style="{{ isset($appliedDiscount) && $appliedDiscount > 0 && !isset($vipDiscountAmount) ? '' : 'display:none;' }}">
                    <span><i class="fa-solid fa-tag me-1"></i> Promo (<span id="coupon_badge_text">{{ $activePromoCode ?? '' }}</span>)</span>
                    <span id="discount_amount_text">- {{ CurrencyService::format($appliedDiscount ?? 0) }}</span>
                </div>
                <div class="summary-row">
                    <span>Taxes & Fees (7.5%)</span>
                    <span id="tax_display">{{ CurrencyService::format($taxAmount) }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="grand_total" class="text-primary">{{ CurrencyService::format($totalPrice) }}</span>
                </div>

                @if(isset($rewardSummary) && $rewardSummary['can_withdraw'])
                {{-- ── 1-Click Prime Rewards Redemption Toggle ── --}}
                <div class="mt-3 p-3 rounded-3" style="background:#f0fdf4; border:1.5px solid #86efac;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width:22px; height:22px; font-size:11px;">
                                <i class="fa-solid fa-coins"></i>
                            </span>
                            <span class="fw-bold text-dark" style="font-size:12.5px;">Redeem Prime Rewards</span>
                        </div>
                        <span class="badge bg-success text-white fw-bold">{{ number_format($rewardSummary['points_balance']) }} Pts Available</span>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="useRewardsToggle" name="use_rewards" value="1" style="cursor:pointer; width:38px; height:20px;">
                        <label class="form-check-label text-dark fw-medium small ms-1" for="useRewardsToggle" style="cursor:pointer;">
                            Use {{ number_format($rewardSummary['points_balance']) }} Points to get <strong>-৳{{ number_format($rewardSummary['bdt_value']) }} OFF</strong>
                        </label>
                    </div>
                </div>
                @endif

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
                    <div class="text-muted mb-2" style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Trusted &amp; Accepted Payments</div>
                    <div class="d-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                        {{-- bKash Official Mini Pill --}}
                        <span class="d-inline-flex align-items-center justify-content-center px-2 py-1 rounded shadow-xs" style="background:#e2136e; color:#ffffff; font-size:10.5px; font-weight:900; height:24px; font-family:'Plus Jakarta Sans',sans-serif; letter-spacing:-0.2px;">
                            bKash
                        </span>
                        {{-- Nagad Official Mini Pill --}}
                        <span class="d-inline-flex align-items-center justify-content-center px-2 py-1 rounded shadow-xs" style="background:linear-gradient(135deg,#f7941d,#ed1c24); color:#ffffff; font-size:10.5px; font-weight:900; height:24px; font-family:'Plus Jakarta Sans',sans-serif; letter-spacing:-0.2px;">
                            Nagad
                        </span>
                        {{-- Visa Card --}}
                        <span class="d-inline-flex align-items-center justify-content-center px-2 py-0.5 rounded border shadow-xs" style="background:#ffffff; height:24px; border-color:#cbd5e1 !important;">
                            <i class="fa-brands fa-cc-visa text-primary" style="font-size:18px;"></i>
                        </span>
                        {{-- MasterCard --}}
                        <span class="d-inline-flex align-items-center justify-content-center px-2 py-0.5 rounded border shadow-xs" style="background:#ffffff; height:24px; border-color:#cbd5e1 !important;">
                            <i class="fa-brands fa-cc-mastercard text-danger" style="font-size:18px;"></i>
                        </span>
                        {{-- Amex --}}
                        <span class="d-inline-flex align-items-center justify-content-center px-2 py-0.5 rounded border shadow-xs" style="background:#ffffff; height:24px; border-color:#cbd5e1 !important;">
                            <i class="fa-brands fa-cc-amex text-info" style="font-size:18px;"></i>
                        </span>
                        {{-- SSLCommerz --}}
                        <span class="d-inline-flex align-items-center justify-content-center px-2 py-1 rounded shadow-xs" style="background:#006eb4; color:#ffffff; font-size:9.5px; font-weight:900; height:24px; font-family:'Plus Jakarta Sans',sans-serif;">
                            SSL
                        </span>
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
    const useRewardsToggle  = document.getElementById('useRewardsToggle');
    const rewardsDiscountVal = {{ isset($rewardSummary) ? (float)$rewardSummary['bdt_value'] : 0 }};

    function formatBDT(n) {
        return '৳ ' + Math.round(n).toLocaleString('en-BD');
    }

    function recalculate() {
        let addons = 0;
        document.querySelectorAll('.addon-check:checked').forEach(function(cb) {
            addons += parseFloat(cb.dataset.price) || 0;
        });

        let effectiveDiscount = appliedDiscount;
        if (useRewardsToggle && useRewardsToggle.checked) {
            effectiveDiscount += rewardsDiscountVal;
        }

        const netBase = Math.max(0, subtotal - effectiveDiscount + addons);
        const tax     = Math.round(netBase * taxRate);
        const total   = netBase + tax;

        if (addons > 0) {
            addonsRow.style.display = 'flex';
            addonsTotal.textContent = formatBDT(addons);
        } else {
            addonsRow.style.display = 'none';
        }

        if (effectiveDiscount > 0) {
            discountRow.style.display = 'flex';
            if (useRewardsToggle && useRewardsToggle.checked) {
                couponBadgeText.textContent = appliedCouponCode ? (appliedCouponCode + ' + Rewards') : 'Rewards Points';
            } else {
                couponBadgeText.textContent = appliedCouponCode;
            }
            discountAmountText.textContent = '- ' + formatBDT(effectiveDiscount);
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

    if (useRewardsToggle) {
        useRewardsToggle.addEventListener('change', recalculate);
    }

    // Payment Option Card Selection
    document.querySelectorAll('.pay-option').forEach(function(card) {
        card.addEventListener('change', function() {
            document.querySelectorAll('.pay-option').forEach(el => el.classList.remove('selected'));
            this.closest('.pay-option').classList.add('selected');
        });
    });

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
