@extends('layouts.app')

@section('title', 'Visual Room Matrix')

@push('styles')
<style>
    .matrix-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .matrix-table th, .matrix-table td {
        border: 1px solid var(--pms-card-border);
        padding: 0.5rem 0.4rem;
        text-align: center;
        vertical-align: middle;
        font-size: 0.78rem;
    }
    .matrix-room-col {
        position: sticky;
        left: 0;
        background-color: var(--bs-card-bg);
        z-index: 10;
        width: 140px;
        min-width: 140px;
        text-align: left !important;
        font-weight: 600;
    }
    .matrix-today {
        background-color: rgba(67, 97, 238, 0.08) !important;
        border-left: 2px solid var(--pms-primary) !important;
        border-right: 2px solid var(--pms-primary) !important;
    }
    .booking-cell-booked {
        background-color: #4361ee;
        color: #fff;
        border-radius: 4px;
        padding: 4px 2px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.15s ease;
        box-shadow: 0 2px 4px rgba(67, 97, 238, 0.25);
    }
    .booking-cell-booked:hover {
        transform: scale(1.05);
    }
    .booking-cell-ota {
        background-color: #3a0ca3;
    }
    .booking-cell-free {
        color: #94a3b8;
        cursor: pointer;
    }
    .booking-cell-free:hover {
        background-color: rgba(46, 196, 182, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Interactive Room Reservation Matrix</h4>
            <p class="text-muted mb-0">Visual timeline of room occupancy, bookings, and available slots.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <input type="date" class="form-control form-control-sm" id="matrixStartDate" value="{{ $startDate }}" style="width: 160px;" onchange="window.location.href='?start_date='+this.value">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#walkInModal"><i class="fa-solid fa-plus me-1"></i>New Booking</button>
        </div>
    </div>

    <!-- Matrix Legend -->
    <div class="card mb-3">
        <div class="card-body py-2 d-flex flex-wrap align-items-center justify-content-between gap-3 small">
            <div class="d-flex gap-4">
                <span class="d-flex align-items-center gap-2"><span class="badge bg-primary px-2">&nbsp;</span> Checked-In / Walk-in</span>
                <span class="d-flex align-items-center gap-2"><span class="badge" style="background:#3a0ca3;">&nbsp;</span> Prime Booking OTA</span>
                <span class="d-flex align-items-center gap-2"><span class="badge bg-light text-muted border px-2">&nbsp;</span> Available Slot</span>
            </div>
            <div class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Click any booking cell to view details or check-in/out.</div>
        </div>
    </div>

    <!-- Timeline Grid Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 650px;">
                <table class="table matrix-table mb-0">
                    <thead class="table-light position-sticky top-0" style="z-index: 20;">
                        <tr>
                            <th class="matrix-room-col bg-light">Room / Date</th>
                            @foreach($matrixData['dates'] as $d)
                                <th class="{{ $d['is_today'] ? 'matrix-today text-primary fw-bold' : '' }}" style="min-width: 55px;">
                                    <div class="text-uppercase text-muted" style="font-size: 0.65rem;">{{ $d['day_name'] }}</div>
                                    <div class="fs-6 lh-1">{{ $d['day_num'] }}</div>
                                    <div style="font-size: 0.62rem;">{{ $d['month'] }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matrixData['matrix'] as $row)
                            <tr>
                                <td class="matrix-room-col">
                                    <div class="fw-bold">{{ $row['room_number'] }}</div>
                                    <small class="text-muted text-truncate d-block" style="font-size: 0.68rem;">{{ $row['type_name'] }}</small>
                                </td>
                                @foreach($matrixData['dates'] as $d)
                                    @php
                                        $cell = $row['days'][$d['date']] ?? ['is_booked' => false];
                                    @endphp
                                    <td class="{{ $d['is_today'] ? 'matrix-today' : '' }}">
                                        @if($cell['is_booked'])
                                            <div class="booking-cell-booked {{ $cell['source'] === 'PRIME_BOOKING_OTA' ? 'booking-cell-ota' : '' }}" 
                                                 title="{{ $cell['reservation_code'] }} - {{ $cell['guest_name'] }}"
                                                 onclick="alert('Reservation: {{ $cell['reservation_code'] }}\nGuest: {{ $cell['guest_name'] }}\nStatus: {{ $cell['status'] }}\nSource: {{ $cell['source'] }}')">
                                                <div class="text-truncate" style="font-size: 0.68rem;">{{ Str::limit($cell['guest_name'], 6) }}</div>
                                            </div>
                                        @else
                                            <div class="booking-cell-free py-1" onclick="document.querySelector('#walkInModal').querySelector('input[name=check_in_date]').value='{{ $d['date'] }}'; new bootstrap.Modal(document.querySelector('#walkInModal')).show();">
                                                <i class="fa-solid fa-plus opacity-25"></i>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
