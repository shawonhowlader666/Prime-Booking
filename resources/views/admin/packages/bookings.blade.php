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
    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif
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
                        <th class="pe-3 text-end">Actions</th>
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
                                <span class="badge bg-light text-dark border">{{ $b->travel_date ? (is_string($b->travel_date) ? date('d M Y', strtotime($b->travel_date)) : $b->travel_date->format('d M Y')) : 'N/A' }}</span>
                            </td>
                            <td>
                                {{ $b->guests }} Person{{ $b->guests > 1 ? 's' : '' }}
                            </td>
                            <td>
                                <strong class="text-success">BDT {{ number_format((float)$b->total_amount) }}</strong>
                            </td>
                            <td>
                                @php
                                    $st = strtolower($b->status ?? 'confirmed');
                                    $badgeClass = match($st) {
                                        'completed' => 'bg-info bg-opacity-10 text-info border-info',
                                        'cancelled' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                        default     => 'bg-success bg-opacity-10 text-success border-success',
                                    };
                                @endphp
                                <span class="badge border fw-bold px-2 py-1 {{ $badgeClass }}">
                                    {{ strtoupper($b->status) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $b->created_at?->format('d M Y, h:i A') }}
                            </td>
                            <td class="pe-3 text-end" style="white-space:nowrap;">
                                <div class="dropdown action-gear-dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                        <i class="fa-solid fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3" href="{{ route('packages.voucher', $b->booking_reference) }}" target="_blank">
                                                <i class="fa-solid fa-ticket text-primary me-2"></i> View E-Voucher
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.packages.bookings.status', $b->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-success">
                                                    <i class="fa-solid fa-circle-check me-2"></i> Mark Confirmed
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.packages.bookings.status', $b->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-info">
                                                    <i class="fa-solid fa-flag-checkered me-2"></i> Mark Completed
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.packages.bookings.status', $b->id) }}" method="POST" class="m-0" onsubmit="return confirm('Cancel package reservation #{{ $b->booking_reference }}?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-ban me-2"></i> Mark Cancelled
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
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
