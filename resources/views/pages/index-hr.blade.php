@extends('layouts.app')

@section('title', 'Index Hr | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Dashboard">Dashboard</li>
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
					<div class="col-lg-12">
						<div class="card">
							<div class="card-header">
								<h6 class="mb-0 fw-bold ">Top Hiring Sources</h6>
							</div>
							<div class="card-body">
								<div id="hiringsources"></div>
							</div>
						</div>
					</div>
					<div class="col-xxl-4 col-xl-12">
						<div class="row g-3">
							<div class="col-xl-12 col-lg-6">
								<div class="card">
									<div class="card-header">
										<h6 class="mb-0 fw-bold ">Upcomming Birthday</h6>
									</div>
									<div class="card-body">
										<div class="d-flex align-items-start py-2">
											<img class="avatar lg rounded-circle img-thumbnail" src="/assets/images/lg/avatar1.jpg" alt="profile">
											<div class="ms-3 border-bottom flex-grow-1">
												<h6 class="fw-bold mb-1">Natalie Gibson</h6>
												<p class="text-muted mb-1">Master Safe</p>
												<small>Date: 12 Aug, 11:30AM</small>
											</div>
										</div>
										<div class="d-flex align-items-start py-2">
											<img class="avatar lg rounded-circle img-thumbnail" src="/assets/images/lg/avatar2.jpg" alt="profile">
											<div class="ms-3 border-bottom flex-grow-1">
												<h6 class="fw-bold mb-1">Peter Piperg</h6>
												<p class="text-muted mb-1">Sous Chef</p>
												<small>Date: 12 Aug, 11:30AM</small>
											</div>
										</div>
										<div class="d-flex align-items-start py-2">
											<img class="avatar lg rounded-circle img-thumbnail" src="/assets/images/lg/avatar3.jpg" alt="profile">
											<div class="ms-3 border-bottom flex-grow-1">
												<h6 class="fw-bold mb-1">Robert Young</h6>
												<p class="text-muted mb-1">Housekeeping Staff</p>
												<small>Date: 12 Aug, 11:30AM</small>
											</div>
										</div>
										<div class="d-flex align-items-start py-2">
											<img class="avatar lg rounded-circle img-thumbnail" src="/assets/images/lg/avatar4.jpg" alt="profile">
											<div class="ms-3 border-bottom flex-grow-1">
												<h6 class="fw-bold mb-1">Gibson Butler</h6>
												<p class="text-muted mb-1">Spa Manager</p>
												<small>Date: 12 Aug, 11:30AM</small>
											</div>
										</div>
										<div class="d-flex align-items-start py-2">
											<img class="avatar lg rounded-circle img-thumbnail" src="/assets/images/lg/avatar6.jpg" alt="profile">
											<div class="ms-3 border-bottom flex-grow-1">
												<h6 class="fw-bold mb-1">Meta Rull</h6>
												<p class="text-muted mb-1">Manager</p>
												<small>Date: 22 Aug, 11:30AM</small>
											</div>
										</div>
										<div class="d-flex align-items-start py-2">
											<img class="avatar lg rounded-circle img-thumbnail" src="/assets/images/lg/avatar1.jpg" alt="profile">
											<div class="ms-3 border-bottom flex-grow-1">
												<h6 class="fw-bold mb-1">Rull Depo</h6>
												<p class="text-muted mb-1">Bar</p>
												<small>Date: 15Aug, 10:30AM</small>
											</div>
										</div>
									</div>
                                </div>
							</div>
                        </div>
					</div>
					<div class="col-xxl-8 col-xl-12">
						<div class="row g-3 row-deck">
							<div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0 fw-bold ">Employees Info</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="ac-line-transparent" id="apex-emplyoeeAnalytics"></div>
                                    </div>
                                </div>
                            </div>
							<div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0 fw-bold ">Total Employees</h6>
                                        <h4 class="mb-0 fw-bold ">423</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="mt-3" id="apex-MainCategories"></div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div><!-- Row End -->

			</div>
@endsection

