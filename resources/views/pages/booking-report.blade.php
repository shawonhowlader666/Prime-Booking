@extends('layouts.app')

@section('title', 'Booking Reports | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Booking Reports">Booking Reports</li>
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
							<li><a class="dropdown-item rounded-pill" href="{{ url('/booking-report.html?export=csv') }}"><i class="fa fa-file-excel-o me-2"></i>Export CSV Report</a></li>
							<li><a class="dropdown-item rounded-pill" href="{{ url('/booking-report.html') }}">Refresh Reports</a></li>
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
                <div class="row g-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Booking Reports</h3>
						</div>
				  	</div>
					<div class="col-sm-12">
						<div class="card">
							<div class="card-header">
								<h6 class="card-title">Search Reports Here</h6>
							</div>
							<div class="card-body">
								<form method="GET" action="{{ route('reports.booking') }}">
									<div class="row g-3">
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">From</label>
												<div class="input-group">
													<span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
													<input type="date" name="from" class="form-control" value="{{ request('from') }}">
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">To</label>
												<div class="input-group">
													<span class="input-group-text"><i class="bi bi-calendar-minus"></i></span>
													<input type="date" name="to" class="form-control" value="{{ request('to') }}">
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Booking Status</label>
												<div class="input-group">
													<span class="input-group-text"><i class="bi bi-house"></i></span>
													<select name="booking_status" class="form-select">
														<option value="">Find Booking Status</option>
														<option value="PENDING" {{ request('booking_status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
														<option value="CONFIRMED" {{ request('booking_status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
														<option value="CHECKED_IN" {{ request('booking_status') === 'CHECKED_IN' ? 'selected' : '' }}>Check In</option>
														<option value="CHECKED_OUT" {{ request('booking_status') === 'CHECKED_OUT' ? 'selected' : '' }}>Check Out</option>
														<option value="CANCELLED" {{ request('booking_status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Payment Status</label>
												<div class="input-group">
													<span class="input-group-text"><i class="bi bi-cash"></i></span>
													<select name="payment_status" class="form-select">
														<option value="">Find Payment Status</option>
														<option value="PENDING">Pending</option>
														<option value="CANCELLED">Cancelled</option>
														<option value="PAID">Complete / Paid</option>
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-12 text-end">
											<a href="{{ route('reports.booking') }}" class="btn btn-light py-2 px-4 me-2">Reset</a>
											<button type="submit" class="btn btn-primary py-2 px-5">Search</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
                  <div class="col-sm-12">
                        <div class="card">
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
										@if(isset($reports) && $reports->count() > 0)
											@foreach($reports as $res)
												<tr>
													<td><strong>{{ $res->guest?->full_name ?? 'Guest' }}</strong></td>
													<td>{{ $res->room?->roomType?->name ?? 'Standard Suite' }}</td>
													<td>{{ \Carbon\Carbon::parse($res->check_in_date)->format('d/m/Y') }}</td>
													<td>{{ \Carbon\Carbon::parse($res->check_out_date)->format('d/m/Y') }}</td>
													<td>${{ number_format($res->folio?->total_paid ?? 0, 2) }}</td>
													<td>${{ number_format($res->folio?->balance ?? 0, 2) }}</td>
													<td>
														<span class="badge {{ ($res->folio?->balance ?? 0) <= 0 ? 'text-bg-success' : 'text-bg-warning' }}">
															{{ ($res->folio?->balance ?? 0) <= 0 ? 'Complete' : 'Pending' }}
														</span>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
											   <td>Frank Baker</td>
											   <td>Single</td>
											   <td>12/03/2021</td>
											   <td>14/03/2021</td>
											   <td>0.00</td>
											   <td>$230</td>
											   <td><span class="badge text-bg-warning">Pending</span></td>
											</tr>
											<tr>
												<td>Rya Randall</td>
												<td>Deluxe</td>
												<td>11/04/2021</td>
												<td>12/04/2021</td>
												<td>0.00</td>
												<td>$430</td>
												<td><span class="badge text-bg-warning">Pending</span></td>
											 </tr>
											 <tr>
												<td>Phil Glover</td>
												<td>Studio</td>
												<td>11/04/2021</td>
												<td>12/04/2021</td>
												<td>0.00</td>
												<td>$4450</td>
												<td><span class="badge text-bg-warning">Pending</span></td>
											 </tr>
										@endif
									</tbody>
                                </table>
                            </div>
                        </div>
                  </div>
                </div><!-- Row End -->
			</div>
@endsection

@push('scripts')
<script>
$(document).ready( function () {
    $('.dataTable')
    .addClass( 'nowrap' )
    .dataTable( {
        responsive: true,
    });
});
</script>
@endpush
