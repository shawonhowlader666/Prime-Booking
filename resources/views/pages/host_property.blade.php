@extends('layouts.main', ['activePage' => 'host'])

@section('title', 'List Your Property | Prime Booking & Aviation Partner')

@section('content')
{{-- Hero Subheader with Dark Gradient & Partner Graphic --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 25px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 mb-2" style="font-size: 12px; border-radius: 6px;">
                <i class="fa-solid fa-handshake me-1"></i> Partner With Us
            </span>
            <h2 class="fw-bold mb-1" style="font-size: 24px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                {{ __('List Your Hotel, Villa or Apartment') }}
            </h2>
            <p class="mb-0" style="font-size: 14px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('Reach millions of international and domestic travelers. Free property listing with instant booking payouts.') }}
            </p>
        </div>

        <!-- Right Side Partner Graphic -->
        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 10px 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2.5">
                <span style="font-size: 30px;">🏢</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">0% Listing Fee</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Instant Payouts</div>
                </div>
            </div>
            <div style="font-size: 44px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                🤝
            </div>
        </div>
    </div>
</div>

<div class="py-5" style="background-color: #f4f6fa; min-height: 80vh;">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 15px;">
        
        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white" style="border-radius: 20px !important;">
            <h4 class="fw-bold text-dark mb-4" style="font-size: 20px;">Property Host Registration</h4>

            <form action="{{ route('contact') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Property Name</label>
                        <input type="text" class="form-control rounded-3 py-2" placeholder="e.g. Royal Beach Resort & Spa" style="font-size: 14px; border-color: #cbd5e1;" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Property Type</label>
                        <select class="form-select rounded-3 py-2" style="font-size: 14px; border-color: #cbd5e1;">
                            <option value="hotel" selected>Hotel / Resort</option>
                            <option value="apartment">Vacation Apartment</option>
                            <option value="villa">Private Villa</option>
                            <option value="guesthouse">Guest House / Cottage</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Owner / Manager Name</label>
                        <input type="text" class="form-control rounded-3 py-2" placeholder="Full Contact Name" style="font-size: 14px; border-color: #cbd5e1;" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Hotline Phone / Mobile</label>
                        <input type="text" class="form-control rounded-3 py-2" placeholder="+880 1700-000000" style="font-size: 14px; border-color: #cbd5e1;" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Property Address &amp; Location</label>
                        <input type="text" class="form-control rounded-3 py-2" placeholder="Street Address, City, Division" style="font-size: 14px; border-color: #cbd5e1;" required>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn text-white fw-bold px-4 py-2.5 rounded-pill shadow-sm" style="background-color: #2067e1; font-size: 14.5px;">
                        {{ __('Submit Partner Application') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
