@extends('layouts.vendor')
@section('title', 'Manage Guest Bookings | Vendor Portal')

@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Bookings Ledger</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Guest Reservations &amp; Bookings</h1>
    </div>
</div>

<div class="page-content-area">

    {{-- Filter Bar --}}
    <form action="{{ route('vendor.bookings.index') }}" method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by Guest Name, Email, or Booking Ref...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-filter me-1"></i> Filter</button>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Guest Reservations</h6>
            <span class="live-feed-badge">Live Ledger</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Guest Info</th>
                        <th>Property</th>
                        <th>Check-in → Check-out</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td style="font-family:monospace; font-weight:700; font-size:13px;">
                            {{ $b->booking_reference }}
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $b->guest_name }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $b->guest_email }}</span>
                        </td>
                        <td>
                            <span style="font-size:12.5px; font-weight:600;">{{ $b->property?->name ?? 'Property' }}</span>
                        </td>
                        <td style="font-size:12px;">
                            {{ \Carbon\Carbon::parse($b->check_in)->format('M d') }} → {{ \Carbon\Carbon::parse($b->check_out)->format('M d, Y') }}
                        </td>
                        <td>
                            <strong style="color:var(--primary); font-size:14px;">{{ CurrencyService::format($b->amount) }}</strong>
                        </td>
                        <td>
                            <span class="badge-gateway">{{ ucfirst($b->payment_method ?? 'bKash') }}</span>
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($b->effective_status) == 'confirmed' ? 'confirmed' : 'pending' }}">
                                {{ ucfirst($b->effective_status) }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('vendor.bookings.show', $b->booking_reference) }}" class="btn-table-action primary">
                                Details <i class="fa-solid fa-chevron-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:#8c8c8c;">
                            No reservations found for your properties.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($bookings, 'links'))
        <div style="padding:12px 16px; border-top:1px solid #f0f0f0;">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
