@extends('layouts.app')

@section('title', 'Room Booking List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Room Booking List">Room Booking List</li>
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
							<form method="GET" action="{{ route('room.bookings.list') }}">
								<div class="row g-3 mt-3">
									<div class="col-12">
										<label class="form-label small text-muted">Sorted by:</label>
										<input type="search" name="search" class="form-control" placeholder="Search reservation or guest" value="{{ request('search') }}">
									</div>
									<div class="col-12 text-end mt-4">
										<a href="{{ route('room.bookings.list') }}" class="btn btn-sm btn-light me-1">Reset</a>
										<button type="submit" class="btn btn-sm btn-primary">Apply</button>
									</div>
								</div>
							</form>
						</div>
					</li>
				</ul>
			</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
                <div class="row g-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Room Booking List</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark btn-set-task w-sm-100" data-bs-toggle="modal" data-bs-target="#quickBookingModal">Room Book</button>
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
										@if(isset($bookings) && $bookings->count() > 0)
											@foreach($bookings as $res)
												<tr>
													<td>
														<strong>{{ $res->guest?->full_name ?? 'Guest' }}</strong>
														<div class="small text-muted">{{ $res->reservation_code }}</div>
													</td>
													<td>{{ $res->room?->roomType?->name ?? 'Deluxe Room' }}</td>
													<td>{{ \Carbon\Carbon::parse($res->check_in_date)->format('d/m/Y') }}</td>
													<td>{{ \Carbon\Carbon::parse($res->check_out_date)->format('d/m/Y') }}</td>
													<td>${{ number_format($res->folio?->total_paid ?? 0, 2) }}</td>
													<td>${{ number_format($res->folio?->balance ?? 0, 2) }}</td>
													<td>
														<span class="badge {{ ($res->folio?->balance ?? 0) <= 0 ? 'text-bg-success' : 'text-bg-warning' }}">
															{{ ($res->folio?->balance ?? 0) <= 0 ? 'Success' : 'Pending' }}
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
