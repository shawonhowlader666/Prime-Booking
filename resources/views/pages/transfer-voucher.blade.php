@extends('layouts.main', ['activePage' => 'transfers'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Airport Taxi Transfer Voucher #' . $booking->booking_reference . ' | Prime Booking')

@section('content')
<style>
@media print {
    .no-print, header, footer, .navbar { display: none !important; }
    body { background: #fff !important; padding: 0 !important; }
    .voucher-card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>

<div class="py-5" style="background: #f8fafc; min-height: 85vh;">
    <div style="max-width: 820px; margin: 0 auto; padding: 0 15px;">
        
        {{-- Success Alert & Action Buttons --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 no-print">
            <div>
                <a href="{{ route('transfers.index') }}" class="text-decoration-none text-secondary fw-semibold small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Book Another Transfer
                </a>
                <h4 class="fw-bold text-dark mb-0 mt-1">Airport Transfer E-Voucher</h4>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-bold" onclick="window.print();">
                    <i class="fa-solid fa-print me-1"></i> Print / PDF
                </button>
                <a href="{{ route('booking.history') }}" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">
                    My Bookings
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4 no-print shadow-xs" style="font-size: 13.5px;">
            <i class="fa-solid fa-circle-check text-success me-1"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Voucher Card --}}
        <div class="card border-0 shadow-md rounded-4 overflow-hidden bg-white voucher-card">
            {{-- Header Strip --}}
            <div class="p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div>
                    <h3 class="fw-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">PRIME BOOKING</h3>
                    <small class="text-white-50" style="letter-spacing: 1px; font-size: 11px;">AIRPORT TAXI &amp; CHAUFFEUR TRANSFER VOUCHER</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold" style="font-size: 12px;">
                        <i class="fa-solid fa-check-circle me-1"></i> {{ strtoupper($booking->status ?? 'CONFIRMED') }}
                    </span>
                    <div class="mt-1 font-monospace text-white-50 small" style="font-size: 12px;">
                        Ref: <strong class="text-white">{{ $booking->booking_reference }}</strong>
                    </div>
                </div>
            </div>

            {{-- Transfer Route Header --}}
            <div class="p-4 border-bottom bg-light">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold mb-1" style="font-size: 11px;">
                            <i class="fa-solid fa-car me-1"></i> Private Chauffeur Transfer
                        </span>
                        <h4 class="fw-bold text-dark mb-1">
                            {{ $booking->pickup_location }} <i class="fa-solid fa-arrow-right mx-2 text-primary fs-6"></i> {{ $booking->dropoff_location }}
                        </h4>
                        <small class="text-secondary">
                            Pickup Date &amp; Time: <strong class="text-dark">{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y, h:i A') }}</strong>
                        </small>
                    </div>
                    @if(!empty($booking->flight_number))
                    <div class="text-md-end p-2 px-3 rounded-3 bg-white border">
                        <small class="text-muted d-block" style="font-size: 11px;">Flight Tracking</small>
                        <strong class="text-primary font-monospace">{{ $booking->flight_number }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Passenger and Ride Specifications --}}
            <div class="p-4 border-bottom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Lead Passenger</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13.5px;">
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Passenger Name:</span>
                                <strong class="text-dark">{{ $booking->passenger_name }}</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Contact Phone:</span>
                                <strong class="text-dark">{{ $booking->passenger_phone }}</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Email Address:</span>
                                <strong class="text-dark">{{ $booking->passenger_email }}</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 border-start-md ps-md-4">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Service Inclusions</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13px;">
                            <li class="d-flex align-items-center gap-2 text-dark">
                                <i class="fa-solid fa-check text-success"></i> <span>Free 60 mins flight delay waiting time</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 text-dark">
                                <i class="fa-solid fa-check text-success"></i> <span>Meet &amp; Greet with Name Sign at Arrivals Gate</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 text-dark">
                                <i class="fa-solid fa-check text-success"></i> <span>Air-conditioned Premium Sedan/Microbus</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 text-dark">
                                <i class="fa-solid fa-check text-success"></i> <span>All Toll, Fuel &amp; Parking fees included</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Price Summary --}}
            <div class="p-4 border-bottom bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block text-dark" style="font-size: 14px;">Total Fixed Fare (All Inclusive)</strong>
                        <small class="text-muted" style="font-size: 12px;">No surge pricing • Door-to-door guaranteed transfer</small>
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold text-primary mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{ CurrencyService::format($booking->total_amount) }}
                        </h3>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size: 11px;">PAID / CONFIRMED</span>
                    </div>
                </div>
            </div>

            {{-- Footer Notes --}}
            <div class="p-4 bg-dark text-white d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 12px;">
                <span class="text-white-50">Driver will contact you via WhatsApp / Phone 30 minutes prior to pickup.</span>
                <span class="text-white-50">24/7 Airport Support: <strong>+880 9610-000000</strong></span>
            </div>
        </div>

    </div>
</div>
@endsection
