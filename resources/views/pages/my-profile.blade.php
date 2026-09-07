@extends('layouts.app')

@section('title', 'My Profile | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Profile">Profile</li>
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
			<!-- start: page body area -->
			<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
				<div class="col-sm-12 mb-3">
					<div class="d-flex align-items-center justify-content-between flex-wrap">
						<h3 class="fw-bold mb-0">My Profile</h3>
					</div>
				</div>
				<div class="card border-0">
					<div class="card-header bg-card pb-3">
						<div class="d-flex align-items-md-start align-items-center flex-column flex-md-row mt-4 w-100">
							<img src="{{ asset('assets/images/profile_av.png') }}" alt="" class="rounded-4">
							<div class="media-body ms-md-5 m-0 mt-4 mt-md-0 text-md-start text-center">
								<h4 class="mb-1">{{ $user->name ?? 'Anny glover' }}</h4>
								<p>{{ $user->email ?? 'anny-glover@hotelair.com' }}</p>
								<span class="text-muted">{{ $user->about_me ?? 'Exploring a chic and contemporary admin template for your upcoming project? Discover our latest Bootstrap admin template! 🚀' }}</span>
								<div class="my-3">
									<button class="btn btn-primary">Follow</button>
									<button class="btn btn-dark">Message</button>
								</div>
								<div class="d-flex flex-row flex-wrap align-items-center justify-content-center justify-content-md-start">
									<div class="bg-body rounded-2 py-2 px-3 me-1 mt-1">
										<small class="text-muted">City</small>
										<div class="fs-5">{{ $hotel->city ?? 'New york' }}</div>
									</div>
									<div class="bg-body rounded-2 py-2 px-3 me-1 mt-1">
										<small class="text-muted">Awards</small>
										<div class="fs-5">13</div>
									</div>
									<div class="bg-body rounded-2 py-2 px-3 me-1 mt-1">
										<small class="text-muted">Experience</small>
										<div class="fs-5">10+</div>
									</div>
									<div class="bg-body rounded-2 py-2 px-3 me-1 mt-1">
										<small class="text-muted">Total Earnings</small>
										<div class="fs-5">৳{{ number_format($user->salary ?? 8705, 0) }}</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body border-top">
						<div class="row g-4">
							<div class="col-xl-4 col-lg-5">
								<div class="card">
									<div class="card-body">
										<h6 class="card-title mb-3">Skills Information</h6>
										<p class="text-muted">Fusce tincidunt, eros eget efficitur volutpat, libero dolor dapibus quam, nec pulvinar magna est vel ligula <a href="#">Read more</a></p>
										<div class="d-flex justify-content-between">
											<small class="text-uppercase">Managment:</small>
											<small class="text-muted">95</small>
										</div>
										<div class="progress mt-1 mb-3" style="height: 2px;">
											<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" style="width: 95%;"></div>
										</div>
										<div class="d-flex justify-content-between">
											<small class="text-uppercase">Account:</small>
											<small class="text-muted">77</small>
										</div>
										<div class="progress mt-1 mb-3" style="height: 2px;">
											<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 77%;"></div>
										</div>
										<div class="d-flex justify-content-between">
											<small class="text-uppercase">Organizer:</small>
											<small class="text-muted">66</small>
										</div>
										<div class="progress mt-1 mb-3" style="height: 2px;">
											<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100" style="width: 66%;"></div>
										</div>
										<div class="d-flex justify-content-between">
											<small class="text-uppercase">Cook:</small>
											<small class="text-muted">80</small>
										</div>
										<div class="progress mt-1 mb-3" style="height: 2px;">
											<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%;"></div>
										</div>
										<div class="d-flex justify-content-between">
											<small class="text-uppercase">Swimming:</small>
											<small class="text-muted">85</small>
										</div>
										<div class="progress mt-1 mb-0" style="height: 2px;">
											<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-8 col-lg-7">
								<h4 class="card-title mb-0">Edit Profile</h4>
								<form action="{{ route('profile.update') }}" method="POST">
									@csrf
									@if(session('success'))
										<div class="alert alert-success alert-dismissible fade show mt-3 mb-0" role="alert">
											<i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}
											<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
										</div>
									@endif
									@if($errors->any())
										<div class="alert alert-danger alert-dismissible fade show mt-3 mb-0" role="alert">
											<i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
											<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
										</div>
									@endif
									<div class="row g-3 my-3">
										<div class="col-md-5">
											<label class="form-label small text-muted">Company</label>
											<input type="text" name="company" class="form-control" placeholder="Company" value="{{ old('company', $hotel->name ?? 'hotelair') }}">
										</div>
										<div class="col-sm-6 col-md-3">
											<label class="form-label small text-muted">Username / Phone</label>
											<input type="text" name="phone" class="form-control" placeholder="Username / Phone" value="{{ old('phone', $user->phone ?? $user->role ?? 'Michelle1212') }}">
										</div>
										<div class="col-sm-6 col-md-4">
											<label class="form-label small text-muted">Email address</label>
											<input type="email" name="email" class="form-control" required placeholder="Email" value="{{ old('email', $user->email ?? '') }}">
										</div>
										<div class="col-sm-6 col-md-6">
											<label class="form-label small text-muted">First Name</label>
											<input type="text" name="name" class="form-control" required placeholder="First Name" value="{{ old('name', $user->name ?? 'Jane') }}">
										</div>
										<div class="col-sm-6 col-md-6">
											<label class="form-label small text-muted">Last Name</label>
											<input type="text" name="designation" class="form-control" placeholder="Last Name" value="{{ old('designation', $user->designation ?? 'Pearson') }}">
										</div>
										<div class="col-md-12">
											<label class="form-label small text-muted">Address</label>
											<input type="text" name="address" class="form-control" placeholder="Home Address" value="{{ old('address', $hotel->address ?? '455 S. Airport St. Moncks Corner') }}">
										</div>
										<div class="col-sm-6 col-md-4">
											<label class="form-label small text-muted">City</label>
											<input type="text" name="city" class="form-control" placeholder="City" value="{{ old('city', $hotel->city ?? 'New York') }}">
										</div>
										<div class="col-sm-6 col-md-3">
											<label class="form-label small text-muted">Postal Code</label>
											<input type="text" name="postal_code" class="form-control" placeholder="ZIP Code" value="{{ old('postal_code', '1212') }}">
										</div>
										<div class="col-md-5">
											<label class="form-label small text-muted">Country</label>
											<input type="text" name="country" class="form-control" placeholder="Country" value="{{ old('country', $hotel->country ?? 'Bangladesh') }}">
										</div>
										<div class="col-md-12">
											<label class="form-label small text-muted">About Me</label>
											<textarea name="about_me" rows="5" class="form-control" placeholder="Here can be your description">{{ old('about_me', $user->about_me ?? 'Exploring a chic and contemporary admin template for your upcoming project? Discover our latest Bootstrap admin template! 🚀') }}</textarea>
										</div>
									</div>
									<button type="submit" class="btn btn-primary">Update Profile</button>
								</form>
							</div>
						</div> <!--[ .row end ]-->
					</div>
				</div>

			</div>
@endsection
