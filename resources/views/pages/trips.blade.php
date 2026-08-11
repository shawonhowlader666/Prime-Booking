@extends('layouts.main', ['activePage' => 'trips'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'My Trips | Prime Booking')

@section('content')
@php
    $userBookings = auth()->check() 
        ? \App\Models\Booking::where('user_id', auth()->id())->with('property')->latest()->get()
        : collect();
@endphp

{{-- Hero Subheader --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 20px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-calendar-check text-warning me-2" style="font-size: 20px;"></i> {{ __('My Trips & Travel Itineraries') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('View past itineraries, upcoming trips, and saved hotel reservations.') }}
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 26px;">🧳</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">My Trips</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">{{ $userBookings->count() }} Reservation{{ $userBookings->count() === 1 ? '' : 's' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-5" style="min-height: 75vh; background-color: #f8fafc;">
    <div style="max-width: 960px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="fw-bold mb-0" style="font-size: 28px; color: #1e293b; letter-spacing: -0.5px;">
                {{ __('My Trips') }}
            </h1>
            <a href="{{ route('booking.history') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-suitcase me-1"></i> {{ __('All Bookings Ledger') }}
            </a>
        </div>

        @if($userBookings->isNotEmpty())
            <div class="d-flex flex-column gap-3">
                @foreach($userBookings as $b)
                <div class="bg-white border rounded-4 p-4 shadow-sm position-relative">
                    <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom pb-3 mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary fw-bold" style="font-family:monospace; font-size:13px;">
                                {{ $b->booking_reference }}
                            </span>
                            <span class="badge bg-success-subtle text-success fw-bold">
                                {{ ucfirst($b->effective_status) }}
                            </span>
                        </div>
                        <small class="text-secondary">Booked on {{ $b->created_at->format('d M Y') }}</small>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-7 d-flex gap-3 align-items-center">
                            <img src="{{ $b->property?->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80' }}"
                                 alt="{{ $b->property?->name }}" style="width: 84px; height: 64px; object-fit: cover; border-radius: 10px;">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size:15px;">{{ $b->property?->name ?? 'Hotel Stay' }}</h6>
                                <p class="text-secondary small mb-1"><i class="fa-solid fa-location-dot text-danger me-1"></i>{{ $b->property?->city }}, Bangladesh</p>
                                <div class="small text-primary fw-semibold">
                                    <i class="fa-regular fa-calendar-days me-1"></i> {{ \Carbon\Carbon::parse($b->check_in)->format('d M') }} → {{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 text-md-end">
                            <div class="fw-bold text-dark fs-5 mb-2">{{ CurrencyService::format($b->amount) }}</div>
                            <div class="d-flex gap-2 justify-content-md-end">
                                <a href="{{ route('booking.voucher', $b->booking_reference) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
                                    Voucher
                                </a>
                                <a href="{{ route('booking.voucher.download', $b->booking_reference) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill fw-bold">
                                    Print E-Ticket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State Illustration & Message -->
            <div class="text-center py-5 bg-white rounded-4 border p-4">
                <div class="mb-4 d-inline-block position-relative">
                    <div style="font-size: 70px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                        🧳✈️🌍
                    </div>
                </div>

                <h3 class="fw-bold mb-2" style="font-size: 22px; color: #1e293b;">
                    {{ __('No bookings, no trips!') }}
                </h3>
                <p class="text-secondary mb-4" style="font-size: 14px; max-width: 440px; margin: 0 auto;">
                    {{ __('Once you make any hotel booking, we\'ll automatically build your trip itinerary here so you can manage your stay.') }}
                </p>

                <a href="{{ route('search.index') }}" class="btn text-white fw-bold px-5 py-2" style="background-color: #2067e1; border-radius: 999px; font-size: 15px; box-shadow: 0 4px 14px rgba(32, 103, 225, 0.3);">
                    {{ __('Explore Hotels & Resorts →') }}
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
