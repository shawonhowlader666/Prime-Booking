@extends('layouts.app')

@section('title', 'Transaction | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Invoice">Transaction</li>
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
							<form method="GET" action="{{ route('transactions') }}">
								<div class="row g-3 mt-3">
									<div class="col-12">
										<label class="form-label small text-muted">Sorted by:</label>
										<input type="search" name="search" class="form-control" placeholder="Search reference or guest" value="{{ request('search') }}">
									</div>
									<div class="col-12 text-end mt-4">
										<a href="{{ route('transactions') }}" class="btn btn-sm btn-light me-1">Reset</a>
										<button type="submit" class="btn btn-sm btn-primary">Apply</button>
									</div>
								</div>
							</form>
						</div>
					</li>
				</ul>
			</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">

				<div class="row mb-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Transaction</h3>
						</div>
					</div>
				</div>

				<ul class="row g-3 list-unstyled row-deck">
					<li class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
						<div class="card rounded-4 p-xl-3">
							<div class="card-body d-flex align-items-start">
								<div class="avatar rounded no-thumbnail"><i class="fa fa-cc-mastercard"></i></div>
								<div class="ms-3 me-auto">
									<h5 class="fw-normal theme-text-color1 mb-1">Bill</h5>
									<p>{{ isset($kpis) ? '$' . number_format($kpis['total_bill'], 2) : '$2,500.00' }}</p>
									<div id="apexspark-Bill"></div>
								</div>
							</div>
						</div>
					</li>
					<li class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
						<div class="card rounded-4 p-xl-3">
							<div class="card-body d-flex align-items-start">
								<div class="avatar rounded no-thumbnail"><i class="fa fa-cutlery"></i></div>
								<div class="ms-3 me-auto">
									<h5 class="fw-normal theme-text-color2 mb-1">Food</h5>
									<p>{{ isset($kpis) ? '$' . number_format($kpis['food'], 2) : '$22,300.00' }}</p>
									<div id="apexspark-Food"></div>
								</div>
							</div>
						</div>
					</li>
					<li class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
						<div class="card rounded-4 p-xl-3">
							<div class="card-body d-flex align-items-start">
								<div class="avatar rounded no-thumbnail"><i class="fa fa-cc-mastercard"></i></div>
								<div class="ms-3 me-auto">
									<h5 class="fw-normal theme-text-color3 mb-1">Utility</h5>
									<p>{{ isset($kpis) ? '$' . number_format($kpis['utility'], 2) : '$730.00' }}</p>
									<div id="apexspark-Utility"></div>
								</div>
							</div>
						</div>
					</li>
					<li class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
						<div class="card rounded-4 p-xl-3">
							<div class="card-body d-flex align-items-start">
								<div class="avatar rounded no-thumbnail"><i class="fa fa-shopping-basket"></i></div>
								<div class="ms-3 me-auto">
									<h5 class="fw-normal theme-text-color4 mb-1">Shopping</h5>
									<p>{{ isset($kpis) ? '$' . number_format($kpis['shopping'], 2) : '$5,500.00' }}</p>
									<div id="apexspark-Shopping"></div>
								</div>
							</div>
						</div>
					</li>
				</ul> <!--[ ul.row end ]-->

				<div class="border border-primary p-4 rounded-2 rounded-4">
					<div class="calendar-tab pb-4" style="--dynamic-color: var(--primary-color)">
						<ul class="nav nav-pills text-muted text-uppercase" role="tablist">
							<li class="nav-item">
								Sun
								<button class="nav-link" data-bs-toggle="pill" data-bs-target="#c_date_1" type="button" role="tab">01</button>
							</li>
							<li class="nav-item">
								Mon
								<button class="nav-link" data-bs-toggle="pill" data-bs-target="#c_date_2" type="button" role="tab">02</button>
							</li>
							<li class="nav-item">
								Tue
								<button class="nav-link" data-bs-toggle="pill" data-bs-target="#c_date_3" type="button" role="tab">03</button>
							</li>
							<li class="nav-item">
								Wed
								<button class="nav-link" data-bs-toggle="pill" data-bs-target="#c_date_4" type="button" role="tab">04</button>
							</li>
							<li class="nav-item">
								Thu
								<button class="nav-link active" data-bs-toggle="pill" data-bs-target="#c_date_5" type="button" role="tab">05</button>
							</li>
						</ul>
					</div>
					<table class="table align-middle table-hover dataTable table-body" style="width: 100%;">
						<thead>
							<tr class="small text-muted text-uppercase">
								<th>
									<div class="form-check mb-0" style="font-size: 16px;">
										<input class="form-check-input select-all" type="checkbox" value="">
									</div>
								</th>
								<th>Product Description</th>
								<th>Status</th>
								<th>Price</th>
								<th>Order No.</th>
								<th>Order Total</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($transactions) && $transactions->count() > 0)
								@foreach($transactions as $trx)
									<tr class="row-selectable">
										<td>
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="">
											</div>
										</td>
										<td class="fs-6">
											<strong>{{ $trx->reservation?->guest?->full_name ? 'Room ' . ($trx->reservation?->room?->room_number ?? '') . ' - ' . $trx->reservation->guest->full_name : ($trx->payment_method . ' Settlement') }}</strong>
										</td>
										<td class="text-primary"><span class="badge {{ $trx->status === 'SUCCESS' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $trx->status }}</span></td>
										<td>${{ number_format($trx->amount, 2) }}</td>
										<td>
											{{ $trx->transaction_reference ?? ('TRX-' . $trx->id) }}
											<div class="small text-muted">{{ $trx->created_at->format('d/m/Y h:i a') }}</div>
										</td>
										<td>${{ number_format($trx->amount, 2) }}</td>
										<td>
											<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Email"><i class="fa fa-envelope"></i></button>
											<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Download invoice"><i class="fa fa-download"></i></button>
										</td>
									</tr>
								@endforeach
							@else
								<tr class="row-selectable">
									<td>
										<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="">
										</div>
									</td>
									<td class="fs-6">Gas Bill Payment</td>
									<td class="text-primary">Your order is successful</td>
									<td>$500.00</td>
									<td>
										B-123123
										<div class="small text-muted">22  5:08 pm</div>
									</td>
									<td>$500.00</td>
									<td>
										<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Email"><i class="fa fa-envelope"></i></button>
										<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Download invoice"><i class="fa fa-download"></i></button>
									</td>
								</tr>
								<tr class="row-selectable">
									<td>
										<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="">
										</div>
									</td>
									<td class="fs-6">Electricity Bill</td>
									<td class="text-primary">successful</td>
									<td>$1000.00</td>
									<td>
										M-123123
										<div class="small text-muted">22  5:08 pm</div>
									</td>
									<td>$1000.00</td>
									<td>
										<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Email"><i class="fa fa-envelope"></i></button>
										<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Download invoice"><i class="fa fa-download"></i></button>
									</td>
								</tr>
								<tr class="row-selectable">
									<td>
										<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="">
										</div>
									</td>
									<td class="fs-6">Hotel Tax Bill</td>
									<td class="text-primary">Booking Confirmed</td>
									<td>$45.00</td>
									<td>
										B-123123
										<div class="small text-muted">22  5:08 pm</div>
									</td>
									<td>$46.00</td>
									<td>
										<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Email"><i class="fa fa-envelope"></i></button>
										<button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Download invoice"><i class="fa fa-download"></i></button>
									</td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>

			</div>
@endsection

@push('scripts')
<script>
$(document).ready( function () {
    $('.dataTable')
    .addClass( 'nowrap' )
    .dataTable( {
        responsive: true,
    });
});
</script>
@endpush
