@extends('layouts.main', ['activePage' => 'bookings'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'My Bookings & Trips | Prime Booking')
@section('meta_description', 'View and manage your hotel reservations, e-tickets, and travel history.')

@section('content')
<style>
.my-bookings-page { background: #f8fafc; min-height: 100vh; padding: 40px 16px; }
.booking-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin-bottom: 16px; transition: box-shadow 0.2s, transform 0.1s; }
.booking-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.08); transform: translateY(-1px); }
.status-badge-confirmed { background: #dcfce7; color: #16a34a; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 50px; }
.status-badge-pending   { background: #fef9c3; color: #ca8a04; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 50px; }
</style>

<div class="my-bookings-page">
    <div style="max-width: 1000px; margin: 0 auto;">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="fw-bold text-dark mb-1" style="font-size: 24px;">My Bookings &amp; Trips</h1>
                <p class="text-secondary mb-0" style="font-size: 13px;">Manage your hotel reservations, download e-ticket vouchers, or view booking history.</p>
            </div>
            <a href="{{ route('search.index') }}" class="btn btn-primary fw-bold btn-sm px-3 rounded-pill">
                <i class="fa-solid fa-plus me-1"></i> Book New Trip
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4" style="font-size:13px;">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Bookings List --}}
        <div class="d-flex flex-column gap-3">
            @forelse($bookings as $booking)
            <div class="booking-card p-3 p-md-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark" style="font-family:monospace; font-size:14px; background:#f1f5f9; padding:4px 10px; border-radius:6px;">
                            {{ $booking->booking_reference }}
                        </span>
                        <span class="{{ strtolower($booking->effective_status) === 'confirmed' ? 'status-badge-confirmed' : 'status-badge-pending' }}">
                            {{ ucfirst($booking->effective_status) }}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size:12px;">
                        Booked on {{ $booking->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>

                <div class="row g-3 align-items-center">
                    <div class="col-md-7 d-flex gap-3 align-items-center">
                        <img src="{{ $booking->property?->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80' }}"
                             alt="{{ $booking->property?->name }}"
                             style="width:90px; height:68px; object-fit:cover; border-radius:10px; flex-shrink:0;">
                        <div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size:15px;">{{ $booking->property?->name ?? 'Hotel Stay' }}</h6>
                            <div class="text-muted" style="font-size:12px;"><i class="fa-solid fa-location-dot text-danger me-1"></i>{{ $booking->property?->city }}, Bangladesh</div>
                            <div class="mt-1" style="font-size:12px; color:#475569;">
                                <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }} → {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                ({{ $booking->nights_count }} night{{ $booking->nights_count > 1 ? 's' : '' }})
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 text-md-end">
                        <div class="text-muted" style="font-size:11px;">Total Paid</div>
                        <div class="fw-bold text-primary mb-2" style="font-size:20px;">
                            {{ CurrencyService::format($booking->amount) }}
                        </div>

                        <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                            <a href="{{ route('booking.voucher', $booking->booking_reference) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold px-3" style="font-size:12px;">
                                <i class="fa-solid fa-ticket me-1"></i> View Voucher
                            </a>
                            <a href="{{ route('booking.voucher.download', $booking->booking_reference) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill fw-semibold px-3" style="font-size:12px;">
                                <i class="fa-solid fa-print me-1"></i> Print E-Ticket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-4 p-5 text-center shadow-xs border">
                <i class="fa-solid fa-suitcase-rolling text-muted mb-3" style="font-size:48px; color:#cbd5e1 !important;"></i>
                <h5 class="fw-bold text-dark mb-1">No Bookings Found</h5>
                <p class="text-secondary small mb-3">You don't have any hotel reservations yet. Start exploring properties now!</p>
                <a href="{{ route('search.index') }}" class="btn btn-primary fw-bold px-4 rounded-pill">
                    Explore Hotels &amp; Resorts →
                </a>
            </div>
            @endforelse
        </div>

        @if(method_exists($bookings, 'links'))
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
