@extends('layouts.main', ['activePage' => 'about'])

@section('title', 'About Prime Booking | Official Travel & Booking Platform')

@section('content')
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 22px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-building-flag text-warning me-2" style="font-size: 20px;"></i> {{ __('About Prime Booking') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500;">
                {{ __("Bangladesh's premier online travel agency delivering world-class travel solutions") }}
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 28px;">🌟</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Trusted OTA</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Govt Licensed</div>
                </div>
            </div>
            <span style="font-size: 38px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" class="d-none d-lg-inline-block">🏢</span>
        </div>
    </div>
</div>

<div class="py-5" style="max-width: 1240px; margin: 0 auto; padding-left: 15px; padding-right: 15px;">
    <div class="card border border-gray-200 rounded-3 p-4 bg-white shadow-xs mb-4" style="border-radius: 12px !important;">
        <h4 class="fw-bold text-dark mb-3">{{ $aboutCms->title ?? __('Who We Are') }}</h4>
        <div class="text-secondary" style="font-size: 14.5px; line-height: 1.8;">
            {!! nl2br(e($aboutCms->content ?? 'Prime Booking is Bangladesh\'s premier hotel, flight, and tour booking platform.')) !!}
        </div>
    </div>

    <!-- Official Credentials Card -->
    <div class="card border border-gray-200 rounded-3 p-4 bg-white shadow-xs mb-4" style="border-radius: 12px !important;">
        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-building text-primary me-2"></i>{{ __('Official Company Credentials') }}</h5>
        <div class="row g-3 text-secondary" style="font-size: 14px;">
            <div class="col-md-6">
                <strong><i class="fa-solid fa-file-contract text-secondary me-2"></i>{{ __('Trade License') }}:</strong> 19/515
            </div>
            <div class="col-md-6">
                <strong><i class="fa-solid fa-globe text-secondary me-2"></i>{{ __('Official Website') }}:</strong> <a href="https://primeavn.com" target="_blank" class="text-primary text-decoration-none">primeavn.com</a>
            </div>
            <div class="col-md-6">
                <strong><i class="fa-solid fa-location-dot text-danger me-2"></i>{{ __('Head Office') }}:</strong> 46, KDA Avenue, Jiban Bima Bhaban, Khulna, Bangladesh
            </div>
            <div class="col-md-6">
                <strong><i class="fa-solid fa-phone text-success me-2"></i>{{ __('Support Hotline') }}:</strong> 01770887733, 01785880033
            </div>
            <div class="col-md-6">
                <strong><i class="fa-solid fa-envelope text-primary me-2"></i>{{ __('Email Support') }}:</strong> primeaviation.ltd@gmail.com
            </div>
            <div class="col-md-6">
                <strong><i class="fa-solid fa-credit-card text-warning me-2"></i>Local Payment Partners:</strong> bKash, Nagad, Rocket, Visa &amp; Mastercard
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border border-gray-200 rounded-3 p-4 bg-white text-center h-100" style="border-radius: 12px !important;">
                <i class="fa-solid fa-hotel text-primary display-5 mb-3"></i>
                <h5 class="fw-bold text-dark mb-2">2M+ Hotels</h5>
                <p class="text-secondary small mb-0">Access over 2 million verified hotels, resorts, and apartments worldwide.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border border-gray-200 rounded-3 p-4 bg-white text-center h-100" style="border-radius: 12px !important;">
                <i class="fa-solid fa-plane text-success display-5 mb-3"></i>
                <h5 class="fw-bold text-dark mb-2">Flight Ticketing</h5>
                <p class="text-secondary small mb-0">Direct GDS integration with 150+ airlines for domestic &amp; international flights.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border border-gray-200 rounded-3 p-4 bg-white text-center h-100" style="border-radius: 12px !important;">
                <i class="fa-solid fa-headset text-warning display-5 mb-3"></i>
                <h5 class="fw-bold text-dark mb-2">24/7 Local Support</h5>
                <p class="text-secondary small mb-0">Bengali &amp; English support hotline for instant booking assistance.</p>
            </div>
        </div>
    </div>
</div>
@endsection
