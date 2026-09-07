@extends('layouts.app')

@section('title', 'Payroll Payslip | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
	<ol class="breadcrumb mb-0 bg-transparent">
		<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" title="home">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page" title="Payslip">Payslip</li>
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
	<div class="card">
		<div class="card-body">
			<div class="bg-gradient-dark p-4 text-white rounded">
				<h5 class="fw-bold mb-0 text-start text-md-center text-white">Payslip for the month of {{ $payroll->month_year ?? 'Aug 2023' }}</h5>
				<hr>
				<div class="row g-lg-3 g-2">
					<div class="col-sm-6">
						<div class="d-flex">
							<a href="#" class="logo">
								<svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill="var(--primary-color)" d="M0 8C0 3.58172 3.58172 0 8 0H24C28.4183 0 32 3.58172 32 8V32H8C3.58172 32 0 28.4183 0 24V8Z"></path>
									<path fill-rule="evenodd" clip-rule="evenodd" d="M9.17708 13.2C9.17708 12.7582 9.53152 12.4 9.96875 12.4H16.5C16.9372 12.4 17.2917 12.7582 17.2917 13.2C17.2917 13.6418 16.9372 14 16.5 14H9.96875C9.53152 14 9.17708 13.6418 9.17708 13.2Z" fill="white"></path>
									<path fill-rule="evenodd" clip-rule="evenodd" d="M7 8.8C7 8.35817 7.35444 8 7.79167 8H12.1458C12.5831 8 12.9375 8.35817 12.9375 8.8C12.9375 9.24183 12.5831 9.6 12.1458 9.6H7.79167C7.35444 9.6 7 9.24183 7 8.8Z" fill="white"></path>
									<path fill-rule="evenodd" clip-rule="evenodd" d="M9.96875 8C10.406 8 10.7604 8.35817 10.7604 8.8V20.9C10.7604 20.9796 10.7917 21.0559 10.8474 21.1121C10.903 21.1684 10.9786 21.2 11.0573 21.2H16.5C16.9372 21.2 17.2917 21.5582 17.2917 22C17.2917 22.4418 16.9372 22.8 16.5 22.8H11.0573C10.5586 22.8 10.0804 22.5998 9.72778 22.2435C9.37518 21.8872 9.17708 21.4039 9.17708 20.9V8.8C9.17708 8.35817 9.53152 8 9.96875 8Z" fill="white"></path>
									<path fill-rule="evenodd" clip-rule="evenodd" d="M17.5885 11.8C17.5098 11.8 17.4343 11.8316 17.3786 11.8879C17.3229 11.9441 17.2917 12.0204 17.2917 12.1V14.3C17.2917 14.3796 17.3229 14.4559 17.3786 14.5121C17.4343 14.5684 17.5098 14.6 17.5885 14.6H24.1198C24.1985 14.6 24.274 14.5684 24.3297 14.5121C24.3854 14.4559 24.4167 14.3796 24.4167 14.3V12.1C24.4167 12.0204 24.3854 11.9441 24.3297 11.8879C24.274 11.8316 24.1985 11.8 24.1198 11.8H17.5885ZM16.259 10.7565C16.6116 10.4002 17.0899 10.2 17.5885 10.2H24.1198C24.6185 10.2 25.0967 10.4002 25.4493 10.7565C25.8019 11.1128 26 11.5961 26 12.1V14.3C26 14.8039 25.8019 15.2872 25.4493 15.6435C25.0967 15.9998 24.6185 16.2 24.1198 16.2H17.5885C17.0899 16.2 16.6116 15.9998 16.259 15.6435C15.9064 15.2872 15.7083 14.8039 15.7083 14.3V12.1C15.7083 11.5961 15.9064 11.1128 16.259 10.7565Z" fill="white"></path>
									<path fill-rule="evenodd" clip-rule="evenodd" d="M17.5885 20.6C17.5098 20.6 17.4343 20.6316 17.3786 20.6879C17.3229 20.7441 17.2917 20.8204 17.2917 20.9V23.1C17.2917 23.1796 17.3229 23.2559 17.3786 23.3121C17.4343 23.3684 17.5098 23.4 17.5885 23.4H24.1198C24.1985 23.4 24.274 23.3684 24.3297 23.3121C24.3854 23.2559 24.4167 23.1796 24.4167 23.1V20.9C24.4167 20.8204 24.3854 20.7441 24.3297 20.6879C24.274 20.6316 24.1985 20.6 24.1198 20.6H17.5885ZM16.259 19.5565C16.6116 19.2002 17.0899 19 17.5885 19H24.1198C24.6185 19 25.0967 19.2002 25.4493 19.5565C25.8019 19.9128 26 20.3961 26 20.9V23.1C26 23.6039 25.8019 24.0872 25.4493 24.4435C25.0967 24.7998 24.6185 25 24.1198 25H17.5885C17.0899 25 16.6116 24.7998 16.259 24.4435C15.9064 24.0872 15.7083 23.6039 15.7083 23.1V20.9C15.7083 20.3961 15.9064 19.9128 16.259 19.5565Z" fill="white"></path>
								</svg>
							</a>
							<div class="ms-3">
								<h6>Hotel Air</h6>
								<p class="mb-0">{{ $hotel->address ?? '802 Corporate Road.' }} <br>{{ $hotel->city ?? 'New York' }} </p>
							</div>
						</div>
					</div>
					<div class="col-sm-6 text-sm-end pt-sm-0 pt-3">
						<h4>Invoice #{{ $payroll->id ?? '1069' }}</h4>
						<p class="mb-0">Salary Month: {{ $payroll->month_year ?? 'Aug, 2023' }}</p>
					</div>
				</div>
			</div>
			<div class="d-flex align-items-start my-4">
				<div class="me-3">
					<img src="/assets/images/lg/avatar1.jpg" class="rounded-circle lg avatar" alt="user">
				</div>
				<div class="info">
					<h6 class="fw-semibold">{{ $payroll->employee->name ?? 'Francisco Vasquez' }}</h6>
					<small>{{ $payroll->employee->designation ?? $payroll->employee->role ?? 'Receptionist' }}<br>Employee ID: EMP-{{ $payroll->employee->id ?? 'LA-0210' }}</small>
				</div>
			</div>
			<div class="row g-lg-4 g-2">
				<div class="col-lg-6">
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr class="small text-uppercase">
									<th>#</th>
									<th>Earnings</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td>Basic Salary</td>
									<td>৳{{ number_format($payroll->base_salary ?? 1500, 2) }}</td>
								</tr>
								<tr>
									<td>2</td>
									<td>House Rent Allowance (H.R.A.)</td>
									<td>৳{{ number_format($payroll->allowances ?? 50, 2) }}</td>
								</tr>
								<tr>
									<td>3</td>
									<td>Bonus</td>
									<td>৳0.00</td>
								</tr>
								<tr>
									<td>4</td>
									<td>Conveyance</td>
									<td>৳0.00</td>
								</tr>
								<tr>
									<td>5</td>
									<td>Other Allowance</td>
									<td>৳0.00</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr class="small text-uppercase">
									<th>#</th>
									<th>Deductions</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td>Tax Deducted at Source (T.D.S.)</td>
									<td>৳{{ number_format($payroll->deductions ?? 0, 2) }}</td>
								</tr>
								<tr>
									<td>2</td>
									<td>Provident Fund</td>
									<td>৳0.00</td>
								</tr>
								<tr>
									<td>3</td>
									<td>ESI</td>
									<td>৳0.00</td>
								</tr>
								<tr>
									<td>4</td>
									<td>Loan</td>
									<td>৳0.00</td>
								</tr>
								<tr>
									<td>5</td>
									<td>Total Deductions</td>
									<td>৳{{ number_format($payroll->deductions ?? 0, 2) }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
