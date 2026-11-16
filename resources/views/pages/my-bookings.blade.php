@extends('layouts.main', ['activePage' => 'bookings'])

@php use App\Services\CurrencyService; @endphp

@section('title', 'My Bookings & Trips | Prime Booking')
@section('meta_description', 'View and manage your hotel reservations, tour package holidays, airport taxi rides, e-tickets, and travel history.')

@section('content')
<style>
.my-bookings-page { background: #f8fafc; min-height: 100vh; padding: 40px 16px; }
.booking-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin-bottom: 16px; transition: box-shadow 0.2s, transform 0.1s; }
.booking-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.08); transform: translateY(-1px); }
.status-badge-confirmed { background: #dcfce7; color: #16a34a; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 50px; }
.status-badge-pending   { background: #fef9c3; color: #ca8a04; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 50px; }
.status-badge-completed { background: #e0f2fe; color: #0284c7; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 50px; }
.status-badge-cancelled { background: #fee2e2; color: #dc2626; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 50px; }
.nav-pills .nav-link { color: #64748b; font-size: 13px; border: 1px solid transparent; }
.nav-pills .nav-link.active { background-color: var(--primary); color: #fff; }
</style>

<div class="my-bookings-page">
    <div style="max-width: 1000px; margin: 0 auto;">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h1 class="fw-bold text-dark mb-1" style="font-size: 24px;">My Bookings &amp; Trips</h1>
                <p class="text-secondary mb-0" style="font-size: 13px;">Manage your hotel reservations, tour holidays, airport transfers, and e-ticket vouchers.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('search.index') }}" class="btn btn-primary fw-bold btn-sm px-3 rounded-pill">
                    <i class="fa-solid fa-hotel me-1"></i> Hotels
                </a>
                <a href="{{ route('packages.index') }}" class="btn btn-outline-primary fw-bold btn-sm px-3 rounded-pill">
                    <i class="fa-solid fa-suitcase-rolling me-1"></i> Tour Packages
                </a>
                <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary fw-bold btn-sm px-3 rounded-pill">
                    <i class="fa-solid fa-taxi me-1"></i> Airport Taxi
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4" style="font-size:13px;">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-3 mb-4" style="font-size:13px;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Unified Navigation Tabs --}}
        <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-3 border" id="bookingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-3 py-2 rounded-pill" id="hotels-tab" data-bs-toggle="pill" data-bs-target="#hotelsTabPane" type="button" role="tab">
                    <i class="fa-solid fa-hotel me-1.5 text-primary"></i> Hotels &amp; Resorts
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1.5">{{ method_exists($bookings, 'total') ? $bookings->total() : count($bookings) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-pill" id="packages-tab" data-bs-toggle="pill" data-bs-target="#packagesTabPane" type="button" role="tab">
                    <i class="fa-solid fa-suitcase-rolling me-1.5 text-success"></i> Tour Packages
                    <span class="badge bg-success bg-opacity-10 text-success ms-1.5">{{ count($packageBookings ?? []) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 rounded-pill" id="transfers-tab" data-bs-toggle="pill" data-bs-target="#transfersTabPane" type="button" role="tab">
                    <i class="fa-solid fa-taxi me-1.5 text-warning"></i> Airport Transfers
                    <span class="badge bg-warning bg-opacity-10 text-dark ms-1.5">{{ count($transferBookings ?? []) }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="bookingsTabContent">
            {{-- TAB 1: HOTELS & RESORTS --}}
            <div class="tab-pane fade show active" id="hotelsTabPane" role="tabpanel">
                <div class="d-flex flex-column gap-3">
                    @forelse($bookings as $booking)
                    <div class="booking-card p-3 p-md-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" style="font-family:monospace; font-size:14px; background:#f1f5f9; padding:4px 10px; border-radius:6px;">
                                    {{ $booking->booking_reference }}
                                </span>
                                @php
                                    $effStatus = strtolower($booking->effective_status ?? $booking->status ?? 'confirmed');
                                    $bClass = match($effStatus) {
                                        'completed' => 'status-badge-completed',
                                        'cancelled' => 'status-badge-cancelled',
                                        'pending'   => 'status-badge-pending',
                                        default     => 'status-badge-confirmed',
                                    };
                                @endphp
                                <span class="{{ $bClass }}">
                                    {{ ucfirst($booking->effective_status ?? $booking->status) }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:12px;">
                                Booked on {{ $booking->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-7 d-flex gap-3 align-items-center">
                                <img src="{{ media_url($booking->property?->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80') }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80';"
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

                                <div class="d-flex gap-2 justify-content-md-end flex-wrap align-items-center">
                                    @if($booking->property)
                                    <a href="{{ route('booking.form', $booking->property->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-2.5" style="font-size:11.5px;" title="Book this property again">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Book Again
                                    </a>
                                    @endif
                                    <a href="{{ route('booking.voucher', $booking->booking_reference) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold px-3" style="font-size:12px;">
                                        <i class="fa-solid fa-ticket me-1"></i> Voucher
                                    </a>
                                    <a href="{{ route('booking.invoice', $booking->booking_reference) }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-2.5" style="font-size:12px;" title="Official Tax Invoice & Receipt">
                                        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Invoice
                                    </a>
                                    @if(in_array(strtolower($booking->effective_status ?? $booking->status), ['confirmed', 'pending']))
                                    <form action="{{ route('booking.cancel', $booking->booking_reference) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel booking #{{ $booking->booking_reference }}? Your reserved dates will be released.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-semibold px-2.5" style="font-size:11.5px;">
                                            <i class="fa-solid fa-ban me-1"></i> Cancel
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-4 p-5 text-center shadow-xs border">
                        <i class="fa-solid fa-hotel text-muted mb-3" style="font-size:48px; color:#cbd5e1 !important;"></i>
                        <h5 class="fw-bold text-dark mb-1">No Hotel Bookings Found</h5>
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

            {{-- TAB 2: TOUR PACKAGES --}}
            <div class="tab-pane fade" id="packagesTabPane" role="tabpanel">
                <div class="d-flex flex-column gap-3">
                    @forelse($packageBookings ?? [] as $pkg)
                    <div class="booking-card p-3 p-md-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" style="font-family:monospace; font-size:14px; background:#f1f5f9; padding:4px 10px; border-radius:6px;">
                                    #{{ $pkg->booking_reference }}
                                </span>
                                @php
                                    $pkgSt = strtolower($pkg->status ?? 'confirmed');
                                    $pkgBClass = match($pkgSt) {
                                        'completed' => 'status-badge-completed',
                                        'cancelled' => 'status-badge-cancelled',
                                        default     => 'status-badge-confirmed',
                                    };
                                @endphp
                                <span class="{{ $pkgBClass }}">
                                    {{ ucfirst($pkg->status) }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:12px;">
                                Reserved on {{ $pkg->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-7 d-flex gap-3 align-items-center">
                                <div style="width:72px; height:68px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0; border:1px solid #bbf7d0;">
                                    <i class="fa-solid fa-suitcase-rolling"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size:15px;">{{ $pkg->package?->title ?? 'Holiday Tour Package' }}</h6>
                                    <div class="text-muted" style="font-size:12px;"><i class="fa-solid fa-location-dot text-danger me-1"></i>{{ $pkg->package?->destination ?? 'Bangladesh' }}</div>
                                    <div class="mt-1" style="font-size:12px; color:#475569;">
                                        <i class="fa-regular fa-calendar me-1 text-success"></i>
                                        Travel Date: <strong>{{ $pkg->travel_date ? (is_string($pkg->travel_date) ? date('d M Y', strtotime($pkg->travel_date)) : $pkg->travel_date->format('d M Y')) : 'N/A' }}</strong>
                                        <span class="ms-2"><i class="fa-solid fa-user-group me-1 text-secondary"></i>{{ $pkg->guests }} Traveler{{ $pkg->guests > 1 ? 's' : '' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 text-md-end">
                                <div class="text-muted" style="font-size:11px;">Total Fare</div>
                                <div class="fw-bold text-success mb-2" style="font-size:20px;">
                                    BDT {{ number_format((float)$pkg->total_amount) }}
                                </div>

                                <div class="d-flex gap-2 justify-content-md-end flex-wrap align-items-center">
                                    @if($pkg->package)
                                    <a href="{{ route('packages.show', $pkg->package->slug) }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-2.5" style="font-size:11.5px;">
                                        <i class="fa-solid fa-eye me-1"></i> View Itinerary
                                    </a>
                                    @endif
                                    <a href="{{ route('packages.voucher', $pkg->booking_reference) }}" class="btn btn-outline-success btn-sm rounded-pill fw-semibold px-3" style="font-size:12px;">
                                        <i class="fa-solid fa-ticket me-1"></i> Package Voucher
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-4 p-5 text-center shadow-xs border">
                        <i class="fa-solid fa-suitcase-rolling text-muted mb-3" style="font-size:48px; color:#cbd5e1 !important;"></i>
                        <h5 class="fw-bold text-dark mb-1">No Tour Package Bookings Found</h5>
                        <p class="text-secondary small mb-3">You haven't reserved any holiday itineraries yet. Discover Bangladesh's top adventures!</p>
                        <a href="{{ route('packages.index') }}" class="btn btn-success fw-bold px-4 rounded-pill">
                            Explore Tour Packages →
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB 3: AIRPORT TRANSFERS --}}
            <div class="tab-pane fade" id="transfersTabPane" role="tabpanel">
                <div class="d-flex flex-column gap-3">
                    @forelse($transferBookings ?? [] as $tr)
                    <div class="booking-card p-3 p-md-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" style="font-family:monospace; font-size:14px; background:#f1f5f9; padding:4px 10px; border-radius:6px;">
                                    #{{ $tr->booking_reference }}
                                </span>
                                @php
                                    $trSt = strtolower($tr->status ?? 'confirmed');
                                    $trBClass = match($trSt) {
                                        'completed' => 'status-badge-completed',
                                        'cancelled' => 'status-badge-cancelled',
                                        default     => 'status-badge-confirmed',
                                    };
                                @endphp
                                <span class="{{ $trBClass }}">
                                    {{ ucfirst($tr->status) }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:12px;">
                                Booked on {{ $tr->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-7 d-flex gap-3 align-items-center">
                                <div style="width:72px; height:68px; border-radius:10px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0; border:1px solid #fde68a;">
                                    <i class="fa-solid fa-taxi"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size:15px;">
                                        <i class="fa-solid fa-plane-arrival text-info me-1"></i>{{ $tr->pickup_location }}
                                        <i class="fa-solid fa-arrow-right mx-1 text-muted" style="font-size:12px;"></i>
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i>{{ $tr->dropoff_location }}
                                    </h6>
                                    <div class="text-muted" style="font-size:12px;">
                                        <i class="fa-solid fa-car-side me-1 text-secondary"></i>{{ $tr->transfer?->vehicle_type ?? 'Sedan' }}
                                        @if($tr->flight_number)
                                            <span class="ms-2"><i class="fa-solid fa-plane me-1 text-primary"></i>Flight: {{ $tr->flight_number }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-1" style="font-size:12px; color:#475569;">
                                        <i class="fa-regular fa-clock me-1 text-warning"></i>
                                        Pickup: <strong>{{ $tr->pickup_datetime ? $tr->pickup_datetime->format('d M Y, h:i A') : 'N/A' }}</strong>
                                        <span class="ms-2"><i class="fa-solid fa-user-group me-1 text-secondary"></i>{{ $tr->passengers }} Pax</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 text-md-end">
                                <div class="text-muted" style="font-size:11px;">Standard Fare</div>
                                <div class="fw-bold text-dark mb-2" style="font-size:20px;">
                                    BDT {{ number_format((float)$tr->total_amount) }}
                                </div>

                                <div class="d-flex gap-2 justify-content-md-end flex-wrap align-items-center">
                                    <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-2.5" style="font-size:11.5px;">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Book Another Ride
                                    </a>
                                    <a href="{{ route('transfers.voucher', $tr->booking_reference) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold px-3" style="font-size:12px;">
                                        <i class="fa-solid fa-ticket me-1"></i> Transfer Voucher
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-4 p-5 text-center shadow-xs border">
                        <i class="fa-solid fa-taxi text-muted mb-3" style="font-size:48px; color:#cbd5e1 !important;"></i>
                        <h5 class="fw-bold text-dark mb-1">No Airport Taxi Rides Found</h5>
                        <p class="text-secondary small mb-3">You haven't scheduled any airport taxi transfers yet. Enjoy guaranteed on-time pickups!</p>
                        <a href="{{ route('transfers.index') }}" class="btn btn-warning fw-bold px-4 rounded-pill text-dark">
                            Schedule Airport Taxi →
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
