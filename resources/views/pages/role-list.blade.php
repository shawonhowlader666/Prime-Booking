@extends('layouts.app')

@section('title', 'Role List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Role List">Role List</li>
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
							<h3 class="fw-bold mb-0">Role List</h3>
							<div class="col-auto d-flex w-sm-100">
								<a href="{{ route('admin.role.add') }}" class="btn btn-dark">Add Role User</a>
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
											<th>Image</th>
											<th>User Name</th>
											<th>Email</th>
											<th>Role</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										@if(isset($users) && $users->count() > 0)
											@foreach($users as $u)
												<tr>
													<td>{{ $u->id }}</td>
													<td>
														<img src="assets/images/xs/avatar{{ ($u->id % 9) + 1 }}.jpg" class="img-fluid avatar md rounded" alt="{{ $u->name }}">
													</td>
													<td><strong>{{ $u->name }}</strong></td>
													<td>{{ $u->email }}</td>
													<td><span class="badge bg-secondary">{{ $u->role }}</span></td>
													<td>
														<span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">
															{{ $u->is_active ? 'Active' : 'Inactive' }}
														</span>
													</td>
													<td>
														<form action="{{ route('admin.user.toggle', $u->id) }}" method="POST" style="display:inline;">
															@csrf
															<button type="submit" class="btn btn-sm {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Toggle Status">
																<i class="bi {{ $u->is_active ? 'bi-person-slash' : 'bi-person-check' }}"></i>
															</button>
														</form>
														<a href="{{ route('admin.role.assign') }}" class="btn btn-sm btn-link" title="Assign Role"><i class="bi bi-pencil-square"></i></a>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>1</td>
												<td>
													<img src="assets/images/xs/avatar1.jpg" class="img-fluid avatar md rounded" alt="">
												</td>
												<td>Jone Pit</td>
												<td>jone123@gmail.com</td>
												<td><span class="badge bg-secondary">Manager</span></td>
												<td>Active</td>
												<td>
													<a href="{{ route('admin.role.add') }}" class="btn"><i class="bi bi-pencil-square"></i></a>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
										@endif
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
