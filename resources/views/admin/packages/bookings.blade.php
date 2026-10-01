@extends('layouts.admin')

@section('title', 'Tour Package Bookings — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0"><i class="fa-solid fa-map-location-dot me-2 text-primary"></i>Tour Package Bookings &amp; Inquiries</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <a href="{{ route('admin.packages.index') }}" class="btn-tbl-copy" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Manage Packages</a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.packages.index') }}">Tour Packages</a>
        <span class="sep">-</span><strong style="color:#333;">Reservations</strong>
    </div>
</div>

<div class="page-content-area">
    <div class="admin-table-card" style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #edf2f7; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:15px; font-weight:700; margin:0; color:#1e293b;">All Package Reservations ({{ $bookings->total() }})</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead style="background:#f8fafc; color:#64748b;">
                    <tr>
                        <th class="ps-3">Reference</th>
                        <th>Package</th>
                        <th>Guest Details</th>
                        <th>Travel Date</th>
                        <th>Guests</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td class="ps-3 fw-bold text-primary">
                                #{{ $b->booking_reference }}
                            </td>
                            <td>
                                <strong class="text-dark">{{ $b->package?->title ?? 'Custom Package' }}</strong>
                                <div class="small text-muted">{{ $b->package?->destination }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->guest_name }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $b->guest_phone }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-envelope me-1"></i>{{ $b->guest_email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $b->travel_date?->format('d M Y') }}</span>
                            </td>
                            <td>
                                {{ $b->guests }} Person{{ $b->guests > 1 ? 's' : '' }}
                            </td>
                            <td>
                                <strong class="text-success">BDT {{ number_format((float)$b->total_amount) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success fw-bold px-2 py-1">
                                    {{ strtoupper($b->status) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $b->created_at?->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-suitcase-rolling fa-2x mb-2 text-secondary"></i>
                                <p class="mb-0">No tour package reservations recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div style="padding:16px 20px; border-top:1px solid #edf2f7;">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
