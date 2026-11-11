@extends('layouts.admin')
@section('title', 'Booking Details #' . ($booking->booking_reference ?? $booking->id) . ' | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Booking Record details #{{ $booking->booking_reference ?? $booking->id }}</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('admin.bookings.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959;">
                <i class="fa-solid fa-arrow-left"></i> Back to All Bookings
            </a>
            <a href="{{ route('booking.voucher', $booking->booking_reference ?? $booking->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius:4px; height:32px; font-size:12px; display:inline-flex; align-items:center;">
                <i class="fa-solid fa-ticket me-1"></i> Check-in Voucher
            </a>
            <a href="{{ route('booking.invoice', $booking->booking_reference ?? $booking->id) }}" target="_blank" class="btn btn-sm btn-primary fw-bold text-white" style="border-radius:4px; height:32px; font-size:12px; background:var(--primary); border:none; display:inline-flex; align-items:center;">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Official Tax Invoice
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.bookings.index') }}">Reservations</a>
        <span class="sep">-</span><strong style="color:#333;">Ref: {{ $booking->booking_reference ?? 'PRM-'.$booking->id }}</strong>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Rejection / Cancellation Big Notice --}}
    @if(in_array(strtolower($booking->booking_status ?? $booking->status ?? ''), ['cancelled', 'rejected']))
        <div class="alert alert-danger d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background:#fee2e2; border:1px solid #ef4444; color:#991b1b;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px; height:44px; border-radius:50%; background:#dc2626; color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div>
                    <div style="font-size:16px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:0.5px;">
                        🚨 THIS RESERVATION IS REJECTED / CANCELLED
                    </div>
                    <div style="font-size:13px; color:#7f1d1d; margin-top:2px;">
                        Status: <strong class="text-uppercase">{{ $booking->effective_status ?? $booking->status }}</strong> — This booking is not active. The room inventory has been released.
                    </div>
                </div>
            </div>
            <span class="badge bg-danger px-3 py-2 text-uppercase fw-bold" style="font-size:12px; letter-spacing:1px; background:#dc2626 !important;">
                <i class="fa-solid fa-ban me-1"></i> REJECTED
            </span>
        </div>
    @endif

    <div class="row g-3">

        {{-- Main Booking Details --}}
        <div class="col-lg-8">
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-hotel me-1"></i> Property &amp; Reservation Details
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Property Name</label>
                        <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0;">
                            {{ optional($booking->property)->name ?? $booking->property_name ?? 'N/A' }}
                        </p>
                        <span style="font-size:11px; color:#8c8c8c;">City: {{ optional($booking->property)->city ?? 'Bangladesh' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Booking Reference</label>
                        <p style="font-size:14px; font-weight:700; color:var(--primary); margin:0;">
                            {{ $booking->booking_reference ?? 'PRM-'.str_pad($booking->id,4,'0',STR_PAD_LEFT) }}
                        </p>
                        <span style="font-size:11px; color:#8c8c8c;">Booked on: {{ $booking->created_at ? $booking->created_at->format('M d, Y, h:i A') : 'N/A' }}</span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Check-In Date</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;"><i class="fa-solid fa-calendar-check text-success me-1"></i> {{ $booking->check_in }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Check-Out Date</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;"><i class="fa-solid fa-calendar-xmark text-danger me-1"></i> {{ $booking->check_out }}</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Adults</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;">{{ $booking->adults ?? 1 }} Guests</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Children</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;">{{ $booking->children ?? 0 }} Kids</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Nights</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;">{{ $booking->nights ?? 1 }} Night(s)</p>
                    </div>
                </div>
            </div>

            {{-- Guest Info --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-user me-1"></i> Guest / Booker Contact Info
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Guest Full Name</label>
                        <p style="font-size:13px; font-weight:700; color:#1e293b; margin:0;">{{ $booking->guest_name ?? optional($booking->user)->name ?? 'Guest User' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone Number</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;">{{ $booking->guest_phone ?? optional($booking->user)->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email Address</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;">{{ $booking->guest_email ?? optional($booking->user)->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Guest Special Requests & Preferences Card --}}
            <div class="form-card mb-3" style="border-left: 4px solid #6366f1;">
                <div class="form-section-title d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fa-solid fa-clipboard-list text-primary me-1"></i> Guest Special Requests &amp; Room Preferences</span>
                    @if($booking->special_requests)
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:11px;">Active Requests</span>
                    @else
                        <span class="badge bg-light text-muted border" style="font-size:11px;">None Specified</span>
                    @endif
                </div>
                <div style="padding:4px 0;">
                    @if($booking->special_requests)
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @php
                                    $sr = $booking->special_requests;
                                    $hasNonSmoking = stripos($sr, 'Non-smoking') !== false;
                                    $hasSmoking = stripos($sr, 'Smoking') !== false && !$hasNonSmoking;
                                    $hasLargeBed = stripos($sr, 'Large bed') !== false;
                                    $hasTwinBed = stripos($sr, 'Twin') !== false;
                                @endphp

                                @if($hasNonSmoking)
                                    <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🚭 Non-smoking Room
                                    </span>
                                @elseif($hasSmoking)
                                    <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🚬 Smoking Room
                                    </span>
                                @endif

                                @if($hasLargeBed)
                                    <span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🛏️ Large Bed Setup
                                    </span>
                                @elseif($hasTwinBed)
                                    <span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🛏️ Twin Beds Setup
                                    </span>
                                @endif

                                @if(stripos($sr, 'High floor') !== false)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🏢 High Floor
                                    </span>
                                @endif

                                @if(stripos($sr, 'Low floor') !== false)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🏠 Low Floor
                                    </span>
                                @endif

                                @if(stripos($sr, 'Quiet room') !== false)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🤫 Quiet Room
                                    </span>
                                @endif

                                @if(stripos($sr, 'Early check-in') !== false)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        ⏰ Early Check-in
                                    </span>
                                @endif

                                @if(stripos($sr, 'Late check-out') !== false)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        ⌛ Late Check-out
                                    </span>
                                @endif

                                @if(stripos($sr, 'Airport') !== false)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-2.5 py-1.5" style="font-size:12px;">
                                        🚗 Airport Transfer
                                    </span>
                                @endif
                            </div>

                            <div style="font-size:13px; color:#334155; line-height:1.6; background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:10px 14px;">
                                <strong style="color:#0f172a; display:block; margin-bottom:4px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Full Request Details / Notes:</strong>
                                {{ $booking->special_requests }}
                            </div>
                        </div>
                    @else
                        <p style="font-size:13px; color:#64748b; margin:0; padding:10px 14px; background:#f8fafc; border-radius:6px; border:1px dashed #cbd5e1;">
                            <i class="fa-solid fa-circle-info text-muted me-1"></i> No special room or bed requests were specified by the guest during booking.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Financial & Payment Info --}}
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fa-solid fa-receipt me-1"></i> Billing &amp; Payment Breakdown
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Amount Paid / Payable</label>
                        <p style="font-size:20px; font-weight:800; color:var(--primary); margin:0;">BDT {{ number_format($booking->total_amount ?? $booking->total_price ?? 0) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Gateway Used</label>
                        <p style="font-size:13px; font-weight:600; color:#334155; margin:0;">
                            <span class="badge-gateway">{{ $booking->currency ?? 'BDT' }} Gateway ({{ $booking->payment_status ?? 'Pending' }})</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Side Column --}}
        <div class="col-lg-4">

            {{-- Update Booking Status Form --}}
            <div class="data-table-card mb-3">
                <div class="data-table-card-header">
                    <h6>Update Reservation Status</h6>
                </div>
                <div style="padding:16px;">
                    <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                        @csrf
                        <label class="form-label mb-1" style="font-size:11px; font-weight:600; color:#8c8c8c;">CURRENT STATUS</label>
                        <select name="status" class="form-select mb-3" style="height:36px; font-size:13px;">
                            <option value="confirmed" {{ ($booking->booking_status ?? $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="pending" {{ ($booking->booking_status ?? $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ ($booking->booking_status ?? $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ ($booking->booking_status ?? $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled / Rejected</option>
                        </select>
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn-add-primary w-100 style-btn" style="justify-content:center; padding:8px;">
                                Update Status <i class="fa-solid fa-check ms-1"></i>
                            </button>
                            @if(!in_array(strtolower($booking->booking_status ?? $booking->status ?? ''), ['cancelled', 'rejected']))
                                <button type="submit" name="status" value="cancelled" class="btn btn-danger btn-sm w-100 fw-bold" style="padding:7px; background:#dc2626; border:none;" onclick="return confirm('Are you sure you want to REJECT / CANCEL this reservation? Room inventory will be automatically released back.')">
                                    <i class="fa-solid fa-ban me-1"></i> Reject / Cancel Reservation
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Update Payment Status Form --}}
            <div class="data-table-card mb-3">
                <div class="data-table-card-header">
                    <h6>Update Payment Status</h6>
                </div>
                <div style="padding:16px;">
                    <form action="{{ route('admin.bookings.update-payment', $booking->id) }}" method="POST">
                        @csrf
                        <label class="form-label mb-1" style="font-size:11px; font-weight:600; color:#8c8c8c;">PAYMENT STATUS</label>
                        <select name="payment_status" class="form-select mb-3" style="height:36px; font-size:13px;">
                            <option value="paid" {{ ($booking->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ ($booking->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="refunded" {{ ($booking->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            <option value="failed" {{ ($booking->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <button type="submit" class="btn-export-csv w-100 style-btn" style="justify-content:center; padding:8px;">
                            Update Payment <i class="fa-solid fa-credit-card ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Delete Booking --}}
            <div class="data-table-card">
                <div class="data-table-card-header">
                    <h6>Danger Zone</h6>
                </div>
                <div style="padding:16px;">
                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this booking record permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-table-action danger w-100" style="justify-content:center; padding:8px;">
                            Delete Booking Record <i class="fa-solid fa-trash ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

