@extends('layouts.app')

@section('title', 'Universal Search | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Search">Search</li>
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
					<div class="col-12">
						<form action="{{ route('search') }}" method="GET" class="input-group input-group-lg mb-3">
							<input type="search" name="q" class="form-control" placeholder="Search guest name, booking number, room or folio..." value="{{ $query ?? '' }}">
							<button class="btn btn-primary" type="submit">Search PMS</button>
						</form>
					</div>
					<div class="col-12">
						<ul class="nav nav-tabs tab-heading li_animate" role="tablist" style="--dynamic-color: var(--theme-color1);">
							<li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#clasic_search">Classic Results</a></li>
							<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#articles_search">Articles & Guides</a></li>
							<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#photos_search">Property Photos</a></li>
						</ul>
					</div>
					<div class="col-12">
						<h5 class="mb-0">Search results</h5>
						<small class="text-muted">About <span class="text-orange">{{ isset($results['guests']) ? count($results['guests']) + count($results['reservations']) : 5 }}</span> results found</small>
					</div>
					<div class="col-12">
						<div class="tab-content mb-4">
							<div class="tab-pane show active" id="clasic_search">
								<ul class="list-group li_animate">
									@if(isset($results['reservations']) && count($results['reservations']) > 0)
										@foreach($results['reservations'] as $res)
											<li class="list-group-item py-3">
												<h5 class="fw-normal"><a href="{{ route('room.bookings.list') }}">Reservation #{{ $res->reservation_code }} (Room {{ $res->room?->room_number ?? 'N/A' }})</a></h5>
												<p class="text-muted">Status: {{ $res->status }} | Arrival: {{ $res->check_in_date->format('M d, Y') }} - Departure: {{ $res->check_out_date->format('M d, Y') }} | Total: ${{ number_format($res->total_amount, 2) }}</p>
												<ul class="list-inline small">
													<li class="list-inline-item"><span class="badge bg-primary">Reservation</span></li>
													<li class="list-inline-item text-muted">{{ $res->created_at->format('M d, Y') }}</li>
												</ul>
											</li>
										@endforeach
									@endif

									@if(isset($results['guests']) && count($results['guests']) > 0)
										@foreach($results['guests'] as $g)
											<li class="list-group-item py-3">
												<h5 class="fw-normal"><a href="{{ route('guests') }}">Guest Profile: {{ $g->full_name }}</a></h5>
												<p class="text-muted">Email: {{ $g->email }} | Phone: {{ $g->phone ?? 'N/A' }} | Nationality: {{ $g->nationality ?? 'N/A' }}</p>
												<ul class="list-inline small">
													<li class="list-inline-item"><span class="badge bg-success">Guest</span></li>
													<li class="list-inline-item text-muted">{{ $g->created_at->format('M d, Y') }}</li>
												</ul>
											</li>
										@endforeach
									@endif

									@if(!isset($results['reservations']) || (count($results['reservations']) === 0 && count($results['guests']) === 0))
										<li class="list-group-item py-3">
											<h5 class="fw-normal"><a href="{{ route('rooms.list') }}">Deluxe Ocean Suite Master Configuration</a></h5>
											<p class="text-muted">High-concurrency room inventory with real-time housekeeping matrix and smart lock digital key access.</p>
											<ul class="list-inline small">
												<li class="list-inline-item"><span class="badge bg-secondary">Room Inventory</span></li>
												<li class="list-inline-item text-muted">{{ now()->format('M d, Y') }}</li>
											</ul>
										</li>
										<li class="list-group-item py-3">
											<h5 class="fw-normal"><a href="{{ route('hrms.employees') }}">HR & Staff Shift Roster Management</a></h5>
											<p class="text-muted">Daily biometric attendance, automated payroll payslips, and dynamic duty schedules.</p>
											<ul class="list-inline small">
												<li class="list-inline-item"><span class="badge bg-success">Staff Management</span></li>
												<li class="list-inline-item text-muted">{{ now()->format('M d, Y') }}</li>
											</ul>
										</li>
									@endif
								</ul>
							</div>
							<div class="tab-pane" id="articles_search">
								<div class="row g-3 row-deck">
									<div class="col-lx-3 col-lg-4 col-md-6">
										<div class="card">
											<img class="card-img-top" src="assets/images/gallery/16.jpg" alt="Card image cap">
											<div class="card-body">
												<span class="badge theme-color1" style="position: absolute; top: 1rem;">Operations</span>
												<h5 class="fw-light mb-2"><a href="#">Night Audit and Tariff Balancing Procedures</a></h5>
												<p>Complete step-by-step breakdown of automated night audits, room charges, and tax reconciliation...</p>
												<small class="text-muted">Updated today</small>
											</div>
										</div>
									</div>
									<div class="col-lx-3 col-lg-4 col-md-6">
										<div class="card">
											<img class="card-img-top" src="assets/images/gallery/17.jpg" alt="Card image cap">
											<div class="card-body">
												<span class="badge theme-color2" style="position: absolute; top: 1rem;">Security</span>
												<h5 class="fw-light mb-2"><a href="#">PCI-DSS Compliance and Payment Gateway Security</a></h5>
												<p>Stripe & PayPal multi-gateway tokenization rules for secure hospitality transactions...</p>
												<small class="text-muted">Updated today</small>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="photos_search">
								<div class="row g-3">
									<div class="col-md-4 col-sm-6">
										<img src="assets/images/gallery/1.jpg" class="img-fluid rounded" alt="Property 1">
									</div>
									<div class="col-md-4 col-sm-6">
										<img src="assets/images/gallery/2.jpg" class="img-fluid rounded" alt="Property 2">
									</div>
									<div class="col-md-4 col-sm-6">
										<img src="assets/images/gallery/3.jpg" class="img-fluid rounded" alt="Property 3">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
@endsection
