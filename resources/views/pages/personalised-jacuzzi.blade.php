@extends('layouts.app')

@section('title', 'Personalised Jacuzzi | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item"><a href="personalised.html" title="Personalised">Personalised</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Jacuzzi">Jacuzzi</li>
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
							<h3 class="fw-bold mb-0">Jacuzzi Booking</h3>
							<div class="col-auto d-flex w-sm-100">
								<a href="personalised-settings.html" class="btn btn-dark" title="personalised-settings">Settings</a>
							</div>
						</div>
				  </div>
                  	<div class="col-sm-12">
						<div class="card mb-3">
							<div class="card-body">
								<div class="row g-3">
									<div class="col-md-6 col-lg-3">
										<label class="form-label">Jacuzzi Category </label>
										<select class="form-select">
											<option selected>Choose...</option>
											<option>Luxury Jacuzzi</option>
											<option>Outdoor Jacuzzi </option>
											<option>In-ground Jacuzzi</option>
											<option>Hydrotherapy Jacuzzi</option>
											<option>Smart Jacuzzi </option>
										</select>
									</div>
									<div class="col-md-6 col-lg-3">
										<label class="form-label">Room Number</label>
										<select class="form-select">
											<option selected>Choose...</option>
											<option>A-101</option>
											<option>B-102</option>
											<option>C-103</option>
											<option>D-104</option>
											<option>E-105</option>
											<option>F-106</option>
											<option>G-107</option>
											<option>H-108</option>
											<option>I-109</option>
											<option>J-110</option>
										</select>
									</div>
									<div class="col-md-6 col-lg-3">
										<label class="form-label">Select Date</label>
										<input type="date" class="form-control">
									</div>
									<div class="col-md-6 col-lg-3">
										<label class="form-label">Select Time Slot</label>
										<select class="form-select">
											<option selected>Choose...</option>
											<option>10 to 12</option>
											<option>12 to 02</option>
											<option>04 to 06</option>
										</select>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.A-109</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.B-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.C-105</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.D-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.E-109</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.F-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.G-109</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.H-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.I-109</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.J-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.K-109</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.L-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.M-109</span>
												<span class="mt-1 text-success">Booked 10 to 12</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Outdoor Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-3">
										<div class="room-select border p-3 rounded">
											<div class="text-center">
												<span class="fw-bold d-block text-primary">Room No.N-106</span>
												<span class="mt-1 text-success">Booked 02 to 04</span>
												<span class="mt-1 text-uppercase text-gradient d-block">Luxury Jacuzzi</span>
											</div>
										</div>
									</div>
									<div class="col-12 text-end">
										<button type="button" class="btn btn-primary">Apply</button>
									</div>
								</div>
							</div>
						</div>
			  		</div>
                </div><!-- Row End -->

			</div>
@endsection

