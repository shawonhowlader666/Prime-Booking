@extends('layouts.main')

@section('title', 'Domestic Flight Booking | PRIME BOOKING')

@section('content')
<div class="py-4 bg-light" style="min-height: 85vh;">
    <div class="container" style="max-width: 1200px;">
        
        {{-- Flight Search Bar Header --}}
        <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-plane-departure text-primary me-2"></i> Book Domestic Flights in Bangladesh</h4>
            
            <form action="{{ route('flights.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">From (Origin)</label>
                    <select name="origin" class="form-select rounded-3 font-semibold" style="height: 46px;">
                        <option value="DAC" {{ request('origin', 'DAC') == 'DAC' ? 'selected' : '' }}>Dhaka (DAC) — Hazrat Shahjalal</option>
                        <option value="CXB" {{ request('origin') == 'CXB' ? 'selected' : '' }}>Cox's Bazar (CXB)</option>
                        <option value="ZYL" {{ request('origin') == 'ZYL' ? 'selected' : '' }}>Sylhet (ZYL) — Osmani Intl</option>
                        <option value="CGP" {{ request('origin') == 'CGP' ? 'selected' : '' }}>Chittagong (CGP) — Shah Amanat</option>
                        <option value="SPD" {{ request('origin') == 'SPD' ? 'selected' : '' }}>Saidpur (SPD)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">To (Destination)</label>
                    <select name="destination" class="form-select rounded-3 font-semibold" style="height: 46px;">
                        <option value="CXB" {{ request('destination', 'CXB') == 'CXB' ? 'selected' : '' }}>Cox's Bazar (CXB)</option>
                        <option value="DAC" {{ request('destination') == 'DAC' ? 'selected' : '' }}>Dhaka (DAC)</option>
                        <option value="ZYL" {{ request('destination') == 'ZYL' ? 'selected' : '' }}>Sylhet (ZYL)</option>
                        <option value="CGP" {{ request('destination') == 'CGP' ? 'selected' : '' }}>Chittagong (CGP)</option>
                        <option value="SPD" {{ request('destination') == 'SPD' ? 'selected' : '' }}>Saidpur (SPD)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">Departure Date</label>
                    <input type="date" name="date" class="form-control rounded-3" value="{{ $date }}" style="height: 46px;">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn text-white fw-bold rounded-3 w-100 py-2.5" style="background-color: #2067e1; height: 46px;">
                        <i class="fa-solid fa-magnifying-glass me-2"></i> SEARCH FLIGHTS
                    </button>
                </div>
            </form>
        </div>

        {{-- Route Info & Direct Flight Cards --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">
                Direct Flights: {{ $originInfo['city'] }} ({{ $originInfo['code'] }}) → {{ $destInfo['city'] }} ({{ $destInfo['code'] }})
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">{{ count($flights) }} Daily Flights Available</span>
        </div>

        <div class="row g-3">
            @foreach($flights as $f)
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm p-3.5 bg-white hover-shadow transition">
                    <div class="row align-items-center">
                        {{-- Airline info --}}
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 44px; height: 44px; background: #f0f5fc; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: {{ $f['airline']['color'] }}; font-size: 16px;">
                                    {{ $f['airline']['code'] }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 14.5px;">{{ $f['airline']['name'] }}</div>
                                    <div class="text-secondary small">{{ $f['flight_number'] }} · Direct</div>
                                </div>
                            </div>
                        </div>

                        {{-- Flight Times & Duration --}}
                        <div class="col-md-4 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <div>
                                    <div class="fw-bold text-dark fs-5">{{ $f['departure_time'] }}</div>
                                    <small class="text-secondary d-block">{{ $originInfo['code'] }}</small>
                                </div>

                                <div class="text-secondary small px-2">
                                    <div>{{ $f['duration'] }}</div>
                                    <div style="border-top: 2px dashed #cbd5e1; width: 60px; margin: 4px auto;"></div>
                                    <div class="text-success fw-semibold" style="font-size: 10.5px;">Direct Flight</div>
                                </div>

                                <div>
                                    <div class="fw-bold text-dark fs-5">{{ $f['arrival_time'] }}</div>
                                    <small class="text-secondary d-block">{{ $destInfo['code'] }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Baggage & Seats --}}
                        <div class="col-md-2 text-center">
                            <small class="text-dark fw-semibold d-block"><i class="fa-solid fa-suitcase me-1 text-primary"></i> {{ $f['baggage'] }}</small>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 mt-1 font-semibold" style="font-size: 10.5px;">Only {{ $f['seats_left'] }} seats left!</span>
                        </div>

                        {{-- Price & Book Button --}}
                        <div class="col-md-3 text-end">
                            <div class="fw-bold text-dark mb-1" style="font-size: 20px;">
                                {{ \App\Services\CurrencyService::format($f['price']) }}
                            </div>
                            <small class="text-secondary d-block mb-2" style="font-size: 11px;">per adult traveler</small>
                            
                            <button class="btn btn-primary btn-sm fw-bold rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#flightModal_{{ $f['id'] }}">
                                SELECT FLIGHT
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flight Booking Modal --}}
            <div class="modal fade" id="flightModal_{{ $f['id'] }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg p-3">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-ticket text-primary me-2"></i> Book Ticket — {{ $f['flight_number'] }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('flights.book') }}" method="POST">
                                @csrf
                                <input type="hidden" name="flight_number" value="{{ $f['flight_number'] }}">
                                <input type="hidden" name="airline_name" value="{{ $f['airline']['name'] }}">
                                <input type="hidden" name="origin" value="{{ $originInfo['city'] }} ({{ $originInfo['code'] }})">
                                <input type="hidden" name="destination" value="{{ $destInfo['city'] }} ({{ $destInfo['code'] }})">
                                <input type="hidden" name="departure_time" value="{{ $f['departure_time'] }}">
                                <input type="hidden" name="flight_date" value="{{ $date }}">
                                <input type="hidden" name="amount" value="{{ $f['price'] }}">

                                <div class="p-3 bg-light rounded-3 mb-3 small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Route:</span> <strong>{{ $originInfo['city'] }} → {{ $destInfo['city'] }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Date &amp; Time:</span> <strong>{{ $date }} at {{ $f['departure_time'] }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Ticket Fare:</span> <strong class="text-primary fs-6">{{ \App\Services\CurrencyService::format($f['price']) }}</strong>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Passenger Full Name</label>
                                    <input type="text" name="passenger_name" class="form-control rounded-3" value="{{ auth()->user()->name ?? '' }}" placeholder="e.g. Tanvir Ahmed" required style="height: 44px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Mobile Phone Number</label>
                                    <input type="text" name="passenger_phone" class="form-control rounded-3" value="+880 " placeholder="+880 1700-000000" required style="height: 44px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Email Address</label>
                                    <input type="email" name="passenger_email" class="form-control rounded-3" value="{{ auth()->user()->email ?? '' }}" placeholder="name@example.com" required style="height: 44px;">
                                </div>

                                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 py-2.5">
                                    CONFIRM &amp; ISSUE E-TICKET VOUCHER
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

