@extends('layouts.app')

@section('title', 'Cab List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Cab List">Cab List</li>
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
							<form method="GET" action="{{ route('cabs.index') }}">
								<div class="row g-3 mt-3">
									<div class="col-12">
										<label class="form-label small text-muted">Sorted by:</label>
										<input type="search" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
									</div>
									<div class="col-12 text-end mt-4">
										<a href="{{ route('cabs.index') }}" class="btn btn-sm btn-light me-1">Reset</a>
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
							<h3 class="fw-bold mb-0">Cab List</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-items">Add Cab</button>
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
											<th>Car Type</th>
											<th>Fuel Type</th>
											<th>Seat Capacity</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@if(isset($cabs) && $cabs->count() > 0)
											@foreach($cabs as $index => $cab)
												<tr>
													<td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
													<td><strong>{{ $cab->car_name }}</strong></td>
													<td><span class="badge bg-light text-dark border">{{ $cab->car_number }}</span></td>
													<td>{{ $cab->car_type }}</td>
													<td>{{ $cab->fuel_type }}</td>
													<td>{{ $cab->seat_capacity }}</td>
													<td>
														<button type="button" class="btn btn-edit-cab" 
															data-id="{{ $cab->id }}"
															data-name="{{ $cab->car_name }}"
															data-number="{{ $cab->car_number }}"
															data-type="{{ $cab->car_type }}"
															data-fuel="{{ $cab->fuel_type }}"
															data-capacity="{{ $cab->seat_capacity }}"
															data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
														<form action="{{ route('cabs.destroy', $cab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this cab?');">
															@csrf
															@method('DELETE')
															<button type="submit" class="btn deleterow p-0 border-0 bg-transparent"><i class="bi bi-trash text-danger"></i></button>
														</form>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>01</td>
												<td>Innova</td>
												<td>AB-GB-0158</td>
												<td>MPV</td>
												<td>Disel</td>
												<td>8</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>02</td>
												<td>XUV 700</td>
												<td>AB-GB-1158</td>
												<td>SUV</td>
												<td>Disel</td>
												<td>5</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>03</td>
												<td>Maruti 800</td>
												<td>AB-GB-5511</td>
												<td>Sedan</td>
												<td>Petrol</td>
												<td>4</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>04</td>
												<td>Hyundai Verna</td>
												<td>AB-GB-7711</td>
												<td>Sedan</td>
												<td>Petrol</td>
												<td>4</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>05</td>
												<td>Maruti Dzire</td>
												<td>AB-GB-9911</td>
												<td>Sedan</td>
												<td>Petrol</td>
												<td>4</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>06</td>
												<td>Honda Amaze</td>
												<td>AB-GB-1188</td>
												<td>Sedan</td>
												<td>Petrol</td>
												<td>4</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>07</td>
												<td>Volkswagen Virtus</td>
												<td>AB-GB-1112</td>
												<td>Sedan</td>
												<td>Petrol</td>
												<td>5</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items"><i class="bi bi-pencil-square"></i></button>
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

	<!-- Add Cab Modal -->
	<div class="modal fade" id="add-items" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Add Cab List</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('cabs.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car *</label>
						<input type="text" name="car_name" class="form-control" required placeholder="Add Car (e.g. Toyota Hiace, Innova)">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car Number *</label>
						<input type="text" name="car_number" class="form-control" required placeholder="Add Car Number (e.g. DHAKA-GA-11-2233)">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car Type *</label>
						<input type="text" name="car_type" class="form-control" required placeholder="Add Car Type (e.g. MPV, SUV, Sedan, Microbus)">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Fuel Type *</label>
						<input type="text" name="fuel_type" class="form-control" required placeholder="Add Fuel Type (e.g. Diesel, Petrol, Octane, Hybrid)">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Seat Capacity *</label>
						<input type="number" name="seat_capacity" class="form-control" required value="4" placeholder="Seat Capacity">
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

	<!-- Edit Cab Modal -->
	<div class="modal fade" id="edit-items" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Edit Cab List</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="editCabForm" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car</label>
						<input type="text" name="car_name" id="edit_car_name" class="form-control" value="Innova">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car Number</label>
						<input type="text" name="car_number" id="edit_car_number" class="form-control" value="AB-GB-0158">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Car Type</label>
						<input type="text" name="car_type" id="edit_car_type" class="form-control" value="MPV">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Fuel Type</label>
						<input type="text" name="fuel_type" id="edit_fuel_type" class="form-control" value="Disel">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Seat Capacity</label>
						<input type="number" name="seat_capacity" id="edit_seat_capacity" class="form-control" value="8">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Update changes</button>
				</div>
			</form>
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

    $(document).on('click', '.btn-edit-cab', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const number = $(this).data('number');
        const type = $(this).data('type');
        const fuel = $(this).data('fuel');
        const capacity = $(this).data('capacity');

        $('#editCabForm').attr('action', '/cabs/' + id);
        $('#edit_car_name').val(name);
        $('#edit_car_number').val(number);
        $('#edit_car_type').val(type);
        $('#edit_fuel_type').val(fuel);
        $('#edit_seat_capacity').val(capacity);
    });
});
</script>
@endpush
