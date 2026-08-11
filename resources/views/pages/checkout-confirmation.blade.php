@extends('layouts.main', ['activePage' => 'home'])

@section('title', 'Booking Confirmed #' . $booking->booking_reference . ' | Prime Aviation')

@section('content')
<div class="py-5 bg-light" style="min-height: 80vh;">
    <div class="container" style="max-width: 960px;">

        {{-- Success Banner Box --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white text-center p-4 p-md-5" style="border-radius: 16px !important;">
            
            {{-- Animated Green Checkmark Icon --}}
            <div class="mx-auto mb-3 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fa-solid fa-circle-check" style="font-size: 46px; color: #10b981;"></i>
            </div>

            <h2 class="fw-bold text-dark mb-1" style="font-size: 26px; font-family: 'Plus Jakarta Sans', sans-serif;">
                Your Booking is Confirmed!
            </h2>
            <p class="text-secondary mb-3" style="font-size: 14px;">
                We have sent your official booking voucher &amp; invoice to <strong>{{ $booking->guest_email }}</strong>
            </p>

            {{-- Booking Reference Box --}}
            <div class="d-inline-flex align-items-center gap-3 bg-light border px-4 py-2 rounded-3 mx-auto mb-4">
                <span class="text-muted small">BOOKING ID:</span>
                <strong class="text-primary fs-5 font-monospace" style="letter-spacing: 1px;">{{ $booking->booking_reference }}</strong>
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="navigator.clipboard.writeText('{{ $booking->booking_reference }}'); alert('Booking ID Copied!');">
                    <i class="fa-regular fa-copy"></i>
                </button>
            </div>

            {{-- Voucher Action Buttons --}}
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                <button type="button" class="btn text-white fw-bold px-4 py-2 rounded-3" style="background-color: #2067e1;" onclick="window.print();">
                    <i class="fa-solid fa-file-pdf me-2"></i> DOWNLOAD PDF VOUCHER
                </button>
                <a href="{{ route('trips') }}" class="btn btn-outline-secondary fw-semibold px-4 py-2 rounded-3">
                    <i class="fa-solid fa-suitcase me-2"></i> VIEW IN MY TRIPS
                </a>
            </div>
        </div>

        {{-- Itinerary Details Card --}}
        <div class="card border border-gray-200 rounded-4 p-4 mb-4 bg-white shadow-sm" style="border-radius: 16px !important;">
            <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom" style="font-size: 18px;">Reservation Summary</h5>
            
            <div class="row g-4">
                {{-- Hotel Info --}}
                <div class="col-md-6 border-end">
                    <div class="d-flex gap-3 mb-3">
                        <img src="{{ $booking->property->primary_image ?? 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=300&q=80' }}" style="width: 90px; height: 90px; object-fit: cover; border-radius: 10px;" alt="{{ $booking->property->name }}">
                        <div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $booking->property->name }}</h6>
                            <div class="text-warning mb-1" style="font-size: 12px;">
                                @for($s=0; $s<($booking->property->star_rating ?? 5); $s++)★@endfor
                            </div>
                            <div class="small text-secondary"><i class="fa-solid fa-location-dot me-1"></i> {{ $booking->property->address }}</div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3" style="font-size: 13px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Primary Guest:</span>
                            <strong class="text-dark">{{ $booking->guest_name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Contact Phone:</span>
                            <strong class="text-dark">{{ $booking->guest_phone }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Payment Status:</span>
                            <span class="badge bg-success text-white fw-bold">PAID (bKash)</span>
                        </div>
                    </div>
                </div>

                {{-- Dates & Room --}}
                <div class="col-md-6">
                    <div class="row g-3 mb-3" style="font-size: 13px;">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 bg-light text-center">
                                <small class="text-muted d-block">CHECK-IN</small>
                                <strong class="text-dark fs-6 d-block">{{ date('D, M d, Y', strtotime($booking->check_in)) }}</strong>
                                <small class="text-primary fw-semibold">From 2:00 PM</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 bg-light text-center">
                                <small class="text-muted d-block">CHECK-OUT</small>
                                <strong class="text-dark fs-6 d-block">{{ date('D, M d, Y', strtotime($booking->check_out)) }}</strong>
                                <small class="text-primary fw-semibold">Until 12:00 PM</small>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border rounded-3 bg-white" style="font-size: 13px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Room Reserved:</span>
                            <strong class="text-dark">{{ $booking->room->name ?? 'Superior Sea View Room' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Guests:</span>
                            <strong class="text-dark">{{ $booking->adults }} Adult(s), {{ $booking->children }} Child</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top mt-2">
                            <strong class="text-dark">Total Amount Paid:</strong>
                            <strong class="text-primary fs-5" style="color: #2067e1 !important;">BDT {{ number_format($booking->total_amount, 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Help Card --}}
        <div class="card border-0 shadow-xs rounded-4 p-3 bg-white text-center" style="border-radius: 12px !important;">
            <div class="d-flex align-items-center justify-content-center gap-3 text-secondary" style="font-size: 13px;">
                <span><i class="fa-solid fa-headset text-primary me-1"></i> Need to modify your stay? Contact 24/7 Support: <strong>01770887733</strong></span>
                <span>|</span>
                <a href="{{ route('home') }}" class="text-decoration-none fw-semibold" style="color: #2067e1;">Return to Homepage</a>
            </div>
        </div>

    </div>
</div>
@endsection
