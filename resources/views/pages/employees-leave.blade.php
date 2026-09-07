@extends('layouts.app')

@section('title', 'Employees Leave | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Leave Request">Leave</li>
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
							<h3 class="fw-bold mb-0">Leave Request</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark btn-set-task w-sm-100" data-bs-toggle="modal" data-bs-target="#leaveadd">Add Leave</button>
							</div>
						</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="myProjectTable" class="table table-hover align-middle mb-0 w-100">
                                    <thead>
                                        <tr>
                                            <th>Employee Id</th>
                                            <th>Employee Name</th>
                                            <th>Leave Type</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Reason</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00001</a></td>
                                           <td>
                                               <img class="avatar rounded-circle" src="/assets/images/xs/avatar1.jpg" alt="">
                                               <span class="fw-bold ms-1">Frank Baker</span>
                                           </td>
                                           <td>Casual Leave</td>
                                           <td>12/03/2021</td>
                                           <td>14/03/2021</td>
                                           <td>Going to Holiday</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                    <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00038</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar2.jpg" alt="">
                                                <span class="fw-bold ms-1">Ryan	Randall</span>
                                            </td>
                                            <td>Casual Leave</td>
                                            <td>11/04/2021</td>
                                            <td>12/04/2021</td>
                                            <td>Going to Holiday</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                         <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00007</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar3.jpg" alt="">
                                                <span class="fw-bold ms-1">Phil	Glover</span>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>11/04/2021</td>
                                            <td>12/04/2021</td>
                                            <td>Going to Hospital</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                         <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00010</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar4.jpg" alt="">
                                                <span class="fw-bold ms-1">Victor Rampling</span>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>28/04/2021</td>
                                            <td>30/04/2021</td>
                                            <td>Going to Hospital</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                         <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00002</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar5.jpg" alt="">
                                                <span class="fw-bold ms-1">Sally Graham</span>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>01/05/2021</td>
                                            <td>06/05/2021</td>
                                            <td>Hospital Admit</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                         <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00005</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar6.jpg" alt="">
                                                <span class="fw-bold ms-1">Robert Anderson</span>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>03/05/2021</td>
                                            <td>06/05/2021</td>
                                            <td>Hospital Admit</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                         <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00014</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar7.jpg" alt="">
                                                <span class="fw-bold ms-1">Ryan	Stewart</span>
                                            </td>
                                            <td>Casual Leave</td>
                                            <td>10/07/2021</td>
                                            <td>18/08/2021</td>
                                            <td>Famaily Holiday</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
										 <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00017</a></td>
                                           <td>
                                               <img class="avatar rounded-circle" src="/assets/images/xs/avatar1.jpg" alt="">
                                               <span class="fw-bold ms-1">Joan Dyer</span>
                                           </td>
                                           <td>Casual Leave</td>
                                           <td>12/03/2021</td>
                                           <td>14/03/2021</td>
                                           <td>Going to Holiday</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                    <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00022</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar2.jpg" alt="">
                                                <span class="fw-bold ms-1">Liam Berry</span>
                                            </td>
                                            <td>Casual Leave</td>
                                            <td>11/04/2021</td>
                                            <td>12/04/2021</td>
                                            <td>Going to Holiday</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                         <tr>
                                            <td><a href="employee-profile.html" class="text-accent">#EMP : 00016</a></td>
                                            <td>
                                                <img class="avatar rounded-circle" src="/assets/images/xs/avatar3.jpg" alt="">
                                                <span class="fw-bold ms-1">Dean Otto</span>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>11/04/2021</td>
                                            <td>12/04/2021</td>
                                            <td>Going to Hospital</td>
                                             <td>
                                                 <div class="btn-group" role="group">
                                                     <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#leaveapprove"><i class="bi bi-check2"></i></button>
                                                     <button type="button" class="btn btn-outline-secondary deleterow" data-bs-toggle="modal" data-bs-target="#leavereject"><i class="bi bi-x"></i></button>
                                                 </div>
                                             </td>
                                         </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                  </div>
                </div><!-- Row End -->

			</div>
@endsection

@push('scripts')
<script>
// project data table
		$(document).ready(function() {
			$('#myProjectTable')
			.addClass( 'nowrap' )
			.dataTable( {
				responsive: true,
			});
		});
</script>
@endpush
