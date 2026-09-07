@extends('layouts.app')

@section('title', 'Hrms | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="HRMS">HRMS</li>
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
				<ul class="row g-2 list-unstyled li_animate row-cols-lg-3 row-cols-md-3 row-cols-sm-2 row-cols-2 row-deck mb-lg-5 mb-4">
					<li class="col">
						<a class="card hr-arrow p-4" href="employees-add.html" title="employees-add" style="--dynamic-color: var(--theme-color4);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
								<path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
								<path d="M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05" />
							</svg>
							<h6 class="title mb-1">Employees Add</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="employees-leave.html" title="Leave" style="--dynamic-color: var(--theme-color6);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24"  stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M6 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
								<path d="M9 6v-1a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v1" />
								<path d="M6 10h12" />
								<path d="M6 16h12" />
								<path d="M9 20v1" />
								<path d="M15 20v1" />
							</svg>
							<h6 class="title mb-1">Leave Tracking</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="duty-assigned-list.html" title="duty-assigned" style="--dynamic-color: var(--theme-color2);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M3 12a9 9 0 0 0 5.998 8.485m12.002 -8.485a9 9 0 1 0 -18 0"></path>
								<path d="M12 7v5"></path><path d="M12 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2"></path>
								<path d="M18 15v2a1 1 0 0 0 1 1h1"></path>
								<path d="M21 15v6"></path>
							</svg>
							<h6 class="title mb-1">Duty Changes</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="employees-attendance.html" title="attendance" style="--dynamic-color: var(--theme-color3);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24"  viewBox="0 0 24 24"  stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" />
								<path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6" />
								<path d="M12 11v2a14 14 0 0 0 2.5 8" />
								<path d="M8 15a18 18 0 0 0 1.8 6" />
								<path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95" />
							</svg>
							<h6 class="title mb-1">Attendance</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="payroll-employee-salary.html" title="salary" style="--dynamic-color: var(--theme-color5);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
								<path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
								<path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
							</svg>
							<h6 class="title mb-1">Salary</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="payroll-report.html" title="Reports" style="--dynamic-color: var(--theme-color1);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24"  stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
								<path d="M18 14v4h4" />
								<path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
								<path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
								<path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
								<path d="M8 11h4" />
								<path d="M8 15h3" />
							</svg>
							<h6 class="title mb-1">Reports</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="employees-departments.html" title="promocode" style="--dynamic-color: var(--theme-color4);">
							<svg xmlns="http://www.w3.org/2000/svg" class="mb-3" width="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
								<path d="M3 3m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
								<path d="M17 17m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
								<path d="M7 5l7 0" />
								<path d="M10 19l7 0" />
								<path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
								<path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
								<path d="M7 5.5a5 6.5 0 0 1 5 6.5a5 6.5 0 0 0 5 6.5" />
							</svg>
							<h6 class="title mb-1">Departments</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
					<li class="col">
						<a class="card hr-arrow p-4" href="employees-loan.html" title="promocode" style="--dynamic-color: var(--theme-color2);">
							<svg  xmlns="http://www.w3.org/2000/svg"  width="24"   viewBox="0 0 24 24"  fill="none"  stroke="currentColor"   stroke-linecap="round"  stroke-linejoin="round"  class="mb-3"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>
							<h6 class="title mb-1">Employees Loan</h6>
							<p class="small text-muted mb-0 d-none d-xl-inline-flex">Description text here...</p>
							<div class="go-corner">
								<div class="go-arrow">→</div>
							</div>
						</a>
					</li>
				</ul>
			</div>
@endsection

