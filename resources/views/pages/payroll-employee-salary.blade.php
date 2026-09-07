@extends('layouts.app')

@section('title', 'Payroll Employee Salary | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Employee Salary">Employee Salary</li>
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

				<h3 class="fw-bold">Employee Salary</h3>
				<div class="card">
					<div class="card-body">
						<table class="table table-hover align-middle my_data_table w-100">
							<thead>
								<tr class="text-muted small text-uppercase">
									<th>Name</th>
									<th>Phone</th>
									<th>Join Date</th>
									<th>Role</th>
									<th>Salary</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar10.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Depo Stone</h6>
											<span class="text-muted">depo@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-1234</td>
									<td>19 March, 2023</td>
									<td>Event Coordinator</td>
									<td>$589</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar4.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Furry Pelot</h6>
											<span class="text-muted">furry@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-5283</td>
									<td>18 March, 2023</td>
									<td>Concierge</td>
									<td>$157</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar5.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Jane Hunt</h6>
											<span class="text-muted">jane_hunt@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-2483</td>
									<td>14 March, 2023</td>
									<td>Housekeeping Staff</td>
									<td>$425</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar6.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Darryl Day</h6>
											<span class="text-muted">darryl_day@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-2583</td>
									<td>08 March, 2023</td>
									<td>Concierge</td>
									<td>$520</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar7.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Jeffrey Warden</h6>
											<span class="text-muted">jeffrey_warden@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-2083</td>
									<td>11 March, 2023</td>
									<td>Security Manager</td>
									<td>$455</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar8.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Berdo Galaviz</h6>
											<span class="text-muted">berdo@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-1283</td>
									<td>27 Feb, 2023</td>
									<td>Housekeeping Staff</td>
									<td>$865</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar1.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Alice Collins</h6>
											<span class="text-muted">alice_collins@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-8888</td>
									<td>24 March, 2023</td>
									<td>Receptionist</td>
									<td>$145</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar2.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Susie Willis</h6>
											<span class="text-muted">sussie-w@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-7783</td>
									<td>22 March, 2023</td>
									<td>Housekeeping Staff</td>
									<td>$233</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar3.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Debra Stewart</h6>
											<span class="text-muted">debra_stewart@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-8883</td>
									<td>19 March, 2023</td>
									<td>Event Coordinator</td>
									<td>$265</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar4.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Francisco Vasquez</h6>
											<span class="text-muted">francisco_vasquez@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-628-8883</td>
									<td>18 March, 2023</td>
									<td>Concierge</td>
									<td>$685</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar5.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Jane Hunt</h6>
											<span class="text-muted">jane_hunt@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-5583</td>
									<td>14 March, 2023</td>
									<td>Housekeeping Staff</td>
									<td>$200</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar6.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Don Mike</h6>
											<span class="text-muted">don@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-2589</td>
									<td>08 March, 2023</td>
									<td>Concierge</td>
									<td>$142</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
								<tr>
									
									<td>
										<div class="d-flex align-items-center">
										<img src="/assets/images/lg/avatar8.jpg" class="rounded-circle avatar" alt="">
										<div class="ms-3">
											<h6 class="mb-0">Bernardo Galaviz</h6>
											<span class="text-muted">bernardo_galaviz@gmail.com</span>
										</div>
										</div>
									</td>
									<td>+ 264-625-2588</td>
									<td>27 Feb, 2023</td>
									<td>Housekeeping Staff</td>
									<td>$80</td>
									<td>
										<button type="button" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send salary slip"><i class="bi bi-envelope"></i> Slip</button>
										<button type="button" class="btn btn-sm btn-default" title="Print"><i class="bi bi-printer"></i></button>
										<button type="button" class="btn deleterow text-danger"><i class="bi bi-trash"></i></button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
			$('.my_data_table')
			.addClass( 'nowrap' )
			.dataTable( {
				responsive: true,
				columnDefs: [
					//{ targets: [-1, -3], className: 'dt-body-right' }
				]
			});
			
		});
</script>
@endpush
