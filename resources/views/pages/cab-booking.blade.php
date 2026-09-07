@extends('layouts.app')

@section('title', 'Cab Booking | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Cab Booking">Cab Booking</li>
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
							<form method="GET" action="{{ route('cabs.bookings') }}">
								<div class="row g-3 mt-3">
									<div class="col-12">
										<label class="form-label small text-muted">Sorted by:</label>
										<input type="search" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
									</div>
									<div class="col-12 text-end mt-4">
										<a href="{{ route('cabs.bookings') }}" class="btn btn-sm btn-light me-1">Reset</a>
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
							<h3 class="fw-bold mb-0">Cab Booking</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-booking">Add Cab Booking</button>
							</div>
						</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
											<th>Car</th>
											<th>Car Number</th>
											<th>Driver</th>
											<th>Customer</th>
											<th>Date</th>
											<th>Type</th>
											<th>Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@if(isset($bookings) && $bookings->count() > 0)
											@foreach($bookings as $index => $b)
												<tr>
													<td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
													<td><strong>{{ $b->cab?->car_name ?? 'Fleet Cab' }}</strong></td>
													<td><span class="badge bg-light text-dark border">{{ $b->cab?->car_number ?? 'N/A' }}</span></td>
													<td>{{ $b->driver_name ?? $b->cab?->driver_name ?? 'Driver' }}</td>
													<td>
														{{ $b->customer_name }}
														@if($b->reservation_id)
															<br><small class="text-muted"><i class="bi bi-door-closed"></i> Room {{ $b->reservation?->room?->room_number ?? 'Folio' }}</small>
														@endif
													</td>
													<td>{{ \Carbon\Carbon::parse($b->booking_date)->format('d/m/Y') }}</td>
													<td>
														<span class="badge {{ $b->transport_type == 'PICKUP' || $b->transport_type == 'Pick Up' ? 'bg-primary' : 'bg-warning text-dark' }}">
															{{ $b->transport_type }}
														</span>
													</td>
													<td>{{ $b->booking_time ?? '08:00 AM' }}</td>
													<td>
														<button type="button" class="btn btn-edit-booking" 
															data-id="{{ $b->id }}"
															data-car="{{ $b->cab_id }}"
															data-customer="{{ $b->customer_name }}"
															data-driver="{{ $b->driver_name }}"
															data-type="{{ $b->transport_type }}"
															data-time="{{ $b->booking_time }}"
															data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
														<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>01</td>
												<td>Innova</td>
												<td>AB-GB-0158</td>
												<td>Max</td>
												<td>Jemmy</td>
												<td>21/01/2024</td>
												<td>Pick Up</td>
												<td>08:20 AM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>02</td>
												<td>XUV 700</td>
												<td>AB-GB-1158</td>
												<td>Jone</td>
												<td>Cargo</td>
												<td>20/01/2024</td>
												<td>Drop</td>
												<td>10:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>03</td>
												<td>XUV 700</td>
												<td>AB-GB-1158</td>
												<td>Jone</td>
												<td>Rita</td>
												<td>25/05/2024</td>
												<td>Drop</td>
												<td>10:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>04</td>
												<td>XUV 700</td>
												<td>AB-GB-1158</td>
												<td>Lucy</td>
												<td>Meddy</td>
												<td>20/06/2024</td>
												<td>Drop</td>
												<td>10:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>05</td>
												<td>Maruti 800</td>
												<td>AB-GB-5511</td>
												<td>Hetto</td>
												<td>Depo</td>
												<td>14/06/2024</td>
												<td>Drop</td>
												<td>10:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>06</td>
												<td>Hyundai Verna</td>
												<td>AB-GB-7711</td>
												<td>Karry</td>
												<td>Atman</td>
												<td>17/05/2024</td>
												<td>Drop</td>
												<td>09:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>07</td>
												<td>Maruti Dzire</td>
												<td>AB-GB-9911</td>
												<td>Hiro</td>
												<td>Lina</td>
												<td>16/05/2024</td>
												<td>Pick Up</td>
												<td>11:00 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>08</td>
												<td>Honda Amaze</td>
												<td>AB-GB-1188</td>
												<td>Liyo</td>
												<td>Perry</td>
												<td>24/03/2024</td>
												<td>Pick Up</td>
												<td>08:15 AM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>09</td>
												<td>XUV 700</td>
												<td>AB-GB-1158</td>
												<td>Jone</td>
												<td>Sima</td>
												<td>30/03/2024</td>
												<td>Drop</td>
												<td>10:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>10</td>
												<td>Maruti Dzire</td>
												<td>AB-GB-9911</td>
												<td>Jone</td>
												<td>Jiya</td>
												<td>25/03/2024</td>
												<td>Drop</td>
												<td>10:20 PM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>11</td>
												<td>Volkswagen Virtus</td>
												<td>AB-GB-1112</td>
												<td>Jone</td>
												<td>Soya</td>
												<td>26/03/2024</td>
												<td>Pick Up</td>
												<td>09:15 AM</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-booking"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button> 
												</td>
											</tr>
										@endif
									</tbody>
                                </table>
                            </div>
                        </div>
                  </div>
                </div><!-- Row End -->
			</div>

	<!-- Add Cab Booking Modal -->
	<div class="modal fade" id="add-booking" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Add Cab Booking</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('cabs.bookings.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label text-muted">Cab / Vehicle *</label>
						<select name="cab_id" class="form-select" required>
							<option value="" selected disabled>Choose Vehicle</option>
							@if(isset($cabs))
								@foreach($cabs as $c)
									<option value="{{ $c->id }}">{{ $c->car_name }} ({{ $c->car_number }}) - {{ $c->car_type }}</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Customer Name *</label>
						<input type="text" name="customer_name" class="form-control" required placeholder="Guest / Customer Full Name">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Customer Phone</label>
						<input type="text" name="customer_phone" class="form-control" placeholder="+880 1700-000000">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Assign Driver</label>
						<input type="text" name="driver_name" class="form-control" placeholder="Driver Name (e.g. Rahim)">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Transportation Type *</label>
						<select name="transport_type" class="form-select" required>
							<option value="Pick Up" selected>Pick Up</option>
							<option value="Drop">Drop</option>
							<option value="Sightseeing">Sightseeing</option>
							<option value="Full Day">Full Day</option>
						</select>
					</div>
					<div class="row g-2 mb-3">
						<div class="col-6">
							<label class="form-label text-muted">Booking Date</label>
							<input type="date" name="booking_date" class="form-control" value="{{ date('Y-m-d') }}">
						</div>
						<div class="col-6">
							<label class="form-label text-muted">Transportation Time</label>
							<input type="time" name="booking_time" class="form-control" value="08:30">
						</div>
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Fare Amount (৳)</label>
						<input type="number" step="0.01" name="fare_amount" class="form-control" placeholder="0.00">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Charge to Guest Room (Stay Folio)</label>
						<select name="reservation_id" class="form-select">
							<option value="">-- Direct Cash / Not Charged to Room --</option>
							@if(isset($reservations))
								@foreach($reservations as $res)
									<option value="{{ $res->id }}">Room {{ $res->room?->room_number ?? 'N/A' }} - {{ $res->guest?->full_name ?? 'Guest' }} ({{ $res->reservation_number }})</option>
								@endforeach
							@endif
						</select>
						<small class="text-muted">If selected, the cab fare will be automatically posted to this guest's stay invoice folio.</small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
		</div>
	</div>  

	<!-- Edit Cab Booking Modal -->
	<div class="modal fade" id="edit-booking" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Edit Cab Booking</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car</label>
						<select class="form-select" id="edit_car_select">
							<option selected>Innova</option>
							<option value="1">Innova</option>
							<option value="2">XUV 700</option>
							<option value="3">Maruti 800</option>
						</select>
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Customer Name</label>
						<input type="text" class="form-control" id="edit_customer_name" value="Jemmy">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Driver</label>
						<input type="text" class="form-control" id="edit_driver_name" value="Max">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Transportation Type</label>
						<select class="form-select" id="edit_transport_type">
							<option selected>Pick Up</option>
							<option value="1">Pick Up</option>
							<option value="2">Drop</option>
						</select>
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Transportation Time</label>
						<input type="time" class="form-control" id="edit_transport_time">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Update changes</button>
			</div>
		</div>
		</div>
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

    $(document).on('click', '.btn-edit-booking', function() {
        const customer = $(this).data('customer');
        const driver = $(this).data('driver');
        const type = $(this).data('type');
        const time = $(this).data('time');

        $('#edit_customer_name').val(customer);
        $('#edit_driver_name').val(driver);
        $('#edit_transport_type').val(type);
        $('#edit_transport_time').val(time);
    });
});
</script>
@endpush
