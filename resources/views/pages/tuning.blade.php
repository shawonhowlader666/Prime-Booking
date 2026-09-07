@extends('layouts.app')

@section('title', 'Tuning & Settings Hub | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Tuning">Tuning</li>
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
							<h3 class="fw-bold mb-0">Tuning & System Control</h3>
						</div>
					</div>
				</div>
				<ul class="row g-2 list-unstyled li_animate row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-1 row-deck mb-lg-5 mb-4">
					<li class="col">
						<a class="card hr-arrow p-4" href="common-settings.html" title="Common" style="--dynamic-color: var(--theme-color1);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M3 21l18 0"></path>
								<path d="M5 21v-14l8 -4v18"></path>
								<path d="M19 21v-10l-6 -4"></path>
								<path d="M9 9l0 .01"></path>
								<path d="M9 12l0 .01"></path>
								<path d="M9 15l0 .01"></path>
								<path d="M9 18l0 .01"></path>
							</svg>
							<h6 class="title mb-1">Common Settings</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Branding, address & hotel metadata</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="payment-set.html" title="Payment" style="--dynamic-color: var(--theme-color2);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M12 19h-6a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v4.5"></path>
								<path d="M3 10h18"></path>
								<path d="M16 19h6"></path>
								<path d="M19 16l3 3l-3 3"></path>
								<path d="M7.005 15h.005"></path>
								<path d="M11 15h2"></path>
							</svg>
							<h6 class="title mb-1">Payment Settings</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Stripe, PayPal, SSLCommerz credentials</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="{{ route('subscription.show') }}" title="SaaS Subscription" style="--dynamic-color: var(--theme-color1);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M12 3a9 9 0 0 1 9 9v7a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-7a9 9 0 0 1 9 -9z"></path>
								<path d="M12 7v5l3 3"></path>
							</svg>
							<h6 class="title mb-1">Subscription Hub</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">License validity, renewal & Prime Sync</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="accounts-setting.html" title="accounts" style="--dynamic-color: var(--theme-color3);">
							<svg class="mb-3" xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
								<path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
								<path d="M9 7l1 0"></path>
								<path d="M9 13l6 0"></path>
								<path d="M13 17l2 0"></path>
							</svg>
							<h6 class="title mb-1">Account Settings</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Tax identification & invoice prefixes</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="role-add.html" title="Role Management" style="--dynamic-color: var(--theme-color5);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24"  viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M3 4m0 1a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z" />
								<path d="M7 8v1" />
								<path d="M17 8v1" />
								<path d="M12.5 4c-.654 1.486 -1.26 3.443 -1.5 9h2.5c-.19 2.867 .094 5.024 .5 7" />
								<path d="M7 15.5c3.667 2 6.333 2 10 0" />
							</svg>
							<h6 class="title mb-1">Role Management</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Staff privileges & security roles</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
				</ul>
			</div>
@endsection
