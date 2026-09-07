@extends('layouts.app')

@section('title', 'Common Settings | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Common Settings">Common Settings</li>
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
				<div class="row mb-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Common Settings</h3>
						</div>
					</div>
				</div>
				<div class="card border-0 mb-3">
					<div class="card-header">
						<h6 class="card-title mb-0">Filled Hotel Settings</h6>
					</div>
					<div class="card-body pt-0">
						<form action="{{ route('settings.common.save') }}" method="POST">
							@csrf
							<div class="row g-3">
								<div class="col-md-6">
									<label class="col-form-label">Site Logo</label>
									<input type="file" class="form-control">
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Site Favicon</label>
									<input type="file" class="form-control">
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Property Name / Site Title</label>
									<input type="text" name="name" class="form-control" value="{{ $hotel->name ?? 'HotelAir Luxury Resort' }}" required>
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Support Email</label>
									<input type="email" name="email" class="form-control" value="{{ $hotel->email ?? 'support@hotelair.com' }}">
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Support Phone Number</label>
									<input type="text" name="phone" class="form-control" value="{{ $hotel->phone ?? '+1 (555) 234-5678' }}">
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Property Code / Prefix</label>
									<input type="text" class="form-control" value="{{ $hotel->code ?? 'HA-01' }}" readonly>
								</div>
								<div class="col-md-12">
									<label class="col-form-label">Physical Address</label>
									<textarea name="address" class="form-control" rows="3">{{ $hotel->address ?? '742 Evergreen Terrace, Luxury Avenue, Ocean City' }}</textarea>
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Short Description</label>
									<textarea class="form-control" rows="3">Prime PMS 5-Star Luxury Resort & Hospitality Suites.</textarea>
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Copyright Details</label>
									<textarea class="form-control" rows="3">© {{ date('Y') }} HotelAir Hospitality Systems. All Rights Reserved.</textarea>
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Map Iframe</label>
									<textarea class="form-control" rows="3"><iframe src="https://maps.google.com"></iframe></textarea>
								</div>
								<div class="col-md-6">
									<label class="col-form-label">Meta Keywords</label>
									<textarea class="form-control" rows="3">hotel, pms, luxury suite, resort, booking, room reservation</textarea>
								</div>
								<div class="col-md-12">
									<label class="col-form-label">Meta Description</label>
									<textarea class="form-control" rows="3">Next-Generation Multi-Tenant Hotel Property Management System.</textarea>
								</div>
								<div class="col-md-12">
									<button type="reset" class="btn btn-warning mt-2">Reset</button>
									<button type="submit" class="btn btn-primary mt-2">Submit</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
@endsection
