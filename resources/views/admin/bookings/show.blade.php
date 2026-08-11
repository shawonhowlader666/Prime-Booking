@extends('layouts.admin')
@section('title', 'Booking Details #' . ($booking->booking_reference ?? $booking->id) . ' | Prime Aviation Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.bookings.index') }}">Reservations</a>
        <span class="sep">-</span><strong style="color:#333;">Ref: {{ $booking->booking_reference ?? 'PRM-'.$booking->id }}</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Booking Record details #{{ $booking->booking_reference ?? $booking->id }}</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <a href="{{ route('admin.bookings.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959;">
                <i class="fa-solid fa-arrow-left"></i> Back to All Bookings
            </a>
            <button class="btn-export-pdf" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print Voucher
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
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
                    @if($booking->special_requests)
                        <div class="col-12">
                            <label class="form-label">Special Requests</label>
                            <p style="font-size:12.5px; color:#595959; background:#fafafa; padding:8px 12px; border-radius:6px; border:1px solid #f0f0f0; margin:0;">
                                {{ $booking->special_requests }}
                            </p>
                        </div>
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
                            <option value="cancelled" {{ ($booking->booking_status ?? $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="btn-add-primary w-100 style-btn" style="justify-content:center; padding:8px;">
                            Update Status <i class="fa-solid fa-check ms-1"></i>
                        </button>
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
