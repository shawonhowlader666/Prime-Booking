@extends('layouts.main', ['activePage' => 'packages'])

@php use App\Services\CurrencyService; @endphp

@section('title', $package->title . ' | Prime Booking')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small text-secondary">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('packages.index') }}" class="text-decoration-none">Tour Packages</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $package->title }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- Left Main Details (68%) --}}
            <div class="col-lg-8">
                
                {{-- Package Header Card --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                    <div style="height: 380px; position: relative;">
                        <img src="{{ $package->featured_image }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $package->title }}">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-dark bg-opacity-75 text-white px-3 py-2 rounded-pill fw-bold" style="font-size: 12px;">
                                <i class="fa-regular fa-clock me-1 text-warning"></i> {{ $package->duration_days }} Days / {{ $package->duration_nights }} Nights
                            </span>
                            <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold" style="font-size: 12px;">
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $package->destination }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <h2 class="fw-bold text-dark mb-2" style="font-size: 26px; letter-spacing: -0.4px;">
                            {{ $package->title }}
                        </h2>
                        <p class="text-secondary mb-4" style="font-size: 14.5px;">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Instant Confirmation • Guaranteed Departure • Free Cancellation up to 72 hours before trip
                        </p>

                        {{-- Package Inclusions --}}
                        @if(!empty($package->inclusions))
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-3" style="font-size: 17px;">
                                <i class="fa-solid fa-circle-check text-success me-2"></i> What's Included
                            </h5>
                            <div class="row g-2">
                                @foreach($package->inclusions as $inc)
                                <div class="col-md-6">
                                    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2" style="font-size: 13.5px; font-weight: 500;">
                                        <i class="fa-solid fa-check text-success fs-6"></i>
                                        <span>{{ $inc }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Tour Highlights --}}
                        @if(!empty($package->highlights))
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-3" style="font-size: 17px;">
                                <i class="fa-solid fa-star text-warning me-2"></i> Tour Highlights
                            </h5>
                            <ul class="list-group list-group-flush border-0">
                                @foreach($package->highlights as $hl)
                                <li class="list-group-item px-0 border-0 d-flex align-items-start gap-2 bg-transparent" style="font-size: 14px;">
                                    <i class="fa-solid fa-angle-right text-primary mt-1"></i>
                                    <span>{{ $hl }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Day by Day Itinerary --}}
                        @if(!empty($package->itinerary))
                        <div>
                            <h5 class="fw-bold text-dark mb-3" style="font-size: 17px;">
                                <i class="fa-solid fa-route text-primary me-2"></i> Day-by-Day Itinerary
                            </h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($package->itinerary as $day)
                                <div class="border-start border-3 border-primary ps-3 py-1">
                                    <span class="badge bg-primary text-white fw-bold mb-1" style="font-size: 11px;">Day {{ $day['day'] ?? $loop->iteration }}</span>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $day['title'] }}</h6>
                                    <p class="text-secondary small mb-0">{{ $day['description'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </div>

            {{-- Right Sticky Booking Widget (32%) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-md rounded-4 p-4 bg-white sticky-top" style="top: 20px; z-index: 100;">
                    <div class="border-bottom pb-3 mb-3">
                        <small class="text-secondary d-block" style="font-size: 12px;">Starting from</small>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fw-bold text-primary display-6 mb-0" style="color: #2067e1 !important;">
                                {{ CurrencyService::format($package->price_per_person) }}
                            </span>
                            <small class="text-secondary">/ person</small>
                        </div>
                        @if($package->discount_price)
                        <span class="badge bg-danger-subtle text-danger fw-bold mt-1" style="font-size: 11px;">
                            Regular Price: {{ CurrencyService::format($package->discount_price) }}
                        </span>
                        @endif
                    </div>

                    <form action="{{ route('checkout.process') }}" method="POST" id="packageBookingForm">
                        @csrf
                        <input type="hidden" name="booking_type" value="package">
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <input type="hidden" name="amount" value="{{ $package->price_per_person }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Travel Date</label>
                            <input type="date" name="travel_date" class="form-control rounded-3" value="{{ date('Y-m-d', strtotime('+3 days')) }}" min="{{ date('Y-m-d') }}" required style="height: 44px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Number of Persons</label>
                            <select name="guests" class="form-select rounded-3" style="height: 44px;">
                                <option value="1">1 Person</option>
                                <option value="2" selected>2 Persons</option>
                                <option value="3">3 Persons</option>
                                <option value="4">4 Persons</option>
                                <option value="5">5+ Group Tour</option>
                            </select>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-4 border">
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Seats Available:</span>
                                <strong class="text-success">{{ $package->available_seats }} seats left</strong>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary">
                                <span>Partner Operator:</span>
                                <strong class="text-dark">{{ $package->vendor?->name ?? 'Prime Verified Partner' }}</strong>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white w-100 fw-bold py-3 rounded-3 shadow-sm" style="background-color: #2067e1; font-size: 15px; letter-spacing: 0.5px;">
                            BOOK PACKAGE NOW →
                        </button>
                    </form>

                    <div class="text-center mt-3 pt-3 border-top">
                        <small class="text-muted" style="font-size: 11.5px;">
                            <i class="fa-solid fa-lock text-success me-1"></i> Safe & Secure 256-bit Encrypted Checkout
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
