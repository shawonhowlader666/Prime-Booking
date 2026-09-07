@extends('layouts.app')

@section('title', 'Departments | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
	<ol class="breadcrumb mb-0 bg-transparent">
		<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" title="home">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page" title="Departments">Departments</li>
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
				<h3 class="fw-bold mb-0">Departments</h3>
				<div class="col-auto d-flex w-sm-100">
					<button type="button" class="btn btn-dark btn-set-task w-sm-100" data-bs-toggle="modal" data-bs-target="#adddepartments">Add Department</button>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="card p-4">
				<table id="myProjectTable" class="table table-hover align-middle mb-0 w-100">
					<thead>
						<tr>
							<th>#</th>
							<th>Department Head</th> 
							<th>Department Name</th> 
							<th>Employee UnderWork</th>   
							<th>Actions</th>  
						</tr>
					</thead>
					<tbody>
						@forelse($departments ?? [] as $index => $dept)
						<tr>
							<td><span class="fw-bold">{{ $index + 1 }}</span></td>
							<td>
								<img class="avatar rounded-circle" src="/assets/images/xs/avatar{{ ($index % 6) + 1 }}.jpg" alt="">
								<span class="fw-bold ms-1">{{ $dept->head->name ?? 'Admin Assigned' }}</span>
							</td>
							<td>{{ $dept->name }}</td>
							<td>{{ $dept->employees_count ?? $dept->employees->count() }}</td>
							<td>
								<button type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#UserList"><i class="bi bi-eye"></i></button>
								<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#editdepartments"><i class="bi bi-pencil-square"></i></button>
								<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
							</td>
						</tr>
						@empty
						<tr>
							<td><span class="fw-bold">1</span></td>
							<td>
								<img class="avatar rounded-circle" src="/assets/images/xs/avatar1.jpg" alt="">
								<span class="fw-bold ms-1">Joan Dyer</span>
							</td>
							<td>Housekeeping Staff</td>
							<td>40</td>
							<td>
								<button type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#UserList"><i class="bi bi-eye"></i></button>
								<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#editdepartments"><i class="bi bi-pencil-square"></i></button>
								<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
    </div><!-- Row End -->
</div>

<!-- Add Department Modal from Template -->
<div class="modal fade" id="adddepartments" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Department</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('hrms.departments.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="row g-3">
						<div class="col-12">
							<label class="form-label text-muted">Department Name</label>
							<input type="text" name="name" required class="form-control form-control-lg" placeholder="Department Name">
						</div>
						<div class="col-sm-6">
							<label class="form-label text-muted">Department Code</label>
							<input type="text" name="code" class="form-control form-control-lg" placeholder="Code (e.g. HKD)">
						</div>
						<div class="col-sm-6">
							<label class="form-label text-muted">Department Head</label>
							<select name="head_user_id" class="form-select form-control-lg">
								<option value="">Select Head</option>
								@foreach($staff ?? [] as $st)
									<option value="{{ $st->id }}">{{ $st->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>
					<button type="submit" class="btn btn-primary">Add</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#myProjectTable')
    .addClass( 'nowrap' )
    .dataTable( {
        responsive: true,
    });
});
</script>
@endpush
