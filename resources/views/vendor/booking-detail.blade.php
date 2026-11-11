@extends('layouts.vendor')
@section('title', 'Booking #' . ($booking->booking_reference ?? $booking->id) . ' | Vendor Portal')

@php use App\Services\CurrencyService; @endphp

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card mb-4" style="background:#fff; border:1px solid #e8e8e8; border-radius:4px; padding:20px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="page-title m-0 fw-bold fs-5 text-dark">
                <i class="fa-solid fa-receipt me-2 text-primary"></i> Reservation #{{ $booking->booking_reference ?? $booking->id }}
            </h1>
            <p class="text-muted mb-0 small mt-1">
                Detailed guest reservation and billing summary.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('vendor.bookings.index') }}" class="btn btn-sm btn-light border text-secondary fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius:4px; height:34px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Ledger
            </a>
            @php
                $phone = $booking->guest_phone ?? null;
                $phoneNum = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
                $waNum = $phoneNum ? (str_starts_with($phoneNum, '880') ? $phoneNum : '880' . ltrim($phoneNum, '0')) : null;
            @endphp
            @if($waNum)
                <a href="https://wa.me/{{ $waNum }}?text={{ urlencode('Hello ' . $booking->guest_name . ', contacting you from ' . ($booking->property?->name ?? 'Hotel Front Desk') . ' regarding your reservation #' . $booking->booking_reference . '.') }}" target="_blank" class="btn btn-sm btn-success fw-bold text-white d-inline-flex align-items-center gap-1.5" style="border-radius:4px; height:34px;">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>
            @endif
            <a href="{{ route('booking.voucher', $booking->booking_reference ?? $booking->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius:4px; height:34px;">
                <i class="fa-solid fa-ticket"></i> Voucher
            </a>
            <a href="{{ route('booking.invoice', $booking->booking_reference ?? $booking->id) }}" target="_blank" class="btn btn-sm btn-primary fw-bold text-white d-inline-flex align-items-center gap-1.5" style="border-radius:4px; height:34px; background:var(--primary); border:none;">
                <i class="fa-solid fa-file-invoice-dollar"></i> Invoice
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('vendor.bookings.index') }}">Bookings</a>
        <span class="sep">-</span><strong style="color:#333;">Ref: {{ $booking->booking_reference ?? 'PB-'.$booking->id }}</strong>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:4px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        {{-- Left Column: Booking & Guest Details --}}
        <div class="col-lg-8">
            {{-- Property & Stay Info --}}
            <div class="card p-3 shadow-sm border-0 mb-3 bg-white" style="border-radius:4px; border:1px solid #e8e8e8 !important;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size:14px;">
                    <i class="fa-solid fa-hotel text-primary me-2"></i> Accommodation Details
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase mb-1">Property Name</label>
                        <p class="fw-bold text-dark mb-0 fs-6">{{ optional($booking->property)->name ?? 'Hotel Stay' }}</p>
                        <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> {{ optional($booking->property)->city ?? 'Bangladesh' }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase mb-1">Room Category</label>
                        <p class="fw-bold text-dark mb-0 fs-6">{{ optional($booking->room)->name ?? 'Standard Category' }}</p>
                        <small class="text-muted"><i class="fa-solid fa-bed me-1"></i> {{ optional($booking->room)->bed_type ?? 'Standard Bed' }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase mb-1">Check-In Date</label>
                        <p class="fw-bold text-success mb-0" style="font-size:13.5px;">
                            <i class="fa-solid fa-calendar-check me-1"></i> {{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('D, M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase mb-1">Check-Out Date</label>
                        <p class="fw-bold text-danger mb-0" style="font-size:13.5px;">
                            <i class="fa-solid fa-calendar-xmark me-1"></i> {{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('D, M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase mb-1">Duration</label>
                        <p class="fw-semibold text-dark mb-0">{{ $booking->nights ?? ($booking->check_in && $booking->check_out ? \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) : 1) }} Night(s)</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase mb-1">Adults</label>
                        <p class="fw-semibold text-dark mb-0">{{ $booking->adults ?? 1 }} Adults</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase mb-1">Children</label>
                        <p class="fw-semibold text-dark mb-0">{{ $booking->children ?? 0 }} Kids</p>
                    </div>
                </div>
            </div>

            {{-- Guest Contact Information --}}
            <div class="card p-3 shadow-sm border-0 mb-3 bg-white" style="border-radius:4px; border:1px solid #e8e8e8 !important;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size:14px;">
                    <i class="fa-solid fa-user text-primary me-2"></i> Guest / Booker Contact
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase mb-1">Guest Full Name</label>
                        <p class="fw-bold text-dark mb-0">{{ $booking->guest_name }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase mb-1">Contact Phone</label>
                        <p class="fw-bold text-dark mb-0">
                            @if($booking->guest_phone)
                                <a href="tel:{{ $booking->guest_phone }}" class="text-dark text-decoration-none">
                                    <i class="fa-solid fa-phone text-primary me-1"></i> {{ $booking->guest_phone }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small text-uppercase mb-1">Email Address</label>
                        <p class="fw-semibold text-dark mb-0 text-truncate" title="{{ $booking->guest_email }}">
                            {{ $booking->guest_email ?? 'N/A' }}
                        </p>
                    </div>
                    @if($booking->special_requests)
                    <div class="col-12">
                        <label class="form-label text-muted small text-uppercase mb-1">Special Requests</label>
                        <div class="p-2.5 bg-light rounded text-dark" style="font-size:12.5px; border:1px solid #f1f5f9;">
                            {{ $booking->special_requests }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Financial & Payment Breakdown --}}
            <div class="card p-3 shadow-sm border-0 bg-white" style="border-radius:4px; border:1px solid #e8e8e8 !important;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size:14px;">
                    <i class="fa-solid fa-receipt text-primary me-2"></i> Payment &amp; Financial Breakdown
                </h6>
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase mb-1">Total Amount</label>
                        <div class="fs-4 fw-bold text-primary">
                            {{ CurrencyService::format($booking->amount ?? $booking->total_price ?? $booking->total_amount ?? 0) }}
                        </div>
                        <small class="text-muted">All inclusive of taxes &amp; service charges</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small text-uppercase mb-1">Payment Method &amp; Status</label>
                        <div>
                            <span class="badge bg-light text-dark border me-1 px-2.5 py-1.5 fw-semibold" style="font-size:11.5px; border-radius:4px;">
                                <i class="fa-solid fa-wallet text-secondary me-1"></i> {{ strtoupper(str_replace('_', ' ', $booking->payment_method ?? 'CASH')) }}
                            </span>
                            @if(strtolower($booking->payment_status ?? '') === 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-bold" style="border-radius:4px; font-size:11.5px;">
                                    <i class="fa-solid fa-circle-check me-1"></i> PAID
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 fw-bold" style="border-radius:4px; font-size:11.5px;">
                                    <i class="fa-solid fa-clock me-1"></i> UNPAID
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Status Controls --}}
        <div class="col-lg-4">
            {{-- Update Reservation Status --}}
            <div class="card p-3 shadow-sm border-0 mb-3 bg-white" style="border-radius:4px; border:1px solid #e8e8e8 !important;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size:13.5px;">
                    <i class="fa-solid fa-sliders text-primary me-2"></i> Update Reservation Status
                </h6>
                <form action="{{ route('vendor.bookings.update-status', $booking->booking_reference ?: $booking->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold mb-1">Current Status</label>
                        <select name="status" class="form-select form-select-sm" style="height:36px; font-size:13px; border-radius:4px;">
                            <option value="confirmed" {{ strtolower($booking->effective_status ?? $booking->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="pending" {{ strtolower($booking->effective_status ?? $booking->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ strtolower($booking->effective_status ?? $booking->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ strtolower($booking->effective_status ?? $booking->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="height:36px; border-radius:4px; background:var(--primary); border:none;">
                        <i class="fa-solid fa-check me-1"></i> Update Status
                    </button>
                </form>
            </div>

            {{-- Update Payment Status --}}
            <div class="card p-3 shadow-sm border-0 bg-white" style="border-radius:4px; border:1px solid #e8e8e8 !important;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size:13.5px;">
                    <i class="fa-solid fa-credit-card text-success me-2"></i> Update Payment Status
                </h6>
                <form action="{{ route('vendor.bookings.update-payment', $booking->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold mb-1">Payment Status</label>
                        <select name="payment_status" class="form-select form-select-sm" style="height:36px; font-size:13px; border-radius:4px;">
                            <option value="paid" {{ strtolower($booking->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ strtolower($booking->payment_status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="refunded" {{ strtolower($booking->payment_status ?? '') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            <option value="failed" {{ strtolower($booking->payment_status ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-success btn-sm w-100 fw-bold" style="height:36px; border-radius:4px;">
                        <i class="fa-solid fa-money-bill-wave me-1"></i> Update Payment Status
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
