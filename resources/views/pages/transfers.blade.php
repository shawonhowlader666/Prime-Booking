@extends('layouts.main')

@section('title', 'Airport Taxi & Chauffeur Transfer | PRIME BOOKING')

@section('content')
<div class="py-4 bg-light" style="min-height: 85vh;">
    <div class="container" style="max-width: 1140px;">
        
        {{-- Header Banner --}}
        <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 mb-4 text-white" style="background: linear-gradient(135deg, #0b2545 0%, #1d2b45 100%);">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-primary px-3 py-1.5 rounded-pill mb-2 font-semibold">24/7 Door-to-Door Airport Pickups</span>
                    <h2 class="fw-bold mb-2 display-6">Airport Transfer &amp; Private Taxi</h2>
                    <p class="text-white-50 mb-0">Hassle-free airport pick-up &amp; drop-off services across Hazrat Shahjalal (Dhaka), Cox's Bazar, and Sylhet with professional chauffeurs.</p>
                </div>
                <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                    <div class="d-inline-flex gap-3 bg-white text-dark p-3 rounded-4 shadow-sm">
                        <div class="text-center px-2 border-end">
                            <div class="fw-bold fs-5 text-primary">100%</div>
                            <small class="text-muted" style="font-size: 11px;">Flight Tracking</small>
                        </div>
                        <div class="text-center px-2 border-end">
                            <div class="fw-bold fs-5 text-success">Free</div>
                            <small class="text-muted" style="font-size: 11px;">60m Wait Time</small>
                        </div>
                        <div class="text-center px-2">
                            <div class="fw-bold fs-5 text-dark">Fixed</div>
                            <small class="text-muted" style="font-size: 11px;">No Extra Fees</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Route Cards --}}
        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-car me-2 text-primary"></i> Available Airport Taxi Routes</h5>
        
        <div class="row g-3">
            @foreach($transfers as $tr)
            <div class="col-md-4">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white hover-shadow transition">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 48px; height: 48px; background: #e0edff; color: #2067e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fa-solid fa-taxi"></i>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1" style="font-size: 11px;">{{ $tr->vehicle_type }}</span>
                            <div class="fw-bold text-dark mt-1" style="font-size: 15px;">{{ $tr->pickup_location }}</div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 small">
                        <div class="text-secondary mb-1"><i class="fa-solid fa-location-dot me-1 text-danger"></i> <strong>Dropoff:</strong> {{ $tr->dropoff_location }}</div>
                        <div class="text-secondary mb-1"><i class="fa-solid fa-users me-1 text-primary"></i> Max Passengers: <strong>{{ $tr->capacity }} Guests</strong></div>
                        <div class="text-secondary"><i class="fa-solid fa-suitcase me-1 text-secondary"></i> Luggage: <strong>{{ $tr->luggage_capacity }} Bags</strong></div>
                    </div>

                    <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Fixed All-Inclusive Rate</small>
                            <strong class="text-dark fs-5">{{ \App\Services\CurrencyService::format($tr->price) }}</strong>
                        </div>
                        <button class="btn btn-primary btn-sm fw-bold rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#transferModal_{{ $tr->id }}">
                            BOOK TAXI
                        </button>
                    </div>
                </div>
            </div>

            {{-- Transfer Booking Modal --}}
            <div class="modal fade" id="transferModal_{{ $tr->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg p-3">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-taxi text-primary me-2"></i> Book {{ $tr->vehicle_type }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('transfers.book') }}" method="POST">
                                @csrf
                                <input type="hidden" name="transfer_id" value="{{ $tr->id }}">

                                <div class="p-3 bg-light rounded-3 mb-3 small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Route:</span> <strong>{{ $tr->pickup_location }} → {{ $tr->dropoff_location }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Fare Total:</span> <strong class="text-primary fs-6">{{ \App\Services\CurrencyService::format($tr->price) }}</strong>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Passenger Full Name</label>
                                    <input type="text" name="passenger_name" class="form-control rounded-3" value="{{ auth()->user()->name ?? '' }}" placeholder="e.g. Tanvir Ahmed" required style="height: 44px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Contact Phone Number</label>
                                    <input type="text" name="passenger_phone" class="form-control rounded-3" value="+880 " required style="height: 44px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Email Address</label>
                                    <input type="email" name="passenger_email" class="form-control rounded-3" value="{{ auth()->user()->email ?? '' }}" required style="height: 44px;">
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-dark small">Pickup Date &amp; Time</label>
                                        <input type="datetime-local" name="pickup_datetime" class="form-control rounded-3" value="{{ now()->addHours(24)->format('Y-m-d\TH:i') }}" required style="height: 44px;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-dark small">Flight Number (Optional)</label>
                                        <input type="text" name="flight_number" class="form-control rounded-3" placeholder="e.g. BS-141" style="height: 44px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small">Number of Passengers</label>
                                    <input type="number" name="passengers" class="form-control rounded-3" value="2" min="1" max="{{ $tr->capacity }}" required style="height: 44px;">
                                </div>

                                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 py-2.5">
                                    CONFIRM TAXI RESERVATION
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

