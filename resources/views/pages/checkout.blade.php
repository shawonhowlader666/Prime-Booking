@extends('layouts.main', ['activePage' => 'home'])

@section('title', 'Secure Booking & Checkout | Prime Aviation')

@section('content')
{{-- Stepper Progress Bar (Agoda 1:1 Parity) --}}
<div class="bg-white border-bottom py-3">
    <div class="container" style="max-width: 1240px;">
        <div class="d-flex align-items-center justify-content-center gap-2 gap-md-5" style="font-size: 13px; font-weight: 600;">
            <div class="d-flex align-items-center gap-2 text-primary">
                <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; font-size: 12px;">1</span>
                <span>Customer Information</span>
            </div>
            <i class="fa-solid fa-chevron-right text-muted" style="font-size: 10px;"></i>
            <div class="d-flex align-items-center gap-2 text-primary">
                <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; font-size: 12px;">2</span>
                <span>Payment Details</span>
            </div>
            <i class="fa-solid fa-chevron-right text-muted d-none d-md-inline" style="font-size: 10px;"></i>
            <div class="d-flex align-items-center gap-2 text-muted d-none d-md-flex">
                <span class="rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center fw-bold" style="width: 26px; height: 26px; font-size: 12px;">3</span>
                <span>Booking Confirmed!</span>
            </div>
        </div>
    </div>
</div>

<div class="py-4" style="max-width: 1240px; margin: 0 auto; padding-left: 15px; padding-right: 15px;">

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="room_id" value="{{ $room ? $room->id : '' }}">
        <input type="hidden" name="check_in" value="{{ $checkIn }}">
        <input type="hidden" name="check_out" value="{{ $checkOut }}">
        <input type="hidden" name="adults" value="{{ $adults }}">
        <input type="hidden" name="children" value="{{ $children }}">

        <div class="row g-4">
            
            {{-- Left 8 Columns: Guest Info & Payment Methods --}}
            <div class="col-lg-8">
                
                {{-- Urgency Alert Banner --}}
                <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 mb-4 d-flex align-items-center gap-3">
                    <i class="fa-solid fa-fire-flame-curved text-danger fs-4"></i>
                    <div>
                        <strong class="text-danger" style="font-size: 14px;">High Demand in {{ $property->location->city ?? 'Cox\'s Bazar' }}!</strong>
                        <div class="text-secondary small">We are holding this special price for you. Complete your booking before rates change.</div>
                    </div>
                </div>

                {{-- 1. Guest Details Card --}}
                <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border: 1px solid #e2e8f0; border-radius: 12px !important;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</span>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 17px;">Primary Guest Contact Details</h5>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Full Name (As per NID / Passport) <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" class="form-control rounded-2" placeholder="e.g. Shawon Ahmed" value="{{ auth()->user()->name ?? '' }}" required style="font-size: 14px; height: 44px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Email Address (For Booking Voucher) <span class="text-danger">*</span></label>
                            <input type="email" name="guest_email" class="form-control rounded-2" placeholder="name@example.com" value="{{ auth()->user()->email ?? '' }}" required style="font-size: 14px; height: 44px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Mobile Number (For Instant SMS Updates) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark fw-semibold" style="font-size: 13px;">🇧🇩 +88</span>
                                <input type="tel" name="guest_phone" class="form-control rounded-end-2" placeholder="017xxxxxxxx" value="{{ auth()->user()->phone ?? '01770887733' }}" required style="font-size: 14px; height: 44px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Country / Territory</label>
                            <select class="form-select rounded-2" style="font-size: 14px; height: 44px;">
                                <option value="BD" selected>Bangladesh 🇧🇩</option>
                                <option value="AE">United Arab Emirates 🇦🇪</option>
                                <option value="TH">Thailand 🇹🇭</option>
                                <option value="SA">Saudi Arabia 🇸🇦</option>
                            </select>
                        </div>
                    </div>

                    {{-- Special Requests --}}
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label small fw-bold text-dark">Special Requests (Optional)</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3" onclick="appendReq('High Floor')">+ High Floor</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3" onclick="appendReq('Non-smoking room')">+ Non-smoking room</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3" onclick="appendReq('Late check-in')">+ Late check-in</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3" onclick="appendReq('Large bed')">+ Large bed</button>
                        </div>
                        <textarea name="special_requests" id="special_req_box" class="form-control rounded-2" rows="2" placeholder="Any special instructions for the hotel staff..." style="font-size: 13px;"></textarea>
                    </div>
                </div>

                {{-- 2. Payment Method Card (Agoda 1:1 Parity) --}}
                <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border: 1px solid #e2e8f0; border-radius: 12px !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</span>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 17px;">Select Payment Method</h5>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-2 py-1" style="font-size: 11px;">
                            <i class="fa-solid fa-lock me-1"></i> 256-Bit SSL Encrypted
                        </span>
                    </div>

                    {{-- Payment Tabs / Radio Options --}}
                    <div class="d-flex flex-column gap-3 mb-4">
                        
                        {{-- bKash Option --}}
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option-card bg-light" style="cursor: pointer; border-radius: 10px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="payment_method" value="bkash" checked class="form-check-input">
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 14px;">bKash Online Payment</strong>
                                    <small class="text-muted" style="font-size: 12px;">Instant confirmation via bKash gateway / PIN</small>
                                </div>
                            </div>
                            <span class="badge bg-danger text-white fw-bold px-2 py-1" style="background-color: #e2136e !important; font-size: 12px;">bKash</span>
                        </label>

                        {{-- Nagad Option --}}
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option-card" style="cursor: pointer; border-radius: 10px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="payment_method" value="nagad" class="form-check-input">
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 14px;">Nagad Digital Wallet</strong>
                                    <small class="text-muted" style="font-size: 12px;">Fast payment using Nagad wallet</small>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1" style="background-color: #f7921e !important; color: #fff !important; font-size: 12px;">Nagad</span>
                        </label>

                        {{-- Cards Option --}}
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option-card" style="cursor: pointer; border-radius: 10px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="payment_method" value="card" class="form-check-input">
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 14px;">Credit / Debit Card (Visa, Mastercard, AMEX)</strong>
                                    <small class="text-muted" style="font-size: 12px;">Local or International Credit Card</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1 fs-5 text-secondary">
                                <i class="fa-brands fa-cc-visa text-primary"></i>
                                <i class="fa-brands fa-cc-mastercard text-danger"></i>
                                <i class="fa-brands fa-cc-amex text-info"></i>
                            </div>
                        </label>

                        {{-- Bank Transfer / Pay at Hotel --}}
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option-card" style="cursor: pointer; border-radius: 10px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="payment_method" value="pay_at_hotel" class="form-check-input">
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 14px;">Pay at Hotel / Bank Deposit</strong>
                                    <small class="text-muted" style="font-size: 12px;">Pay upon check-in at property desk</small>
                                </div>
                            </div>
                            <span class="badge bg-secondary text-white fw-bold px-2 py-1" style="font-size: 11px;">Pay Later</span>
                        </label>

                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn text-white w-100 fw-bold py-3 shadow-sm" style="background-color: #2067e1; border-radius: 10px; font-size: 16px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-lock me-2"></i> CONFIRM &amp; PAY BDT {{ number_format($totalAmount, 0) }}
                    </button>
                    
                    <div class="text-center mt-3 text-muted small" style="font-size: 11.5px;">
                        By clicking "Confirm &amp; Pay", you agree to Prime Aviation's <a href="{{ route('terms') }}" target="_blank" class="text-primary text-decoration-none">Terms of Service</a> and <a href="{{ route('privacy') }}" target="_blank" class="text-primary text-decoration-none">Cancellation Policy</a>.
                    </div>
                </div>

            </div>

            {{-- Right 4 Columns: Sticky Price Summary Sidebar --}}
            <div class="col-lg-4">
                <div class="card border border-gray-200 rounded-3 p-4 sticky-top shadow-sm bg-white" style="top: 80px; z-index: 100; border-radius: 12px !important;">
                    
                    {{-- Property Card Summary --}}
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <img src="{{ $property->primary_image ?? 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=300&q=80' }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;" alt="{{ $property->name }}">
                        <div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 14px; line-height: 1.2;">{{ $property->name }}</h6>
                            <div class="text-warning mb-1" style="font-size: 11px;">
                                @for($s=0; $s<($property->star_rating ?? 5); $s++)★@endfor
                            </div>
                            <small class="text-muted d-block" style="font-size: 11px;"><i class="fa-solid fa-location-dot me-1"></i> {{ $property->location->city ?? 'Cox\'s Bazar' }}</small>
                        </div>
                    </div>

                    {{-- Stay Dates Summary --}}
                    <div class="bg-light p-3 rounded-3 mb-3" style="font-size: 12.5px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Check-in:</span>
                            <strong class="text-dark">{{ date('D, M d, Y', strtotime($checkIn)) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Check-out:</span>
                            <strong class="text-dark">{{ date('D, M d, Y', strtotime($checkOut)) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top mt-2">
                            <span class="text-muted">Total Stay:</span>
                            <strong class="text-primary">{{ $nights }} Night(s), {{ $adults }} Adult(s)</strong>
                        </div>
                    </div>

                    {{-- Room Summary --}}
                    <div class="mb-3 pb-3 border-bottom" style="font-size: 13px;">
                        <div class="fw-bold text-dark mb-1">Selected Room:</div>
                        <div class="text-secondary">{{ $room ? $room->name : 'Superior Sea View Room' }}</div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-2 py-1 mt-1" style="font-size: 10px;">
                            <i class="fa-solid fa-check me-1"></i> Free Cancellation
                        </span>
                    </div>

                    {{-- Price Breakdown --}}
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 14px;">Price Summary</h6>
                    <div class="d-flex flex-column gap-2 mb-3" style="font-size: 13px;">
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Room Price ({{ $nights }} night x BDT {{ number_format($pricePerNight, 0) }})</span>
                            <span class="fw-semibold text-dark">BDT {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Taxes &amp; Service Fees (12%)</span>
                            <span class="fw-semibold text-dark">BDT {{ number_format($taxesAndFees, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-success">
                            <span>Unlocked Trip Savings Discount</span>
                            <span class="fw-bold">- BDT {{ number_format($discount, 0) }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-baseline justify-content-between pt-3 border-top mb-3">
                        <strong class="text-dark" style="font-size: 16px;">Total Price</strong>
                        <div class="text-end">
                            <div class="fw-bold text-primary" style="font-size: 24px; color: #2067e1 !important; line-height: 1.1;">
                                BDT {{ number_format($totalAmount, 0) }}
                            </div>
                            <small class="text-muted" style="font-size: 10px;">Includes all taxes &amp; fees</small>
                        </div>
                    </div>

                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-2 text-center small fw-semibold">
                        <i class="fa-solid fa-shield-halved me-1"></i> Best Price Guarantee Included
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>

<script>
function appendReq(text) {
    var box = document.getElementById('special_req_box');
    if (box.value.length > 0) {
        box.value += ', ' + text;
    } else {
        box.value = text;
    }
}
</script>
@endsection
