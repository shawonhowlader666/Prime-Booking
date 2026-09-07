@extends('layouts.app')

@section('title', 'House Keeping Qr-List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="House Keeping Qr-List">House Keeping Qr-List</li>
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
							<h3 class="fw-bold mb-0">House Keeping Qr-List</h3>
						</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">
									@if(isset($rooms) && $rooms->count() > 0)
										@foreach($rooms as $room)
											<div class="col-md-4 col-lg-3">
												<div class="room-select border p-3 rounded text-center">
													<img src="/assets/images/qr.png" class="img-fluid mb-2" alt="QR Code" style="max-height: 140px;">
													<div>
														<span class="fw-bold d-block text-primary fs-6">Room No.{{ $room->room_number }}</span>
														<small class="text-muted d-block">{{ $room->roomType?->name ?? 'Standard' }}</small>
														<span class="badge mt-1 {{ $room->cleanliness === 'CLEAN' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $room->cleanliness ?? 'CLEAN' }}</span>
													</div>
												</div>
											</div>
										@endforeach
									@else
										<div class="col-md-4 col-lg-3">
											<div class="room-select border p-3 rounded">
												<img src="/assets/images/qr.png" class="img-fluid" alt="">
												<div class="text-center">
													<span class="fw-bold d-block text-primary">Room No.101</span>
												</div>
											</div>
										</div>
										<div class="col-md-4 col-lg-3">
											<div class="room-select border p-3 rounded">
												<img src="/assets/images/qr.png" class="img-fluid" alt="">
												<div class="text-center">
													<span class="fw-bold d-block text-primary">Room No.102</span>
												</div>
											</div>
										</div>
										<div class="col-md-4 col-lg-3">
											<div class="room-select border p-3 rounded">
												<img src="/assets/images/qr.png" class="img-fluid" alt="">
												<div class="text-center">
													<span class="fw-bold d-block text-primary">Room No.103</span>
												</div>
											</div>
										</div>
										<div class="col-md-4 col-lg-3">
											<div class="room-select border p-3 rounded">
												<img src="/assets/images/qr.png" class="img-fluid" alt="">
												<div class="text-center">
													<span class="fw-bold d-block text-primary">Room No.104</span>
												</div>
											</div>
										</div>
									@endif
								</div>
                            </div>
                        </div>
                  </div>
                </div><!-- Row End -->
			</div>
@endsection
