@extends('layouts.app')

@section('title', 'All Employees | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
	<ol class="breadcrumb mb-0 bg-transparent">
		<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" title="home">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page" title="Employees">All Employee</li>
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
		<div class="col-12">
			<div class="px-0 d-flex align-items-center justify-content-between">
				<h3 class="fw-bold flex-fill mb-0">All Employee</h3>
				<a href="{{ route('hrms.employees.add') }}" class="btn btn-dark me-1 mt-1 w-sm-100">Add Employee</a>
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle mt-1 w-sm-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">Status</button>
					<ul class="dropdown-menu dropdown-menu-end p-2 p-xl-3 shadow rounded-4" >
						<li><a class="dropdown-item rounded-pill" href="#">All</a></li>
						<li><a class="dropdown-item rounded-pill" href="#">Task Assign Members</a></li>
						<li><a class="dropdown-item rounded-pill" href="#">Not Assign Members</a></li>
					</ul>
				</div>
			</div>
        </div>
		@forelse($employees ?? [] as $index => $emp)
        <div class="col-lg-6 col-md-12">
            <div class="card p-lg-3">
                <div class="card-body d-flex flex-column flex-sm-row text-center text-sm-start">
                    <div class="profile-av text-center">
                        <img src="/assets/images/lg/avatar{{ ($index % 8) + 1 }}.jpg" alt="" class="avatar xl rounded-circle img-thumbnail shadow">
                    </div>
                    <div class="flex-grow-1 ms-0 ms-sm-4">
                        <h5 class="mb-1 mt-2">{{ $emp->name }}</h5>
                        <p class="text-muted mb-0 small">{{ $emp->designation ?? $emp->role }} - {{ $emp->department->name ?? 'General Staff' }}</p>
						<p>{{ $emp->email }} | {{ $emp->phone ?? '+880 1711-000000' }}</p>
                        <a class="btn btn-sm btn-dark" href="{{ route('profile') }}">View Profile</a>
                    </div>
                </div>
            </div>
        </div>
		@empty
        <div class="col-lg-6 col-md-12">
            <div class="card p-lg-3">
                <div class="card-body d-flex flex-column flex-sm-row text-center text-sm-start">
                    <div class="profile-av text-center">
                        <img src="/assets/images/lg/avatar1.jpg" alt="" class="avatar xl rounded-circle img-thumbnail shadow">
                    </div>
                    <div class="flex-grow-1 ms-0 ms-sm-4">
                        <h5 class="mb-1 mt-2">Luke Short</h5>
                        <p class="text-muted mb-0 small">Master Safe</p>
						<p>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices.</p>
                        <a class="btn btn-sm btn-dark" href="employee-profile.html">View Profile</a>
                    </div>
                </div>
            </div>
        </div>
		@endforelse
    </div>
</div>
@endsection
