@extends('layouts.app')

@section('title', 'Floor Plan List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Floor Plan List">Floor Plan List</li>
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
							<h3 class="fw-bold mb-0">Floor Plan List</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#add-floor">Add Floor</button>
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#assign-floor">Assign Floor</button>
							</div>
						</div>
				  	</div>
				  <div class="col-12">
					<div class="alert alert-primary" role="alert">
						<ol class="mb-0">
							<li>Add Floor Name</li>
							<li>Assign Floor</li>
						</ol>
					</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Floor Name</th>
											<th>Number Of Room</th>
											<th>Start Room Number</th> 
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@if(isset($floorPlans) && $floorPlans->count() > 0)
											@foreach($floorPlans as $plan)
												<tr>
													<td>{{ $plan->id }}</td>
													<td><strong>{{ $plan->floor_name }}</strong></td>
													<td>{{ $plan->number_of_rooms }}</td>
													<td>{{ $plan->start_room_number ?? '-' }}</td>
													<td>
														<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-floor"><i class="bi bi-pencil-square"></i></button>
														<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>1101</td>
												<td>Fourteenth Floor</td>
												<td>5</td>
												<td>1400</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-floor"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1102</td>
												<td>Seventh Floor</td>
												<td>5</td>
												<td>700</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-floor"><i class="bi bi-pencil-square"></i></button>
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

	<!-- Add Floor Modal -->
	<div class="modal fade" id="add-floor" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Add Floor</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form action="{{ route('room.floor_plans.store') }}" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group mb-3">
							<label class="form-label text-muted">Floor Name <span class="text-danger">*</span></label>
							<input type="text" name="floor_name" class="form-control" placeholder="e.g. First Floor, 14th Floor" required>
						</div>
						<div class="form-group mb-3">
							<label class="form-label text-muted">Floor Number</label>
							<input type="number" name="floor_number" class="form-control" value="1">
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
	
	<!-- Assign Floor Modal -->
	<div class="modal fade" id="assign-floor" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Assign Floor</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form action="{{ route('room.floor_plans.assign') }}" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group mb-3">
							<label class="form-label text-muted">Floor Select <span class="text-danger">*</span></label>
							<input type="text" name="floor_name" class="form-control" placeholder="e.g. First Floor" required>
						</div>
						<div class="form-group mb-3">
							<label class="form-label text-muted">Start Room Number <span class="text-danger">*</span></label>
							<input type="text" name="start_room_number" class="form-control" placeholder="e.g. 100, 1400" required>
						</div>
						<div class="form-group">
							<label class="form-label text-muted">Number Of Rooms <span class="text-danger">*</span></label>
							<input type="number" name="number_of_rooms" class="form-control" placeholder="e.g. 5" required>
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

	<!-- Edit Floor Modal -->
	<div class="modal fade" id="edit-floor" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Edit Floor</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form>
						<div class="form-group mb-3">
							<label class="form-label text-muted">Floor Select <span class="text-danger">*</span></label>
							<input type="text" class="form-control" value="Fourteenth Floor">
						</div>
						<div class="form-group mb-3">
							<label class="form-label text-muted">Start Room Number</label>
							<input type="text" class="form-control" value="1400">
						</div>
						<div class="form-group">
							<label class="form-label text-muted">Number Of Rooms</label>
							<input type="text" class="form-control" value="5">
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
});
</script>
@endpush
