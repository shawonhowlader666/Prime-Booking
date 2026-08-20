@extends('layouts.main', ['activePage' => 'pointsmax'])

@section('title', 'PointsMAX Airline Partners | Prime Booking')

@section('content')
<div class="py-4" style="background-color: #f4f6fa; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        <div class="row g-4">
            
            <!-- Left White Sidebar Navigation (1:1 Exact Match of Agoda Live) -->
            <div class="col-lg-3 col-md-4" style="max-width: 270px;">
                <x-user-sidebar activePage="pointsmax" />
            </div>

            <!-- Right Column: PointsMAX Partner Miles -->
            <div class="col-lg-9 col-md-8">
                
                <!-- PointsMAX Banner (Slim & Compact) -->
                <div class="card border-0 rounded-4 p-3.5 p-md-4 mb-4 shadow-sm text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); border-radius: 16px !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="mb-2">
                                <span class="badge bg-warning text-dark fw-bold px-2.5 py-1" style="font-size: 11px; border-radius: 6px;">
                                    <i class="fa-solid fa-plane-departure me-1"></i> Earn Airline Miles
                                </span>
                            </div>
                            <h4 class="fw-bold mb-1" style="color: #ffffff !important; font-size: 20px; letter-spacing: -0.3px; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                                {{ __('Earn Miles on Every Hotel Booking') }}
                            </h4>
                            <p class="mb-0" style="max-width: 580px; font-size: 13.5px; color: #e2e8f0 !important; opacity: 0.95;">
                                {{ __('Choose your favorite airline loyalty program (Emirates Skywards, Qatar Privilege, Biman Bangladesh) and collect up to 6,000 bonus miles per stay!') }}
                            </p>
                        </div>

                        <!-- Right Side Hotel & Miles Badge Graphic -->
                        <div class="d-none d-lg-flex align-items-center gap-3">
                            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 10px 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2.5">
                                <span style="font-size: 28px;">🏨</span>
                                <div>
                                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">PointsMAX</div>
                                    <div style="font-size: 12px; font-weight: 800; color: #ffffff;">+6,000 MILES</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Airline Partners Cards Grid -->
                <div class="card border shadow-xs p-4" style="border-color: #cbd5e1 !important; border-radius: 18px !important; background-color: #ffffff;">
                    <h5 class="fw-bold mb-4 text-dark" style="font-size: 17px;">{{ __('Select Your Partner Loyalty Program') }}</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between" style="border-color: #cbd5e1 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-danger text-white fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 14px;">
                                        EK
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Emirates Skywards</h6>
                                        <p class="text-secondary mb-0" style="font-size: 12px;">Earn up to 5,000 Skywards Miles</p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold agoda-hover-white-btn" style="font-size: 12px; border-color: #2067e1; color: #2067e1;">Link Account</button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between" style="border-color: #cbd5e1 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 14px;">
                                        BG
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Biman Bangladesh Club</h6>
                                        <p class="text-secondary mb-0" style="font-size: 12px;">Earn up to 4,000 Frequent Flyer Miles</p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold agoda-hover-white-btn" style="font-size: 12px; border-color: #2067e1; color: #2067e1;">Link Account</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
