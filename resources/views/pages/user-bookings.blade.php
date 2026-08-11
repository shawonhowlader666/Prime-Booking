@extends('layouts.main', ['activePage' => 'bookings'])

@section('title', 'My Account & Booking History | Prime Aviation')

@section('content')
<div style="background-color: #f8fafc; min-height: 80vh; padding: 40px 16px;">
    <div style="max-width: 1140px; margin: 0 auto;">
        
        {{-- Header Section --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;"><i class="fa-solid fa-ticket-simple text-primary me-2"></i>My Bookings &amp; Trips</h2>
                <p class="text-secondary small mb-0">View active stay vouchers, payment receipts, and manage cancellations</p>
            </div>

            {{-- Reference Lookup Form --}}
            <form action="{{ route('account.bookings') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" name="reference" class="form-control form-control-sm rounded-3 px-3" placeholder="Enter Booking Ref (e.g. BK-9821)" value="{{ request('reference') }}" style="width: 220px; font-size: 13px;">
                <button type="submit" class="btn text-white btn-sm fw-bold px-3" style="background-color: #2067e1; border-radius: 8px; font-size: 12.5px;">Search</button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 p-3 mb-4 shadow-xs" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-4 shadow-xs" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex flex-column gap-3">
            @forelse($bookings as $b)
                @php
                    $prop = $b->property;
                    $statusBg = match($b->booking_status) {
                        'confirmed' => 'bg-success',
                        'pending'   => 'bg-warning text-dark',
                        'completed' => 'bg-info',
                        'cancelled' => 'bg-danger',
                        default     => 'bg-secondary',
                    };
                    $payBg = $b->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark';
                @endphp

                <div class="card border-0 shadow-xs rounded-4 overflow-hidden bg-white" style="border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-light bg-opacity-75 py-2.5 px-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-color: #f1f5f9 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark" style="font-size: 13px;">Ref #{{ $b->booking_reference }}</span>
                            <span class="badge {{ $statusBg }} fw-bold px-2 py-1" style="font-size: 10.5px; border-radius: 4px; text-transform: uppercase;">
                                {{ $b->booking_status }}
                            </span>
                            <span class="badge {{ $payBg }} fw-bold px-2 py-1" style="font-size: 10.5px; border-radius: 4px; text-transform: uppercase;">
                                Payment: {{ $b->payment_status }}
                            </span>
                        </div>
                        <small class="text-muted" style="font-size: 11.5px;">Booked on {{ $b->created_at->format('M d, Y') }}</small>
                    </div>

                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            {{-- Property Thumbnail --}}
                            <div class="col-md-3">
                                <img src="{{ $prop->primary_image ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=600&q=80' }}" class="rounded-3 w-100" style="height: 120px; object-fit: cover;" alt="{{ $prop->name ?? 'Stay' }}">
                            </div>

                            {{-- Stay Details --}}
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark mb-1">{{ $prop->name ?? 'Hotel Stay' }}</h6>
                                <p class="text-muted small mb-2" style="font-size: 12px;"><i class="fa-solid fa-location-dot text-primary me-1"></i> {{ $prop->city ?? ($prop->address ?? 'Bangladesh') }}</p>

                                <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded-3 text-secondary small" style="font-size: 12px;">
                                    <div>
                                        <span class="text-muted d-block" style="font-size: 10px;">Check-in</span>
                                        <strong><i class="fa-solid fa-calendar-check text-primary me-1"></i> {{ \Carbon\Carbon::parse($b->check_in)->format('D, M d, Y') }}</strong>
                                    </div>
                                    <div class="border-end pe-3"></div>
                                    <div>
                                        <span class="text-muted d-block" style="font-size: 10px;">Check-out</span>
                                        <strong><i class="fa-solid fa-calendar-xmark text-secondary me-1"></i> {{ \Carbon\Carbon::parse($b->check_out)->format('D, M d, Y') }}</strong>
                                    </div>
                                    <div class="border-end pe-3"></div>
                                    <div>
                                        <span class="text-muted d-block" style="font-size: 10px;">Guests</span>
                                        <strong><i class="fa-solid fa-user-group text-info me-1"></i> {{ $b->adults_count ?? 2 }} Adults, {{ $b->children_count ?? 0 }} Kids</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Price & Actions --}}
                            <div class="col-md-3 text-md-end border-start-md">
                                <small class="text-muted d-block" style="font-size: 10.5px;">Total Paid Amount</small>
                                <div class="fw-bold text-primary mb-2" style="font-size: 18px; color: #2067e1 !important;">
                                    {{ \App\Services\CurrencyService::format($b->total_price) }}
                                </div>

                                <div class="d-flex flex-column gap-1.5">
                                    <a href="{{ route('booking.voucher.download', $b->booking_reference) }}" target="_blank" class="btn btn-outline-primary btn-sm fw-bold w-100" style="font-size: 12px; border-radius: 6px;">
                                        <i class="fa-solid fa-file-pdf me-1"></i> E-Ticket Voucher
                                    </a>

                                    @if(!in_array($b->booking_status, ['cancelled', 'completed']))
                                    <button type="button" class="btn btn-outline-danger btn-sm fw-semibold w-100" style="font-size: 11.5px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#cancelBookingModal_{{ $b->id }}">
                                        Cancel Booking
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cancellation Modal --}}
                @if(!in_array($b->booking_status, ['cancelled', 'completed']))
                <div class="modal fade" id="cancelBookingModal_{{ $b->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header bg-danger text-white border-0 py-3">
                                <h6 class="modal-title fw-bold mb-0"><i class="fa-solid fa-triangle-exclamation me-1"></i> Cancel Booking Ref #{{ $b->booking_reference }}</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('user.bookings.cancel', $b->booking_reference) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <p class="text-dark small mb-3">Are you sure you want to cancel your stay at <strong>{{ $prop->name ?? 'Hotel' }}</strong>?</p>
                                    <label class="fw-bold small text-dark mb-1">Reason for cancellation (optional):</label>
                                    <textarea name="cancellation_reason" class="form-control form-control-sm rounded-3" rows="3" placeholder="Please let us know why you are cancelling..." style="font-size: 12.5px;"></textarea>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light btn-sm rounded-3 px-3 fw-semibold" data-bs-dismiss="modal">Keep Booking</button>
                                    <button type="submit" class="btn btn-danger btn-sm rounded-3 px-4 fw-bold">Confirm Cancellation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            @empty
                <div class="card border-0 shadow-xs rounded-4 p-5 text-center bg-white my-3">
                    <div class="mb-3 text-muted"><i class="fa-solid fa-ticket display-3 text-opacity-25"></i></div>
                    <h5 class="fw-bold text-dark mb-1">No bookings found</h5>
                    <p class="text-secondary small mb-3">You don't have any stay reservations yet. Explore top hotels and start planning!</p>
                    <div>
                        <a href="{{ route('search.index') }}" class="btn text-white fw-bold px-4 py-2" style="background-color: #2067e1; border-radius: 8px; font-size: 13.5px;">Explore Hotels &amp; Stays</a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($bookings->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $bookings->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
