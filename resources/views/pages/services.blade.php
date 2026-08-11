@extends('layouts.main', ['activePage' => 'services'])

@section('title', 'Services & Transport | Prime Booking')

@section('content')
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 22px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-car-side text-warning me-2" style="font-size: 20px;"></i> {{ __('Transport & Travel Services') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500;">
                {{ __('Instant booking for Domestic Flights, Express Buses, Railway Trains, Launch & Airport Transfers') }}
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 28px;">🚖</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Airport Transfer</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Safe &amp; Reliable</div>
                </div>
            </div>
            <span style="font-size: 38px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" class="d-none d-lg-inline-block">✈️</span>
        </div>
    </div>
</div>

<div class="py-4" style="max-width: 1240px; margin: 0 auto; padding-left: 15px; padding-right: 15px;">

    {{-- Services Grid --}}
    <div class="row g-4 mb-5">
        
        {{-- Domestic Flights --}}
        <div class="col-md-6 col-lg-4" id="flights">
            <div class="card border border-gray-200 rounded-3 p-4 h-100 bg-white" style="border-radius: 12px !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-plane-departure fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">Domestic Flights</h5>
                        <small class="text-muted">Biman BD, US-Bangla, Air Astra</small>
                    </div>
                </div>
                <p class="text-secondary small mb-3">Book instant domestic flights between Dhaka, Cox's Bazar, Chittagong, Sylhet, Saidpur, Rajshahi &amp; Barisal.</p>
                <a href="{{ route('search.index') }}?type=flight" class="btn btn-outline-primary btn-sm fw-bold rounded-2 mt-auto align-self-start" style="color: #2067e1; border-color: #2067e1;">
                    SEARCH FLIGHTS <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        {{-- Express Buses --}}
        <div class="col-md-6 col-lg-4" id="bus">
            <div class="card border border-gray-200 rounded-3 p-4 h-100 bg-white" style="border-radius: 12px !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-bus fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">Express Buses</h5>
                        <small class="text-muted">Green Line, Hanif, Ena, Shyamoli</small>
                    </div>
                </div>
                <p class="text-secondary small mb-3">AC Sleeper, Business Class &amp; Economy bus tickets nationwide with seat selection.</p>
                <a href="{{ route('contact') }}#inquiry" class="btn btn-outline-success btn-sm fw-bold rounded-2 mt-auto align-self-start">
                    BOOK BUS SEATS <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        {{-- Railway Trains --}}
        <div class="col-md-6 col-lg-4" id="train">
            <div class="card border border-gray-200 rounded-3 p-4 h-100 bg-white" style="border-radius: 12px !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-train fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">Railway Trains</h5>
                        <small class="text-muted">Cox's Bazar Express, Suborno, Parabat</small>
                    </div>
                </div>
                <p class="text-secondary small mb-3">Snigdha AC Chair &amp; Cabin train tickets assistance across all intercity routes.</p>
                <a href="{{ route('contact') }}#inquiry" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-2 mt-auto align-self-start">
                    TRAIN TICKETS <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        {{-- Launch & Ferries --}}
        <div class="col-md-6 col-lg-4" id="ferry">
            <div class="card border border-gray-200 rounded-3 p-4 h-100 bg-white" style="border-radius: 12px !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-ship fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">Launch &amp; Ferries</h5>
                        <small class="text-muted">Barisal &amp; Saint Martin Luxury Ships</small>
                    </div>
                </div>
                <p class="text-secondary small mb-3">VIP Cabin &amp; Deck seats for Barisal, Bhola, Saint Martin &amp; Sundarbans waterways.</p>
                <a href="{{ route('contact') }}#inquiry" class="btn btn-outline-info btn-sm fw-bold rounded-2 mt-auto align-self-start">
                    FERRY CABIN <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        {{-- Airport Transfer --}}
        <div class="col-md-6 col-lg-4" id="transfer">
            <div class="card border border-gray-200 rounded-3 p-4 h-100 bg-white" style="border-radius: 12px !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-car fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 17px;">Airport Transfer</h5>
                        <small class="text-muted">Private Sedan, SUV &amp; Microbus</small>
                    </div>
                </div>
                <p class="text-secondary small mb-3">24/7 Door-to-door pickup &amp; drop-off service for Hazrat Shahjalal International Airport.</p>
                <a href="{{ route('contact') }}#inquiry" class="btn btn-outline-danger btn-sm fw-bold rounded-2 mt-auto align-self-start">
                    BOOK CAR RIDE <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
