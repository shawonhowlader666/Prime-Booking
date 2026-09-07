@extends('layouts.app')

@section('title', 'Role Permission | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Role Permission">Role Permission</li>
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
							<h3 class="fw-bold mb-0">Role Permission</h3>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="card">
							<div class="card-body">
								<form action="{{ route('admin.role.permission.save') }}" method="POST">
									@csrf
									<div class="row g-3">
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Role Name <span class="text-danger">*</span></label>
												<input type="text" name="role_name" class="form-control form-control-lg" placeholder="Role Name" value="Manager" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label text-muted">Description</label>
												<textarea name="description" class="form-control">Full access to manage staff duty shifts, room occupancy, and billing.</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5 class="theme-text-color2">Duty & Operational Permissions</h5>
											<div class="table-responsive">
												<table class="table table-striped custom-table mb-0">
													<thead>
														<tr>
															<th>Project Permission</th>
															<th class="text-center">Read</th>
															<th class="text-center">Write</th>
															<th class="text-center">Create</th>
															<th class="text-center">Delete</th>
														</tr>
													</thead>
													<tbody>
														@php
															$permissions = [
																'Shift Management',
																'Shift List',
																'Roster Assign',
																'Roster List',
																'Attendance Dashboard',
																'Rooms & Occupancy',
																'Housekeeping Operations',
																'Financial Transactions',
																'Inventory Stock Control',
															];
														@endphp
														@foreach($permissions as $perm)
															<tr>
																<td class="fw-bold">{{ $perm }}</td>
																<td class="text-center">
																	<input class="form-check-input" type="checkbox" name="permissions[{{ $perm }}][read]" checked>
																</td>
																<td class="text-center">
																	<input class="form-check-input" type="checkbox" name="permissions[{{ $perm }}][write]" checked>
																</td>
																<td class="text-center">
																	<input class="form-check-input" type="checkbox" name="permissions[{{ $perm }}][create]" checked>
																</td>
																<td class="text-center">
																	<input class="form-check-input" type="checkbox" name="permissions[{{ $perm }}][delete]">
																</td>
															</tr>
														@endforeach
													</tbody>
												</table>
											</div>
										</div>
										<div class="col-12 text-end mt-4">
											<button type="submit" class="btn btn-primary px-4">Save Permissions</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
@endsection
