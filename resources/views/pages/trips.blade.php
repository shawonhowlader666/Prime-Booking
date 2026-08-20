@extends('layouts.main', ['activePage' => 'trips'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'My Trips | Prime Booking')
@section('meta_description', 'View and manage your travel bookings, itineraries, hotel reservations and trip details.')

@section('content')
@php
    $userBookings = auth()->check() 
        ? \App\Models\Booking::where('user_id', auth()->id())->with('property')->latest()->get()
        : collect();
@endphp

<style>
/* Agoda 1:1 My Trips Styling */
.trips-main-wrapper {
    background-color: #f7f9fa;
    min-height: 88vh;
    padding: 36px 0 70px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.trips-container {
    max-width: 940px;
    margin: 0 auto;
    padding: 0 16px;
}
/* All Bookings pill bar with 3D hover */
.all-bookings-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 12px 22px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
    color: #1e293b;
    margin-bottom: 48px;
    transition: all 0.2s ease;
}
.all-bookings-pill:hover {
    box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.1);
    border-color: #cbd5e1;
    transform: translateY(-1.5px);
    color: #2067e1;
}

/* Trip Card 3D hover */
.trip-item-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.trip-item-card:hover {
    transform: translateY(-2.5px);
    box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.12), 0 4px 10px -2px rgba(15, 23, 42, 0.06);
    border-color: #cbd5e1;
}
</style>

<div class="trips-main-wrapper">
    <div class="trips-container">
        
        {{-- Exact 1:1 Agoda Heading --}}
        <h1 class="fw-bold mb-4" style="font-size: 32px; color: #1e293b; letter-spacing: -0.6px;">
            My trips
        </h1>

        {{-- Exact Agoda Top Pill Bar: "🧳 All bookings >" --}}
        <a href="{{ route('booking.history') }}" class="all-bookings-pill">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-suitcase text-secondary" style="font-size: 15px;"></i>
                <span class="fw-semibold" style="font-size: 14.5px;">All bookings</span>
            </div>
            <i class="fa-solid fa-chevron-right text-secondary" style="font-size: 13px;"></i>
        </a>

        @if($userBookings->isNotEmpty())
            {{-- List of Active Trips --}}
            <div class="d-flex flex-column gap-3">
                @foreach($userBookings as $b)
                <div class="trip-item-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom pb-3 mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary fw-bold" style="font-family: monospace; font-size: 13px;">
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
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $b->property?->name ?? 'Hotel Stay' }}</h6>
                                <p class="text-secondary small mb-1"><i class="fa-solid fa-location-dot text-danger me-1"></i>{{ $b->property?->city ?? 'Bangladesh' }}</p>
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
            {{-- ── 1:1 AGODA AUTHENTIC EMPTY STATE (Exact Match with Screenshot) ── --}}
            <div class="text-center py-4">
                
                {{-- Vector Art: Floating Heart, Search Card, Luggage & Yellow Traveling Mascot --}}
                <div class="mb-4 d-inline-block">
                    <svg viewBox="0 0 280 200" style="width: 260px; height: 180px; overflow: visible;">
                        {{-- Background Card / Hotel Search Skeleton --}}
                        <g opacity="0.85">
                            {{-- Top Small Card --}}
                            <rect x="70" y="25" width="105" height="42" rx="8" fill="#e8f0fe" stroke="#d2e3fc" stroke-width="1.5" />
                            <rect x="80" y="38" width="40" height="6" rx="3" fill="#aecbfa" />
                            <rect x="80" y="50" width="60" height="5" rx="2.5" fill="#dadce0" />
                            
                            {{-- Floating Red Heart Notification Badge --}}
                            <g transform="translate(62, 16)">
                                <circle cx="12" cy="12" r="14" fill="#f87171" />
                                <path d="M 12 7 C 9 3, 5 7, 7 11 L 12 17 L 17 11 C 19 7, 15 3, 12 7 Z" fill="#ffffff" />
                            </g>

                            {{-- Bottom Larger Card --}}
                            <rect x="50" y="78" width="130" height="56" rx="8" fill="#f1f5f9" stroke="#e2e8f0" stroke-width="1.5" />
                            <rect x="62" y="92" width="50" height="8" rx="4" fill="#cbd5e1" />
                            <rect x="62" y="106" width="75" height="5" rx="2.5" fill="#e2e8f0" />
                        </g>

                        {{-- Dark Blue Rolling Suitcase --}}
                        <g transform="translate(95, 115)">
                            <rect x="0" y="0" width="36" height="46" rx="4" fill="#1e3a8a" />
                            <rect x="14" y="-8" width="8" height="8" rx="2" fill="#94a3b8" />
                            <line x1="6" y1="8" x2="30" y2="8" stroke="#3b82f6" stroke-width="1.5" />
                            <line x1="6" y1="36" x2="30" y2="36" stroke="#3b82f6" stroke-width="1.5" />
                            <circle cx="6" cy="48" r="3" fill="#334155" />
                            <circle cx="30" cy="48" r="3" fill="#334155" />
                        </g>

                        {{-- Purple Small Suitcase --}}
                        <g transform="translate(132, 102)">
                            <rect x="0" y="0" width="30" height="60" rx="4" fill="#6366f1" />
                            <path d="M 10 -12 L 20 -12 L 20 0 L 10 0 Z" fill="#475569" stroke="#334155" stroke-width="1" />
                            <line x1="6" y1="12" x2="24" y2="12" stroke="#818cf8" stroke-width="1.5" />
                            <line x1="6" y1="48" x2="24" y2="48" stroke="#818cf8" stroke-width="1.5" />
                            <circle cx="6" cy="62" r="3" fill="#334155" />
                            <circle cx="24" cy="62" r="3" fill="#334155" />
                        </g>

                        {{-- Tan Tote Bag on Ground --}}
                        <g transform="translate(106, 134)">
                            <path d="M 4 8 L 24 8 L 26 28 L 2 28 Z" fill="#d97706" />
                            <path d="M 9 8 Q 14 -2 19 8" fill="none" stroke="#b45309" stroke-width="2" />
                            <rect x="2" y="26" width="24" height="4" rx="2" fill="#92400e" />
                        </g>

                        {{-- Agoda Yellow Traveling Mascot --}}
                        <g transform="translate(162, 100)">
                            {{-- Legs --}}
                            <line x1="18" y1="52" x2="16" y2="68" stroke="#475569" stroke-width="3" stroke-linecap="round" />
                            <line x1="32" y1="52" x2="38" y2="68" stroke="#475569" stroke-width="3" stroke-linecap="round" />
                            <path d="M 12 68 L 18 68" stroke="#1e293b" stroke-width="4" stroke-linecap="round" />
                            <path d="M 36 68 L 44 68" stroke="#1e293b" stroke-width="4" stroke-linecap="round" />

                            {{-- Left Arm holding Purple Suitcase handle --}}
                            <path d="M 4 28 Q -10 24 -16 20" fill="none" stroke="#475569" stroke-width="3" stroke-linecap="round" />

                            {{-- Sun Hat on Back --}}
                            <ellipse cx="26" cy="12" rx="28" ry="10" fill="#fcd34d" transform="rotate(-15 26 12)" />
                            <ellipse cx="26" cy="10" rx="20" ry="7" fill="#f59e0b" transform="rotate(-15 26 10)" />
                            <path d="M 8 16 Q 26 22 44 14" stroke="#dc2626" stroke-width="2" fill="none" />

                            {{-- Yellow Mascot Body --}}
                            <circle cx="26" cy="30" r="24" fill="#fbbf24" stroke="#f59e0b" stroke-width="1.5" />

                            {{-- Cute Eyes & Smile --}}
                            <circle cx="20" cy="24" r="2.5" fill="#1e293b" />
                            <circle cx="32" cy="24" r="2.5" fill="#1e293b" />
                            <path d="M 23 32 Q 26 36 29 32" fill="none" stroke="#1e293b" stroke-width="2" stroke-linecap="round" />
                            {{-- Blushing Cheeks --}}
                            <circle cx="16" cy="28" r="3" fill="#f87171" opacity="0.6" />
                            <circle cx="36" cy="28" r="3" fill="#f87171" opacity="0.6" />

                            {{-- Right Arm holding Passport & Ticket --}}
                            <path d="M 46 30 Q 56 36 58 44" fill="none" stroke="#475569" stroke-width="3" stroke-linecap="round" />
                            {{-- Green Passport & Boarding Pass in Hand --}}
                            <g transform="translate(54, 40) rotate(15)">
                                <rect x="0" y="0" width="12" height="16" rx="2" fill="#059669" />
                                <rect x="2" y="2" width="8" height="3" fill="#34d399" />
                                <line x1="2" y1="8" x2="10" y2="8" stroke="#ffffff" stroke-width="1" />
                                <line x1="2" y1="11" x2="8" y2="11" stroke="#ffffff" stroke-width="1" />
                            </g>
                        </g>
                    </svg>
                </div>

                {{-- Exact Agoda Heading --}}
                <h3 class="fw-bold mb-2" style="font-size: 21px; color: #2d2d2d; letter-spacing: -0.2px;">
                    No bookings, no trips!
                </h3>

                {{-- Exact Agoda Subheading --}}
                <p class="text-secondary mb-4" style="font-size: 14.5px; color: #737373 !important; max-width: 540px; margin-left: auto; margin-right: auto; line-height: 1.5;">
                    Once you make any booking we'll create a trip here, so you can build and manage your itinerary.
                </p>

                {{-- Agoda Authentic Pill Button: "Explore" --}}
                <div>
                    <a href="{{ route('search.index') }}" class="btn text-white fw-bold shadow-sm"
                       style="background-color: #2067e1; border-radius: 28px; padding: 10px 42px; font-size: 15px; border: none; transition: transform 0.15s ease, background-color 0.15s ease;"
                       onmouseover="this.style.backgroundColor='#1a56db'; this.style.transform='translateY(-1px)';"
                       onmouseout="this.style.backgroundColor='#2067e1'; this.style.transform='translateY(0)';">
                        Explore
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
