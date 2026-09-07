@extends('layouts.app')

@section('title', 'Payment Settings | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Payment Settings">Payment Settings</li>
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
				<div class="row mb-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Payment Settings</h3>
						</div>
					</div>
				</div>
				<div class="card-body mb-3">
					<!-- Stripe Gateway Card -->
					@php $stripe = $gateways['STRIPE'] ?? null; @endphp
					<div class="card border-0 mb-4">
						<div class="card-header">
							<h6 class="card-title mb-0">Stripe Settings</h6>
						</div>
						<div class="card-body pt-0">
							<form action="{{ route('settings.payments.save') }}" method="POST">
								@csrf
								<input type="hidden" name="gateway" value="STRIPE">
								<div class="mb-3 row mt-3">
									<label class="col-md-3 form-check-label" for="Stripe">Stripe Payments</label>
									<div class="col-md-9">
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" name="is_enabled" role="switch" id="Stripe" {{ ($stripe && $stripe->is_enabled) ? 'checked' : '' }}>
										</div>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Payment Mode</label>
									<div class="col-md-9">
										<select name="environment" class="form-select">
											<option value="sandbox" {{ ($stripe && $stripe->environment === 'sandbox') ? 'selected' : '' }}>Sandbox (Test)</option>
                                            <option value="live" {{ ($stripe && $stripe->environment === 'live') ? 'selected' : '' }}>Live (Production)</option>
										</select>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Currency Code</label>
									<div class="col-md-9">
										<select name="currency_code" class="form-select">
											<option value="USD" {{ (!$stripe || $stripe->currency_code === 'USD') ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ ($stripe && $stripe->currency_code === 'EUR') ? 'selected' : '' }}>EUR</option>
											<option value="GBP" {{ ($stripe && $stripe->currency_code === 'GBP') ? 'selected' : '' }}>GBP</option>
											<option value="BDT" {{ ($stripe && $stripe->currency_code === 'BDT') ? 'selected' : '' }}>BDT</option>
										</select>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Payment Endpoint URL</label>
									<div class="col-md-9">
										<input type="text" name="endpoint_url" class="form-control" value="{{ $stripe?->endpoint_url ?? 'https://api.stripe.com/v1' }}">
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Publishable Key</label>
									<div class="col-md-9">
										<input type="text" name="publishable_key" class="form-control" value="{{ $stripe?->publishable_key }}" placeholder="pk_test_...">
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Secret Key</label>
									<div class="col-md-9">
										<input type="password" name="secret_key" class="form-control" value="{{ $stripe?->secret_key }}" placeholder="sk_test_...">
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Webhook Secret Key</label>
									<div class="col-md-9">
										<input type="password" name="webhook_secret" class="form-control" value="{{ $stripe?->webhook_secret }}" placeholder="whsec_...">
									</div>
								</div>
								<div class="col-12 text-end">
									<button type="reset" class="btn btn-warning">Reset</button>
									<button type="submit" class="btn btn-primary">Save Stripe</button>
								</div>
							</form>
						</div>
					</div>

					<!-- PayPal Gateway Card -->
					@php $paypal = $gateways['PAYPAL'] ?? null; @endphp
					<div class="card border-0 mb-4">
						<div class="card-header">
							<h6 class="card-title mb-0">PayPal Settings</h6>
						</div>
						<div class="card-body pt-0">
							<form action="{{ route('settings.payments.save') }}" method="POST">
								@csrf
								<input type="hidden" name="gateway" value="PAYPAL">
								<div class="mb-3 row mt-3">
									<label class="col-md-3 form-check-label" for="Paypal">PayPal Payments</label>
									<div class="col-md-9">
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" name="is_enabled" role="switch" id="Paypal" {{ ($paypal && $paypal->is_enabled) ? 'checked' : '' }}>
										</div>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Payment Mode</label>
									<div class="col-md-9">
										<select name="environment" class="form-select">
											<option value="sandbox" {{ ($paypal && $paypal->environment === 'sandbox') ? 'selected' : '' }}>Sandbox</option>
                                            <option value="live" {{ ($paypal && $paypal->environment === 'live') ? 'selected' : '' }}>Live</option>
										</select>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Client ID</label>
									<div class="col-md-9">
										<input type="text" name="publishable_key" class="form-control" value="{{ $paypal?->publishable_key }}" placeholder="Client ID">
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-3 col-form-label">Secret Key</label>
									<div class="col-md-9">
										<input type="password" name="secret_key" class="form-control" value="{{ $paypal?->secret_key }}" placeholder="Secret Key">
									</div>
								</div>
								<div class="col-12 text-end">
									<button type="reset" class="btn btn-warning">Reset</button>
									<button type="submit" class="btn btn-primary">Save PayPal</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
@endsection
