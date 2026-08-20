@extends('layouts.main', ['activePage' => 'packages'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'Tour Package Booking Voucher #' . $booking['reference'] . ' | Prime Booking')

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
        
        {{-- Success Flash Alert & Print Buttons --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 no-print">
            <div>
                <a href="{{ route('packages.index') }}" class="text-decoration-none text-secondary fw-semibold small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Tour Packages
                </a>
                <h4 class="fw-bold text-dark mb-0 mt-1">Tour Booking Voucher</h4>
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
            <div class="p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #2067e1 100%);">
                <div>
                    <h3 class="fw-bold mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">PRIME BOOKING</h3>
                    <small class="text-white-50" style="letter-spacing: 1px; font-size: 11px;">OFFICIAL TOUR VOUCHER &amp; ITINERARY</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold" style="font-size: 12px;">
                        <i class="fa-solid fa-check-circle me-1"></i> {{ $booking['status'] ?? 'CONFIRMED' }}
                    </span>
                    <div class="mt-1 font-monospace text-white-50 small" style="font-size: 12px;">
                        Ref: <strong class="text-white">{{ $booking['reference'] }}</strong>
                    </div>
                </div>
            </div>

            {{-- Package Summary --}}
            <div class="p-4 border-bottom bg-light">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <img src="{{ $booking['package']->featured_image ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400' }}" class="w-100 rounded-3 shadow-xs" style="height: 95px; object-fit: cover;" alt="Tour Thumb">
                    </div>
                    <div class="col-md-9">
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold mb-1" style="font-size: 11px;">
                            {{ $booking['package']->destination ?? 'Bangladesh' }}
                        </span>
                        <h5 class="fw-bold text-dark mb-1">{{ $booking['package']->title ?? 'Scenic Tour Package' }}</h5>
                        <p class="text-secondary small mb-0">
                            <i class="fa-regular fa-clock me-1 text-warning"></i> {{ $booking['package']->duration_days ?? 3 }} Days / {{ $booking['package']->duration_nights ?? 2 }} Nights
                            • Operator: <strong>{{ $booking['package']->vendor?->name ?? 'Prime Verified Partner' }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Trip & Guest Details Table --}}
            <div class="p-4 border-bottom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Trip Specifications</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13.5px;">
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Departure Date:</span>
                                <strong class="text-dark">{{ \Carbon\Carbon::parse($booking['travel_date'])->format('d M Y (l)') }}</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Number of Travelers:</span>
                                <strong class="text-dark">{{ $booking['guests'] }} Person(s)</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Meeting Point:</span>
                                <strong class="text-dark">{{ $booking['package']->destination ?? 'Dhaka City Center' }}</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 border-start-md ps-md-4">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Lead Traveler Contact</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13.5px;">
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Full Name:</span>
                                <strong class="text-dark">{{ $booking['guest_name'] }}</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Email:</span>
                                <strong class="text-dark">{{ $booking['guest_email'] }}</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-secondary">Phone:</span>
                                <strong class="text-dark">{{ $booking['guest_phone'] }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Price Breakdown --}}
            <div class="p-4 border-bottom bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block text-dark" style="font-size: 14px;">Total Package Amount (All Taxes &amp; Fees Included)</strong>
                        <small class="text-muted" style="font-size: 12px;">{{ CurrencyService::format($booking['package']->price_per_person ?? 12500) }} × {{ $booking['guests'] }} Person(s)</small>
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold text-primary mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{ CurrencyService::format($booking['total_amount']) }}
                        </h3>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size: 11px;">PAID / CONFIRMED</span>
                    </div>
                </div>
            </div>

            {{-- Footer Notes --}}
            <div class="p-4 bg-dark text-white d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 12px;">
                <span class="text-white-50">Please present this voucher or show on your phone upon tour departure.</span>
                <span class="text-white-50">24/7 Helpline: <strong>+880 9610-000000</strong></span>
            </div>
        </div>

    </div>
</div>
@endsection
