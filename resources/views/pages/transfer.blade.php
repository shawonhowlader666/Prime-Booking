@extends('layouts.main', ['activePage' => 'transfer'])

@section('title', 'Airport Transfer & Taxi Rides | Prime Aviation')

@section('content')
{{-- Hero Subheader --}}
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%); padding: 20px 0; color: #fff; position: relative; overflow: hidden; border-bottom: 3px solid #3b82f6;">
    <div style="position: absolute; top: -30px; right: 15%; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.25); filter: blur(35px); border-radius: 50%; pointer-events: none;"></div>

    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px; position: relative; z-index: 2;" class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1" style="font-size: 22px; color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.3px;">
                <i class="fa-solid fa-taxi text-warning me-2" style="font-size: 20px;"></i> {{ __('Airport Transfer & Private Taxis') }}
            </h2>
            <p class="mb-0" style="font-size: 13.5px; color: #e2e8f0 !important; font-weight: 500; opacity: 0.95;">
                {{ __('Pre-book reliable airport pickups, private sedan transfers, and shuttle rides at flat rates.') }}
            </p>
        </div>

        <div class="d-none d-md-flex align-items-center gap-3">
            <div style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);" class="d-flex align-items-center gap-2">
                <span style="font-size: 26px;">🚕</span>
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Flat Rate Pickups</div>
                    <div style="font-size: 12px; font-weight: 800; color: #fff;">Free Flight Tracking</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-4" style="background-color: #f4f6fa; min-height: 80vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <h4 class="fw-bold text-dark mb-3" style="font-size: 19px;">Available Airport Taxi Routes &amp; Transfers</h4>

        <div class="row g-3 mb-4">
            @forelse($transfers ?? [] as $t)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 rounded-4 shadow-sm p-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-primary-subtle text-primary fw-bold">{{ $t->vehicle_type }}</span>
                            <strong class="text-success fs-5">BDT {{ number_format($t->price, 0) }}</strong>
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-plane-arrival text-primary me-1"></i> {{ $t->pickup_location }}</h6>
                        <p class="text-secondary small mb-3"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $t->dropoff_location }}</p>
                        
                        <div class="d-flex align-items-center gap-3 small text-secondary mb-3">
                            <span><i class="fa-solid fa-user-group text-muted me-1"></i> {{ $t->capacity }} Passengers</span>
                            <span><i class="fa-solid fa-suitcase text-muted me-1"></i> {{ $t->luggage_capacity }} Bags</span>
                        </div>

                        <button class="btn btn-primary w-100 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#bookTransferModal{{ $t->id }}">
                            Book This Transfer <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal --}}
                <div class="modal fade" id="bookTransferModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('transfer.book') }}" method="POST">
                                @csrf
                                <input type="hidden" name="transfer_id" value="{{ $t->id }}">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Reserve Airport Taxi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <strong class="d-block text-dark">{{ $t->pickup_location }} → {{ $t->dropoff_location }}</strong>
                                        <small class="text-secondary">{{ $t->vehicle_type }} | Flat Rate: <strong>BDT {{ number_format($t->price) }}</strong></small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Passenger Name</label>
                                        <input type="text" name="passenger_name" class="form-control" value="{{ auth()->user()?->name }}" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Mobile Phone</label>
                                            <input type="text" name="passenger_phone" class="form-control" value="{{ auth()->user()?->phone }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Email Address</label>
                                            <input type="email" name="passenger_email" class="form-control" value="{{ auth()->user()?->email }}" required>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Pickup Date &amp; Time</label>
                                            <input type="datetime-local" name="pickup_datetime" class="form-control" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Flight Number</label>
                                            <input type="text" name="flight_number" class="form-control" placeholder="e.g. BS-201 / BG-028">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Passengers Count</label>
                                        <input type="number" name="passengers" class="form-control" min="1" max="{{ $t->capacity }}" value="1" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary fw-bold">Confirm Transfer Reservation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 border">
                    <p class="text-secondary mb-0">No active transfer routes currently available.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
