@extends('layouts.app')

@section('title', 'Checkin Out | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Checkin Out">Checkin Out</li>
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
                <div class="row g-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Checkin Out</h3>
							<div class="col-auto d-flex w-sm-100">
								<select class="form-select" onchange="window.location.href='?room_id='+this.value">
									<option value="">Choose Room No</option>
									@if(isset($rooms))
										@foreach($rooms as $r)
											<option value="{{ $r->id }}" {{ (isset($selectedRoom) && $selectedRoom->id == $r->id) ? 'selected' : '' }}>
												Room-{{ $r->room_number }} ({{ $r->status }})
											</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h6 class="card-title">Customer Details</h6>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-sm table-bordered mb-0">
									  <tbody>
										<tr>
										  <th scope="row">Name</th>
										  <td>{{ $reservation?->guest?->full_name ?? 'Jone Liya' }}</td>
										</tr>
										<tr>
										  <th scope="row">Room No </th>
										  <td>{{ $selectedRoom?->room_number ?? '102' }}</td>
										</tr>
										<tr>
										  <th scope="row">Booking No</th>
										  <td>{{ $reservation?->reservation_code ?? '11580' }}</td>
										</tr>
										<tr>
										  <th scope="row">Email ID</th>
										  <td>{{ $reservation?->guest?->email ?? 'joneliya@gmail.com' }}</td>
										</tr>
										<tr>
											<th scope="row">Mobile No</th>
											<td>{{ $reservation?->guest?->phone ?? '+22 202-302-4586' }}</td>
										</tr>
										<tr>
											<th scope="row">Address</th>
											<td>{{ $reservation?->guest?->address ?? 'Low FG, Fat Road' }}</td>
										</tr>
										<tr>
											<th scope="row">Time Format</th>
											<td>24HRS</td>
										</tr>
									  </tbody>
									</table>
								</div>
							</div>
						</div>
				  	</div>
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h6 class="card-title">Billing Details</h6>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-sm table-bordered mb-0">
									  <tbody>
										<tr>
										  <th scope="row">Room Rent Amt</th>
										  <td>${{ number_format($reservation?->base_rate ?? 800, 2) }}</td>
										</tr>
										<tr>
										  <th scope="row">Discount Amt</th>
										  <td>$0.00</td>
										</tr>
										<tr>
										  <th scope="row">Service Charge Amt</th>
										  <td>${{ number_format(($reservation?->base_rate ?? 800) * 0.05, 2) }}</td>
										</tr>
										<tr>
											<th scope="row">Total Room Rent Amt</th>
											<td>${{ number_format($reservation?->total_amount ?? 856, 2) }}</td>
										</tr>
										<tr>
											<th scope="row">Room Amt With Tax</th>
											<td>${{ number_format($reservation?->total_amount ?? 972, 2) }}</td>
										</tr>
										<tr>
											<th scope="row">Advance Amt</th>
											<td>${{ number_format($reservation?->folio?->total_paid ?? 0, 2) }}</td>
										</tr>
										<tr>
											<th scope="row">Payable Rent Amt</th>
											<td>${{ number_format($reservation?->folio?->balance ?? 972, 2) }}</td>
										</tr>
									  </tbody>
									</table>
								</div>
							</div>
						</div>
				  	</div>
                </div><!-- Row End -->

			</div>
@endsection
