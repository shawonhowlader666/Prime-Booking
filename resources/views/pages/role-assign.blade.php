@extends('layouts.app')

@section('title', 'Role Assign | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Role Assign">Role Assign</li>
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
							<h3 class="fw-bold mb-0">Role Assign</h3>
						</div>
					</div>
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								<form action="{{ route('admin.role.assign.save') }}" method="POST">
									@csrf
									<div class="row g-3">
										<div class="col-md-12">
											<div class="form-group">
												<label class="form-label text-muted">User Name <span class="text-danger">*</span></label>
												<select name="user_id" class="form-control" required>
													@if(isset($users))
														@foreach($users as $u)
															<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
														@endforeach
													@endif
												</select>
											</div>
										</div>
										<div class="col-md-12">
											<label class="form-label text-muted d-block">Role *</label>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="role" id="inlineRadio1" value="RECEPTIONIST" checked>
												<label class="form-check-label" for="inlineRadio1">Booking / Receptionist</label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="role" id="inlineRadio2" value="HOUSEKEEPER">
												<label class="form-check-label" for="inlineRadio2">Housekeeper</label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="role" id="inlineRadio3" value="MANAGER">
												<label class="form-check-label" for="inlineRadio3">Manager</label>
											</div>
											<div class="form-check form-check-inline">
												<input class="form-check-input" type="radio" name="role" id="inlineRadio4" value="ACCOUNTANT">
												<label class="form-check-label" for="inlineRadio4">Accountant</label>
											</div>
										</div>
										<div class="col-md-12">
											<button type="submit" class="btn btn-primary px-4">Save Assignment</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h6 class="card-title mb-0">Default Settings Permission</h6>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-striped custom-table mb-0">
										<thead>
											<tr>
												<th>Common Permission</th>
												<th class="text-center">Read</th>
												<th class="text-center">Write</th>
												<th class="text-center">Create</th>
												<th class="text-center">Delete</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="fw-bold">Password Set</td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox"></td>
											</tr>
											<tr>
												<td class="fw-bold">Commission List</td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox"></td>
											</tr>
											<tr>
												<td class="fw-bold">Booking List</td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox" checked></td>
												<td class="text-center"><input class="form-check-input" type="checkbox"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
@endsection
