@extends('layouts.app')

@section('title', 'Employees Add | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Add Employees">Employees Add</li>
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

				<h3 class="fw-bold">Add Employees</h3>
				<div class="card p-4">
					<form class="row g-3">
						<div class="col-lg-6 col-12">
							<label class="form-label text-muted">Employee Name</label>
							<input type="text" class="form-control form-control-lg" placeholder="">
						</div>
						<div class="col-lg-6 col-12">
							<label class="form-label text-muted">Employee Company</label>
							<input type="text" class="form-control form-control-lg" placeholder="">
						</div>
						<div class="col-12">
							<label class="form-label text-muted">Employee Profile</label>
							<input class="form-control" type="file">
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6">
							<label class="form-label text-muted">Employee ID</label>
							<input type="text" class="form-control form-control-lg" placeholder="User ID">
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6">
							<label class="form-label text-muted">Joining Date</label>
							<input type="date" class="form-control form-control-lg">
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6">
							<label class="form-label text-muted">User Name</label>
							<input type="text" class="form-control form-control-lg" placeholder="User Name">
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6">
							<label class="form-label text-muted">Password</label>
							<input type="Password" class="form-control form-control-lg" placeholder="Password">
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6">
							<label class="form-label text-muted">Email ID</label>
							<input type="email" class="form-control form-control-lg" placeholder="Email">
						</div>
						<div class="col-xl-3 col-lg-4 col-md-6">
							<label class="form-label text-muted">Phone</label>
							<input type="number" class="form-control form-control-lg" placeholder="Phone number">
						</div>
						<div class="col-xl-3 col-lg-6 col-sm-6">
							<label  class="form-label text-muted">Department</label>
							<select class="form-select form-control-lg" aria-label="Default select Project Category">
								<option selected>Housekeeping Staff</option>
								<option value="1">It Management</option>
								<option value="2">Marketing</option>
							</select>
						</div>
						<div class="col-xl-3 col-lg-6 col-sm-6">
							<label  class="form-label text-muted">Designation</label>
							<select class="form-select form-control-lg" aria-label="Default select Project Category">
								<option selected>Master Safe</option>
								<option value="1">BarTender</option>
								<option value="2">Manager</option>
								<option value="3">BarTender</option>
								<option value="4">Housekeeping Staff</option>
								<option value="5">Receptionist</option>
								<option value="6">HR Manager</option>
								<option value="7">BarTender</option>
								<option value="10">Other</option>
							</select>
						</div>
						<div class="col-12">
							<label class="form-label text-muted">Description (optional)</label>
							<textarea class="form-control" rows="3" placeholder="Add any extra details about the request"></textarea>
						</div> 
						<div class="col-12">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>
				<button type="button" class="btn btn-primary">Create</button>
						</div>
					</form><!-- Row End -->
				</div>

			</div>
@endsection

