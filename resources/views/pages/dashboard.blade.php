@extends('layouts.app')

@section('title', 'Dashboard | HotelAir')

@section('content')
<!-- start: page header area -->
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
    <ol class="breadcrumb mb-0 bg-transparent">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" title="home">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page" title="Dashboard">Dashboard</li>
    </ol>
    <ul class="list-unstyled action d-flex align-items-center mb-0">
        <li class="dropdown">
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
                    <path d="M9 12l6 0"></path>
                    <path d="M12 9l0 6"></path>
                </svg>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2 p-xl-3 shadow rounded-4">
                <li><a class="dropdown-item rounded-pill" href="#" data-bs-toggle="modal" data-bs-target="#walkInModal">+ New Reservation</a></li>
                <li><a class="dropdown-item rounded-pill" href="{{ route('matrix') }}">Room Matrix</a></li>
                <li><a class="dropdown-item rounded-pill" href="{{ route('sync-hub') }}">Prime Booking Sync</a></li>
            </ul>
        </li>
        <li class="dropdown ms-3">
            <a href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"></path>
                </svg>
            </a>
        </li>
    </ul>
</div>

<!-- start: page body area -->
<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
    <!-- 4 Official KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Today Booking</h6>
                </div>
                <div class="card-body">
                    <h2>1,587</h2>
                    <span class="badge bg-primary">+11% </span>
                    <span class="text-muted">From previous period</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Total Amount</h6>
                </div>
                <div class="card-body">
                    <h2>$2,258</h2>
                    <span class="badge bg-info">+05% </span>
                    <span class="text-muted">New income</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Total Customer</h6>
                </div>
                <div class="card-body">
                    <h2>2.3k</h2>
                    <span class="badge bg-warning">+11% </span>
                    <span class="text-muted">From previous period</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Total Revenue</h6>
                </div>
                <div class="card-body">
                    <h2>11,5587</h2>
                    <span class="badge bg-success">+21% </span>
                    <span class="text-muted">From previous period</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Reservations Mixed Chart Card -->
    <div class="row g-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Reservations</h6>
                </div>
                <div class="card-body">
                    <div id="reservations" style="height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Official DataTable Booking List -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Today Booking List</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#walkInModal">+ New Booking</button>
                </div>
                <div class="card-body">
                    <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Booking Code</th>
                                <th>Guest Name</th>
                                <th>Room Unit</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Amount</th>
                                <th>Source</th>
                                <th>Payment Status</th>
                                <th class="text-end">Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $res)
                                <tr>
                                    <td class="fw-bold font-monospace text-primary">{{ $res->reservation_code }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $res->guest->full_name ?? 'Guest' }}</div>
                                        <small class="text-muted">{{ $res->guest->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $res->room ? 'Room ' . $res->room->room_number : 'Auto-Assign' }}
                                        </span>
                                        <small class="d-block text-muted">{{ $res->roomType->name ?? '' }}</small>
                                    </td>
                                    <td>{{ $res->check_in_date->format('d/m/Y') }}</td>
                                    <td>{{ $res->check_out_date->format('d/m/Y') }}</td>
                                    <td class="fw-bold">৳{{ number_format($res->total_amount) }}</td>
                                    <td>
                                        @if($res->source === 'PRIME_BOOKING_OTA')
                                            <span class="badge bg-info">Prime OTA</span>
                                        @else
                                            <span class="badge bg-secondary">Walk-In</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($res->status === 'CHECKED_IN')
                                            <span class="badge bg-success">Checked In</span>
                                        @elseif($res->status === 'CONFIRMED')
                                            <span class="badge bg-primary">Confirmed</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $res->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($res->folio)
                                            <a href="{{ route('folios.invoice', $res->folio->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-receipt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No reservations recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var initialColors = (typeof window.getThemeColors === 'function') ? window.getThemeColors() : ['#6238B3', '#00B8D6'];
    var options = {
        chart: {
            height: 280,
            type: 'line',
            toolbar: { show: false }
        },
        colors: initialColors,
        series: [{
            name: 'Booking Confirmed',
            type: 'column',
            data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160]
        }, {
            name: 'Booking Pending',
            type: 'line',
            data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16]
        }],
        stroke: {
            width: [0, 4]
        },
        labels: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
        xaxis: {
            type: 'datetime'
        },
        yaxis: [{
            title: { text: 'Booking Confirmed' }
        }, {
            opposite: true,
            title: { text: 'Booking Pending' }
        }]
    };
    window.reservationChart = new ApexCharts(document.querySelector("#reservations"), options);
    window.reservationChart.render();

    $('.dataTable').addClass('nowrap').dataTable({
        responsive: true
    });
});
</script>
@endpush
