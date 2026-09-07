@extends('layouts.app')

@section('title', 'Duty Roster List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Duty Roster List">Duty Roster List</li>
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
							<h3 class="fw-bold mb-0">Duty Roster List</h3>    
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-roster">Create Roster</button>
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
											<th>Shift</th>
											<th>Roster Start</th>
											<th>Roster End</th>
											<th>Roster Days</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>1</td>
											<td>Morning Shift</td>
											<td>2024-01-11</td>
											<td>2024-01-26</td>
											<td>16</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>2</td>
											<td>Night Shift</td>
											<td>2021-12-01</td>
											<td>2022-10-24</td>
											<td>28</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>3</td>
											<td>Morning Shift, Night Shift</td>
											<td>2021-11-21</td>
											<td>2021-11-30</td>
											<td>10</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>4</td>
											<td>Morning Shift, Night Shift</td>
											<td>2024-03-21</td>
											<td>2024-04-16</td>
											<td>10</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>5</td>
											<td>Morning Shift</td>
											<td>2024-04-16</td>
											<td>2024-05-16</td>
											<td>17</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>6</td>
											<td>Night Shift</td>
											<td>2024-05-16</td>
											<td>2024-06-16</td>
											<td>17</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>7</td>
											<td>Night Half</td>
											<td>2024-06-16</td>
											<td>2024-07-16</td>
											<td>27</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
										</tr>
										<tr>
											<td>8</td>
											<td>Morning Half</td>
											<td>2024-06-16</td>
											<td>2024-07-16</td>
											<td>37</td>
											<td>
												<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
											</td>
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
$(document).ready( function () {
            $('.dataTable')
            .addClass( 'nowrap' )
            .dataTable( {
                responsive: true,
            });
        });
</script>
@endpush
