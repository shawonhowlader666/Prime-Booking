@extends('layouts.app')

@section('title', 'Payroll Report | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Report">Report</li>
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

				<div class="card bg-transparent border-0">
					<ul class="nav nav-tabs li_animate fs-5" role="tablist">
						<li class="nav-item" role="presentation"><a class="nav-link px-lg-4 py-lg-3 active" data-bs-toggle="tab" href="#ExpenseReport" aria-selected="true" role="tab"><i class="bi bi-credit-card"></i> Expense Report</a></li>
						<li class="nav-item" role="presentation"><a class="nav-link px-lg-4 py-lg-3" data-bs-toggle="tab" href="#InvoiceReport" aria-selected="false" tabindex="-1" role="tab"><i class="bi bi-journal-text"></i><span class="d-none d-sm-inline-flex ps-1">Invoice Report</span></a></li>
					</ul>
					<div class="tab-content bg-card p-3 border border-top-0 rounded-bottom">
						<!--[ Start:: Expense Report ]-->
						<div class="tab-pane fade show active" id="ExpenseReport" role="tabpanel">
							<table class="table table-hover custom-table my_data_table w-100">
								<thead>
									<tr class="text-muted small text-uppercase">
										<th>Item</th>
										<th>Order By</th>
										<th>From</th>
										<th>Date</th>
										<th>Paid By</th>
										<th>Status</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Lenovo Monitor 24 inch</td>
										<td>Kevin Glover</td>
										<td>Amazon India</td>
										<td>16 Dec, 2022</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-warning">Pending</span></td>
										<td>$135</td>
									</tr>
									<tr>
										<td>Mi LED Smart TV 4A 80</td>
										<td>Julia Bower</td>
										<td>Mi Website</td>
										<td>23 Jan, 2023</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$312</td>
									</tr>
									<tr>
										<td>Airtel DG-HR3400 Router</td>
										<td>Sebastian Black</td>
										<td>Amazon India</td>
										<td>01 Feb, 2023</td>
										<td><img src="/assets/images/payment/mastercard.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$250</td>
									</tr>
									<tr>
										<td>Lenovo Monitor 24 inch</td>
										<td>Kevin Glover</td>
										<td>Amazon India</td>
										<td>16 Dec, 2022</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-warning">Pending</span></td>
										<td>$135</td>
									</tr>
									<tr>
										<td>Mi LED Smart TV 4A 80</td>
										<td>Julia Bower</td>
										<td>Mi Website</td>
										<td>23 Jan, 2023</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$312</td>
									</tr>
									<tr>
										<td>Airtel DG-HR3400 Router</td>
										<td>Sebastian Black</td>
										<td>Amazon India</td>
										<td>01 Feb, 2023</td>
										<td><img src="/assets/images/payment/mastercard.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$250</td>
									</tr>
									<tr>
										<td>Dell 16 inch Laptop Backpack</td>
										<td>Kevin James</td>
										<td>ebay UK</td>
										<td>17 July, 2022</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$125</td>
									</tr>
									<tr>
										<td>Dell 16 inch Laptop Backpack</td>
										<td>Kevin James</td>
										<td>ebay UK</td>
										<td>17 July, 2022</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$125</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!--[ Start:: Invoice Report ]-->
						<div class="tab-pane fade" id="InvoiceReport" role="tabpanel">
							<table class="table table-hover custom-table my_data_table w-100">
								<thead>
									<tr class="text-muted small text-uppercase">
										<th>Invoice Number</th>
										<th>Clients</th>
										<th>Date</th>
										<th>Paid By</th>
										<th>Status</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>#LA-0220</td>
										<td>iVince Tech Pvt Ltd</td>
										<td>17 Nov, 2022</td>
										<td><img src="/assets/images/payment/paypal.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-warning">Pending</span></td>
										<td>$5,205</td>
									</tr>
									<tr>
										<td>#LA-0245</td>
										<td>Altra Systems</td>
										<td>23 Feb, 2023</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$2,000</td>
									</tr>
									<tr>
										<td>#LA-0246</td>
										<td>Data Core Systems</td>
										<td>01 Jan, 2023</td>
										<td><img src="/assets/images/payment/paypal.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$2,850</td>
									</tr>
									<tr>
										<td>#LA-0312</td>
										<td>Vintage solutions</td>
										<td>07 May, 2022</td>
										<td><img src="/assets/images/payment/mastercard.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-warning">Pending</span></td>
										<td>$4,280</td>
									</tr>
									<tr>
										<td>#LA-0348</td>
										<td>Tritech Solutions Pvt Ltd</td>
										<td>25 Aug, 2022</td>
										<td><img src="/assets/images/payment/paypal.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-warning">Pending</span></td>
										<td>$1,025</td>
									</tr>
									<tr>
										<td>#LA-0352</td>
										<td>Unit India Systems</td>
										<td>15 March, 2023</td>
										<td><img src="/assets/images/payment/mastercard.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$7,520</td>
									</tr>
									<tr>
										<td>#LA-0353</td>
										<td>Megara IT Solutions</td>
										<td>08 Jan, 2023</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$5,320</td>
									</tr>
									<tr>
										<td>#LA-0365</td>
										<td>Logind Technologies</td>
										<td>25 March, 2022</td>
										<td><img src="/assets/images/payment/visa-card.svg" alt=""></td>
										<td><span class="text-uppercase badge bg-success">Approved</span></td>
										<td>$1,500</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
			$('.my_data_table').addClass('nowrap').dataTable({
				responsive: true
			});
			$('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
			});
		});
</script>
@endpush
