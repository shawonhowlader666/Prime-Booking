@extends('layouts.app')

@section('title', 'Hotels Multi-Property Hub | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Hotels">Hotels</li>
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
							<h3 class="fw-bold mb-0">Hotels & Properties Directory</h3>
						</div>
					</div>
				</div>
				<ul class="row g-2 list-unstyled li_animate row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-1 row-deck mb-lg-5 mb-4">
					<li class="col">
						<a class="card hr-arrow p-4" href="room-booking.html" title="room-booking" style="--dynamic-color: var(--theme-color1);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M14 12v.01"></path>
								<path d="M3 21h18"></path>
								<path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16"></path>
							</svg>
							<h6 class="title mb-1">Room Book</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Front-desk walk-in and reservation engine</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="room-list.html" title="room-list" style="--dynamic-color: var(--theme-color2);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M14 12v.01"></path>
								<path d="M3 21h18"></path>
								<path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16"></path>
							</svg>
							<h6 class="title mb-1">Room List</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Complete room inventory and classification</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="room-status.html" title="room-status" style="--dynamic-color: var(--theme-color6);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M14 12v.01"></path>
								<path d="M3 21h18"></path>
								<path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16"></path>
							</svg>
							<h6 class="title mb-1">Room Status</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Live occupancy matrix and cleaning states</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="room-image.html" title="room-image" style="--dynamic-color: var(--theme-color3);">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="mb-3"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M11.5 21h-5.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v7" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l.5 .5" /><path d="M15 19l2 2l4 -4" /></svg>
							<h6 class="title mb-1">Room Image</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Property photography & media gallery</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="room-facilites-list.html" title="room-facilites-list" style="--dynamic-color: var(--theme-color2);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M12 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M7 3m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
								<path d="M12 3v2"></path>
								<path d="M10 15v.01"></path>
								<path d="M10 18v.01"></path>
								<path d="M14 18v.01"></path>
								<path d="M14 15v.01"></path>
							</svg>
							<h6 class="title mb-1">Room Facilities</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">High-end guest amenities & master configurations</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="cab-booking.html" title="Cab Booking" style="--dynamic-color: var(--theme-color5);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
								<path d="M6 10h15"></path>
							</svg>
							<h6 class="title mb-1">Cab Booking</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Airport shuttles & guest transfer dispatch</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="{{ route('admin.tenants.index') }}" title="SaaS Master Tenants" style="--dynamic-color: var(--theme-color1);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M3 21l18 0"></path>
								<path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"></path>
								<path d="M9 9l1 0"></path>
								<path d="M9 12l1 0"></path>
								<path d="M9 15l1 0"></path>
								<path d="M14 9l1 0"></path>
								<path d="M14 12l1 0"></path>
								<path d="M14 15l1 0"></path>
							</svg>
							<h6 class="title mb-1">SaaS Master Control</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Manage 10,000+ properties, licenses & trials</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
				</ul>
			</div>
@endsection
