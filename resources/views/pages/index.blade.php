@extends('layouts.app')

@section('title', 'Index | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
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
								<div class="col-12">
									<label class="form-label small text-muted">Status:</label>
									<select class="form-select">
										<option selected>Choose...</option>
										<option>...</option>
									</select>
								</div>
								<div class="col-12">
									<label class="form-label small text-muted">Notifications:</label>
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" role="switch">
										<label class="form-check-label">Enabled</label>
									</div>
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
					<div class="col-sm-12">
                        <div class="card">
							<div class="card-header">
								<h6 class="card-title">Today Booking List</h6>
							</div>
                            <div class="card-body">
                                <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Room Type</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <th>Paid Amount</th>
                                            <th>Due Amount</th>
                                            <th>Payment Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                           <td>
                                               Frank Baker
                                           </td>
                                           <td>Single</td>
                                           <td>12/03/2024</td>
                                           <td>13/03/2024</td>
                                           <td>0.00</td>
                                           <td>$230</td>
                                           <td><span class="badge text-bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Rya Randall
                                            </td>
                                            <td>Deluxe</td>
											<td>12/03/2024</td>
											<td>24/03/2024</td>
                                            <td>0.00</td>
                                            <td>$430</td>
                                            <td><span class="badge text-bg-warning">Pending</span></td>
                                         </tr>
                                         <tr>
                                            <td>
                                                Phil Glover
                                            </td>
                                            <td>Studio</td>
											<td>12/03/2024</td>
											<td>21/03/2024</td>
                                            <td>0.00</td>
                                            <td>$4450</td>
                                            <td><span class="badge text-bg-warning">Pending</span></td>
                                         </tr>
                                         <tr>
                                            <td>
                                                Victor Rampling
                                            </td>
                                            <td>Junior Suite</td>
											<td>12/03/2024</td>
											<td>15/03/2024</td>
                                            <td>0.00</td>
                                            <td>$530</td>
                                            <td><span class="badge text-bg-warning">Pending</span></td>
                                         </tr>
                                         <tr>
                                            <td>
                                                Sally Graham
                                            </td>
                                            <td>Queen</td>
											<td>12/03/2024</td>
											<td>17/03/2024</td>
                                            <td>$1550</td>
                                            <td>0.00</td>
                                            <td><span class="badge text-bg-success">Success</span></td>
                                         </tr>
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
            toolbar: {
                show: false,
            },
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
            title: {
                text: 'Booking Confirmed',
            },
        },{
            opposite: true,
            title: {
                text: 'Booking Pending'
            }
        }]
    };

    window.reservationChart = new ApexCharts(document.querySelector("#reservations"), options);
    window.reservationChart.render();

    $('.dataTable')
        .addClass('nowrap')
        .dataTable({
            responsive: true,
        });
});
</script>
@endpush
