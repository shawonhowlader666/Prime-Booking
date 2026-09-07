@extends('layouts.app')

@section('title', 'Personalised Settings | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item"><a href="personalised.html" title="Personalised">Personalised</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="personalised-settings">Personalised-settings</li>
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
				<div class="row g-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Personalised Settings</h3>
						</div>
				  </div>
                  <div class="col-sm-12">
						<div class="card mb-3">
							<div class="card-header">
								<h6 class="card-title">Personalised Service Add</h6>
							</div>
							<div class="card-body">
								<form>
									<div class="row g-3">
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Service Name</label>
												<input type="text" class="form-control"  placeholder="Add Service Name">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Service Time Slot</label>
												<div class="input-group">
													<input type="time" class="form-control">
													<input type="time" class="form-control">
													<button class="btn btn-outline-secondary" type="button"><svg xmlns="http://www.w3.org/2000/svg" class="svg-stroke" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg></button>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Category One</label>
												<input type="text" class="form-control"  placeholder="Add Category">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Category Two</label>
												<input type="text" class="form-control"  placeholder="Add Category">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Category Three</label>
												<input type="text" class="form-control"  placeholder="Add Category">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted d-block">Add New</label>
												<button type="button" class="btn btn-primary">Add Category</button>
											</div>
										</div>
										<div class="col-lg-12 text-end">
											<button type="button" class="btn btn-primary">Apply</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="card mb-3">
							<div class="card-header">
								<h6 class="card-title">List Personalised Service</h6>
							</div>
							<div class="card-body">
								<table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
									<thead>
										<tr>
											<th>Service Id</th>
											<th>Service Name</th>
											<th>Category</th>
											<th>Time Slot</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>01</td>
											<td>Jacuzzi</td>
											<td >
												<div class="p-1 border">
													Outdoor Jacuzzi
												</div>
												<div class="p-1 border">
													Luxury Jacuzzi
												</div>
												<div class="p-1 border">
													Portable Jacuzzi
												</div>
											</td>
											<td >
												<div class="p-1 border">
													10 to 12
												</div>
												<div class="p-1 border">
													12 to 02
												</div>
												<div class="p-1 border">
													04 to 06
												</div>
											</td>
											<td>
												<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-service"><i class="bi bi-pencil-square"></i></button>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>02</td>
											<td>Swimming Pool</td>
											<td >
												<div class="p-1 border">
													Swimming Lessons
												</div>
												<div class="p-1 border">
													Water Aerobics
												</div>
												<div class="p-1 border">
													Competitive Swimming
												</div>
											</td>
											<td >
												<div class="p-1 border">
													10 to 12
												</div>
												<div class="p-1 border">
													12 to 02
												</div>
												<div class="p-1 border">
													04 to 06
												</div>
											</td>
											<td>
												<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-service"><i class="bi bi-pencil-square"></i></button>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>03</td>
											<td>Spa</td>
											<td >
												<div class="p-1 border">
													Massage Therapy
												</div>
												<div class="p-1 border">
													Facial Treatments
												</div>
												<div class="p-1 border">
													Aromatherapy
												</div>
											</td>
											<td >
												<div class="p-1 border">
													10 to 12
												</div>
												<div class="p-1 border">
													12 to 02
												</div>
												<div class="p-1 border">
													04 to 06
												</div>
											</td>
											<td>
												<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-service"><i class="bi bi-pencil-square"></i></button>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>04</td>
											<td>Pedicure</td>
											<td >
												<div class="p-1 border">
													Classic Pedicures
												</div>
												<div class="p-1 border">
													Gel Pedicures
												</div>
												<div class="p-1 border">
													French Pedicures
												</div>
											</td>
											<td >
												<div class="p-1 border">
													10 to 12
												</div>
												<div class="p-1 border">
													12 to 02
												</div>
												<div class="p-1 border">
													04 to 06
												</div>
											</td>
											<td>
												<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-service"><i class="bi bi-pencil-square"></i></button>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>05</td>
											<td>Theater</td>
											<td >
												<div class="p-1 border">
													Movie
												</div>
												<div class="p-1 border">
													Match
												</div>
												<div class="p-1 border">
													Auction
												</div>
											</td>
											<td >
												<div class="p-1 border">
													10 to 01
												</div>
												<div class="p-1 border">
													02 to 05
												</div>
												<div class="p-1 border">
													06 to 09 
												</div>
											</td>
											<td>
												<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-service"><i class="bi bi-pencil-square"></i></button>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
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
