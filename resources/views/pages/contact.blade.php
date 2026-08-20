@extends('layouts.main', ['activePage' => 'contact'])

@section('title', 'Contact Us & Customer Support | Prime Booking')

@section('content')
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 22px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-headset text-warning me-2" style="font-size: 20px;"></i> {{ __('Customer Support & Contact') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500;">
                {{ __('We are available 24/7 to assist you with your hotel, flight & holiday bookings') }}
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 28px;">📞</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">{{ __('24/7 Dedicated Customer Care') }}</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">01770887733</div>
                </div>
            </div>
            <span style="font-size: 38px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" class="d-none d-lg-inline-block">🎧</span>
        </div>
    </div>
</div>

<div class="py-5" style="max-width: 1240px; margin: 0 auto; padding-left: 15px; padding-right: 15px;">
    <div class="row g-4">
        {{-- Contact Form --}}
        <div class="col-lg-7" id="inquiry">
            <div class="card border border-gray-200 rounded-3 p-4 bg-white shadow-xs" style="border-radius: 12px !important;">
                <h5 class="fw-bold text-dark mb-3">{{ __('Send Us a Message / Inquiry') }}</h5>

                @if(session('success'))
                    <div class="alert alert-success rounded-3 small">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('inquiry.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control rounded-2" placeholder="e.g. Shawon" required style="font-size: 13px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control rounded-2" placeholder="017xxxxxxxx" required style="font-size: 13px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-2" placeholder="name@example.com" style="font-size: 13px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject / Service Requested</label>
                        <input type="text" name="subject" class="form-control rounded-2" value="{{ request('property') ? 'Inquiry for ' . request('property') : (request('package') ? 'Package: ' . request('package') : '') }}" placeholder="e.g. Hotel Booking, Flight Inquiry" style="font-size: 13px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Message / Special Request</label>
                        <textarea name="message" rows="4" class="form-control rounded-2" placeholder="Describe your travel dates, number of guests, or special requirements..." style="font-size: 13px;"></textarea>
                    </div>
                    <button type="submit" class="btn text-white fw-bold px-4 py-2" style="background-color: #2067e1; border-radius: 8px; font-size: 14px;">
                        SUBMIT INQUIRY <i class="fa-solid fa-paper-plane ms-1"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Contact Info Sidebar --}}
        <div class="col-lg-5">
            <div class="card border border-gray-200 rounded-3 p-4 bg-white shadow-xs mb-4" style="border-radius: 12px !important;">
                <h5 class="fw-bold text-dark mb-3">Head Office</h5>
                
                <div class="d-flex align-items-start gap-3 mb-3">
                    <i class="fa-solid fa-location-dot text-danger fs-5 mt-1"></i>
                    <div>
                        <strong class="text-dark d-block mb-1">Prime Booking</strong>
                        <span class="text-secondary small">{{ $siteSettings['address'] ?? \App\Models\SiteSetting::get('site_address', 'House #12, Road #04, Block #A, Banani, Dhaka-1213, Bangladesh') }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-3">
                    <i class="fa-solid fa-phone text-primary fs-5 mt-1"></i>
                    <div>
                        <strong class="text-dark d-block mb-1">Hotline &amp; Support</strong>
                        <span class="text-secondary small d-block">{{ $siteSettings['phone'] ?? \App\Models\SiteSetting::get('site_phone', '+880 1770-887733') }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <i class="fa-solid fa-envelope text-info fs-5 mt-1"></i>
                    <div>
                        <strong class="text-dark d-block mb-1">Email Support</strong>
                        <span class="text-secondary small d-block">{{ $siteSettings['email'] ?? \App\Models\SiteSetting::get('site_email', 'support@primebooking.com.bd') }}</span>
                    </div>
                </div>
            </div>

            <div class="card border border-gray-200 rounded-3 p-4 bg-white shadow-xs" style="border-radius: 12px !important;">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-shield-halved text-success me-2"></i> Trade License Verified</h6>
                <p class="text-secondary small mb-0">Government Registered OTA &amp; Aviation Partner in Bangladesh.</p>
            </div>
        </div>
    </div>
</div>
@endsection

