@extends('layouts.app')

@section('title', 'Executive Activity & Revenue Flow | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
    <ol class="breadcrumb mb-0 bg-transparent">
        <li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page" title="Dashboard">Executive Dashboard</li>
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
                <li><a class="dropdown-item rounded-pill" href="#">New folder</a></li>
                <li><a class="dropdown-item rounded-pill" href="#">File upload</a></li>
                <li><a class="dropdown-item rounded-pill" href="#">Google docs</a></li>
                <li><a class="dropdown-item rounded-pill" href="#">Google sheets</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item rounded-pill" href="#">+ Invite</a></li>
                <li><a class="dropdown-item rounded-pill" href="#">Generate report</a></li>
                <li><a class="dropdown-item rounded-pill" href="#">New Reports</a></li>
            </ul>
        </li>
        <li class="dropdown ms-3">
            <a href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <svg class="svg-stroke" xmlns="http://www.w3.org/2000/svg" width="22" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"></path>
                </svg>
            </a>
            <div class="dropdown-menu dropdown-menu-end p-2 p-xl-3 shadow rounded-4" style="width: 300px;">
                <h6>Filter Options</h6>
                <div class="row g-3 mt-3">
                    <div class="col-12">
                        <label class="form-label small text-muted">Sorted by:</label>
                        <input type="search" class="form-control" placeholder="Search">
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="reset" class="btn btn-sm btn-light me-1">Reset</button>
                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">

    <!-- TOP INSIGHT BAR -->
    <div class="row g-3 mb-4">

        <!-- ACTIVITY TIMELINE STYLE -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Hotel Activity Stream</h6>
                <span class="badge bg-success">Live Updates</span>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <span class="badge bg-success mb-2">Check In</span>
                                <h6 class="mb-1">Room 204 – Michael Johnson</h6>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">Payment</span>
                                <h6 class="mb-1">Invoice #5541 Paid ($450.00)</h6>
                                <small class="text-muted">5 minutes ago</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2">Housekeeping</span>
                                <h6 class="mb-1">Room 312 Cleaning Done</h6>
                                <small class="text-muted">12 minutes ago</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <span class="badge bg-danger mb-2">Maintenance</span>
                                <h6 class="mb-1">AC Issue – Room 118 Resolved</h6>
                                <small class="text-muted">20 minutes ago</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <span class="badge bg-info mb-2">New Booking</span>
                                <h6 class="mb-1">Reservation via Prime Booking OTA</h6>
                                <small class="text-muted">35 minutes ago</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2">Room Change</span>
                                <h6 class="mb-1">Guest moved 305 → 410</h6>
                                <small class="text-muted">1 hour ago</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Hotel Pulse Today</h5>

                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" style="width: {{ $kpis['occupancy_rate'] ?? 78 }}%">Occupied {{ $kpis['occupancy_rate'] ?? 78 }}%</div>
                    </div>

                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-warning text-dark" style="width: 14%">Cleaning 14%</div>
                    </div>

                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-danger" style="width: 8%">Maintenance 8%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Real-Time Hotel Efficiency Score</h6>
                    <h1 class="display-4 text-primary">9.4</h1>
                    <span class="badge bg-success">Superior Enterprise Performance Today</span>
                </div>
            </div>
        </div>

    </div>

    <!-- CENTRAL CONTROL AREA -->
    <div class="row g-3 mb-4">

        <div class="col-lg-4">

            <div class="card mb-3">
                <div class="card-header">Today Movement</div>
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Check Ins</span>
                        <strong>{{ $kpis['today_checkins'] ?? 14 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Check Outs</span>
                        <strong>{{ $kpis['today_checkouts'] ?? 12 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Available Rooms</span>
                        <strong>{{ $kpis['available_rooms'] ?? 45 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Total Rooms</span>
                        <strong>{{ $kpis['total_rooms'] ?? 60 }}</strong>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">Service Desk Alerts</div>
                <div class="card-body">

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            Laundry Requests
                            <span class="badge bg-primary">12</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            Housekeeping Calls
                            <span class="badge bg-warning text-dark">7</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            Maintenance Alerts
                            <span class="badge bg-danger">3</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            Guest Inquiries
                            <span class="badge bg-info">1</span>
                        </li>
                    </ul>

                </div>
            </div>

        </div>

        <!-- CENTER PANEL -->
        <div class="col-lg-8">

            <div class="card mb-3">
                <div class="card-header">Recent Reservations Activity</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Booking #</th>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentBookings) && $recentBookings->count() > 0)
                                    @foreach($recentBookings as $b)
                                        <tr>
                                            <td><strong>{{ $b->reservation_code }}</strong></td>
                                            <td>{{ $b->guest?->full_name ?? 'Guest' }}</td>
                                            <td>{{ $b->room?->room_number ?? 'Suite' }}</td>
                                            <td><span class="badge bg-success">{{ $b->status }}</span></td>
                                            <td><strong>${{ number_format($b->total_amount, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6>Monthly Revenue</h6>
                            <h2>${{ number_format($kpis['monthly_revenue'] ?? 48200, 0) }}</h2>
                            <span class="badge bg-success">+24.5%</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6>Today Revenue</h6>
                            <h2>${{ number_format($kpis['today_revenue'] ?? 3450, 0) }}</h2>
                            <span class="badge bg-primary">Audited</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6>Pending Folios</h6>
                            <h2>${{ number_format($kpis['pending_folios'] ?? 450, 0) }}</h2>
                            <span class="badge bg-warning text-dark">Active</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection
