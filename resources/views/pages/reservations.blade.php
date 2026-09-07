@extends('layouts.app')

@section('title', 'Reservations & Bookings')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Reservation Portfolio</h4>
            <p class="text-muted mb-0">Search, filter, check-in, check-out, and print folio invoices.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#walkInModal"><i class="fa-solid fa-plus me-2"></i>New Walk-In Booking</button>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by Code, Guest Name, Phone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                        <option value="CHECKED_IN" {{ request('status') === 'CHECKED_IN' ? 'selected' : '' }}>Checked In</option>
                        <option value="CHECKED_OUT" {{ request('status') === 'CHECKED_OUT' ? 'selected' : '' }}>Checked Out</option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Booking Code</th>
                        <th>Guest Details</th>
                        <th>Room Assigned</th>
                        <th>Dates & Stay</th>
                        <th>Source</th>
                        <th>Amount & Dues</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr>
                            <td class="fw-bold font-monospace text-primary">{{ $res->reservation_code }}</td>
                            <td>
                                <div class="fw-bold">{{ $res->guest->full_name ?? 'Guest' }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $res->guest->phone ?? 'N/A' }}</div>
                                @if($res->guest->is_vip ?? false)
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i>VIP</span>
                                @endif
                            </td>
                            <td>
                                @if($res->room)
                                    <span class="badge bg-light text-dark border fs-6">Room {{ $res->room->room_number }}</span>
                                    <small class="d-block text-muted">{{ $res->roomType->name ?? '' }}</small>
                                @else
                                    <span class="badge bg-warning text-dark">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $res->check_in_date->format('M d, Y') }}</div>
                                <div class="text-muted small">to {{ $res->check_out_date->format('M d, Y') }} ({{ $res->check_in_date->diffInDays($res->check_out_date) }}N)</div>
                            </td>
                            <td>
                                @if($res->source === 'PRIME_BOOKING_OTA')
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25"><i class="fa-solid fa-globe me-1"></i>Prime OTA</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary border-opacity-25"><i class="fa-solid fa-user me-1"></i>Walk-in</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">৳{{ number_format($res->total_amount) }}</div>
                                @if($res->balance_amount > 0)
                                    <small class="text-danger fw-semibold">Due: ৳{{ number_format($res->balance_amount) }}</small>
                                @else
                                    <small class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Paid</small>
                                @endif
                            </td>
                            <td>
                                @if($res->status === 'CHECKED_IN')
                                    <span class="badge bg-success">Checked In</span>
                                @elseif($res->status === 'CONFIRMED')
                                    <span class="badge bg-primary">Confirmed</span>
                                @elseif($res->status === 'CHECKED_OUT')
                                    <span class="badge bg-secondary">Checked Out</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $res->status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($res->status === 'CONFIRMED')
                                        <button class="btn btn-outline-success" onclick="checkInGuest({{ $res->id }})" title="Check-In Guest">
                                            <i class="fa-solid fa-right-to-bracket me-1"></i>Check-in
                                        </button>
                                    @elseif($res->status === 'CHECKED_IN')
                                        <button class="btn btn-outline-danger" onclick="checkOutGuest({{ $res->id }})" title="Check-Out Guest">
                                            <i class="fa-solid fa-right-from-bracket me-1"></i>Check-out
                                        </button>
                                    @endif

                                    @if($res->folio)
                                        <a href="{{ route('folios.invoice', $res->folio->id) }}" class="btn btn-outline-secondary" title="Folio Invoice">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No reservations match your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reservations->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function checkInGuest(id) {
        if (!confirm('Proceed with Check-In for this guest?')) return;
        try {
            const res = await fetch(`/api/v1/reservations/${id}/check-in`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await res.json();
            if (data.success) {
                alert('Guest Checked In successfully! Room is now marked Occupied.');
                window.location.reload();
            } else {
                alert(data.message);
            }
        } catch(e) {
            window.location.reload();
        }
    }

    async function checkOutGuest(id) {
        if (!confirm('Proceed with Check-Out? Room will be flagged for Housekeeping.')) return;
        try {
            const res = await fetch(`/api/v1/reservations/${id}/check-out`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await res.json();
            if (data.success) {
                alert('Guest Checked Out successfully! Room marked DIRTY.');
                window.location.reload();
            } else {
                alert(data.message);
            }
        } catch(e) {
            window.location.reload();
        }
    }
</script>
@endpush
