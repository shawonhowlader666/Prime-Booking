@extends('layouts.app')

@section('title', 'Accounts Setting | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="tuning.html" title="tuning">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Accounts Settings">Accounts</li>
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
							<h3 class="fw-bold mb-0">Accounts</h3>
						</div>
					</div>
				</div>
				<div class="card bg-transparent border-0">
					<ul class="nav nav-tabs li_animate" role="tablist">
						<li class="nav-item" role="presentation"><a class="nav-link active" data-bs-toggle="tab" href="#setting-general" aria-selected="true" role="tab"><i class="bi bi-gear"></i><span class="ps-1">General</span></a></li>
						<li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#setting-billing" aria-selected="false" tabindex="-1" role="tab"><i class="bi bi-credit-card"></i><span class="d-none d-sm-inline-flex ps-1">Billing</span></a></li>
						<li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#setting-members" aria-selected="false" tabindex="-1" role="tab"><i class="bi bi-person"></i><span class="d-none d-sm-inline-flex ps-1">Members</span></a></li>
						<li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#setting-security" aria-selected="false" tabindex="-1" role="tab"><i class="bi bi-shield-check"></i><span class="d-none d-sm-inline-flex ps-1">Security</span></a></li>
						<li class="nav-item" role="presentation"><a class="nav-link" data-bs-toggle="tab" href="#setting-notifications" aria-selected="false" tabindex="-1" role="tab"><i class="bi bi-bell"></i><span class="d-none d-sm-inline-flex ps-1">Notifications</span></a></li>
					</ul>
					<div class="tab-content bg-card p-3 border border-top-0 rounded-bottom">
						<!--[ Start:: general]-->
						<div class="tab-pane fade show active" id="setting-general" role="tabpanel">
							<ul class="row g-3 list-unstyled li_animate mb-0">
								<li class="col-12 mb-5">
									<h5 class="mb-1">Account Setting</h5>
									<p class="text-muted">Update your account details</p>
									<!--[ setting: Account Setting input]-->
									<form class="row g-3">
										<div class="col-lg-4 col-md-6 col-sm-6 col-6">
											<div class="form-floating">
												<input type="text" class="form-control" placeholder="Enter your name">
												<label>Enter your name</label>
											</div>
										</div>
										<div class="col-lg-4 col-md-6 col-sm-6 col-6">
											<div class="form-floating">
												<input type="number" class="form-control" placeholder="Enter phone">
												<label>Enter phone</label>
											</div>
										</div>
										<div class="col-lg-4 col-md-6 col-sm-12">
											<div class="form-floating">
												<input type="email" class="form-control" placeholder="Enter your email">
												<label>Enter your email</label>
											</div>
										</div>
										<div class="col-lg-4 col-md-6 col-sm-12">
											<div class="form-floating">
												<input type="url" class="form-control" placeholder="Facebook profile link">
												<label>Facebook profile link</label>
											</div>
										</div>
										<div class="col-lg-4 col-md-6 col-sm-12">
											<div class="form-floating">
												<input type="url" class="form-control" placeholder="Instagram profile link">
												<label>Instagram profile link</label>
											</div>
										</div>
										<div class="col-lg-4 col-md-6 col-sm-12">
											<div class="form-floating">
												<input type="url" class="form-control" placeholder="Linkedin profile link">
												<label>Linkedin profile link</label>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group">
												<label>Select Avatar</label>
												<input type="file" class="form-control-file">
											</div>
										</div>
										<div class="col-12">
											<div class="form-floating">
												<textarea class="form-control" placeholder="Enter Bio" style="height: 100px"></textarea>
												<label>Enter Bio</label>
											</div>
										</div>
										<div class="col-12">
											<button type="button" class="btn btn-primary">Update Details</button>
											<button type="button" class="btn btn-secondary">Cancel</button>
										</div>
									</form> <!--[ .row end ]-->
								</li>
								<li class="col-12">
									<div class="card">
										<div class="card-body">
											<h4 class="card-title fw-light mb-4">Delete your account</h4>
											<p class="lead text-muted">Please note, deleting your account is a permanent action and will no be recoverable once completed.</p>
											<button class="btn btn-danger">Delete</button>
										</div>
									</div>
								</li>
							</ul> <!--[ ul.row end ]-->
						</div>
						<!--[ Start:: billing ]-->
						<div class="tab-pane fade" id="setting-billing" role="tabpanel">
							<ul class="row g-3 list-unstyled li_animate mb-0">
								<li class="col-12">
									<div class="card">
										<div class="card-body">
											<div class="alert alert-danger">
												<i class="zmdi zmdi-info me-1"></i> You are near your API limits.
											</div>
											<div class="row align-items-center">
												<div class="col">
													<span class="text-muted">Current plan</span>
													<h4 class="mb-0 mt-2">$39/ per month</h4>
												</div>
												<div class="col-auto">
													<a class="btn btn-sm btn-dark" href="#">Upgrade</a>
												</div>
											</div> <!--[ row end ]-->
										</div>
									</div>
								</li>
								<li class="col-12 mb-4">
									<div class="card">
										<div class="card-header">
											<h6 class="card-title mb-0">Payment methods</h6>
											<a class="btn btn-sm btn-primary" href="#!">Add method</a>
										</div>
										<div class="card-body">
											<div class="list-group list-group-flush my-2">
												<div class="list-group-item">
													<div class="row align-items-center">
														<div class="col-auto">
															<img class="img-fluid" src="/assets/images/visa.svg" alt="..." style="max-width: 38px;">
														</div>
														<div class="col ml-n2">
															<h6 class="mb-0">Visa ending in 7878</h6>
															<small class="text-muted">Expires 08/2022</small>
														</div>
														<div class="col-auto mr-n3">
															<span class="badge bg-light text-dark">Default</span>
														</div>

														<!--[ Dropdown ]-->
														<div class="col-auto">
															<a href="#" class="dropdown-toggle after-none" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
															<ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
																<li><a class="dropdown-item" href="#">File Info</a></li>
																<li><a class="dropdown-item" href="#">Copy to</a></li>
																<li><a class="dropdown-item" href="#">Move to</a></li>
																<li><a class="dropdown-item" href="#">Rename</a></li>
																<li><a class="dropdown-item" href="#">Block</a></li>
																<li><a class="dropdown-item" href="#">Delete</a></li>
															</ul>
														</div>
													</div> <!--[ row end ]-->
												</div>
												<div class="list-group-item">
													<div class="row align-items-center">
														<div class="col-auto">
															<img class="img-fluid" src="/assets/images/mastercard.svg" alt="..." style="max-width: 38px;">                            
														</div>
														<div class="col ml-n2">
															<h6 class="mb-0">Mastercard ending in 2525</h6>
															<small class="text-muted">Expires 11/2024</small>
														</div>

														<!--[ Dropdown ]-->
														<div class="col-auto">
															<a href="#" class="dropdown-toggle after-none" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
															<ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
																<li><a class="dropdown-item" href="#">File Info</a></li>
																<li><a class="dropdown-item" href="#">Copy to</a></li>
																<li><a class="dropdown-item" href="#">Move to</a></li>
																<li><a class="dropdown-item" href="#">Rename</a></li>
																<li><a class="dropdown-item" href="#">Block</a></li>
																<li><a class="dropdown-item" href="#">Delete</a></li>
															</ul>
														</div>

													</div> <!--[ row end ]-->
												</div>
											</div>
										</div>
									</div>
								</li>
								<li class="col-12">
									<h5 class="fw-normal mb-0">Invoices</h5>
									<p class="text-muted">Showing data from</p>
									<div class="table-responsive">
										<table class="table table-border table-hover table-nowrap mb-0">
											<thead>
												<tr>
													<th>Invoice ID</th>
													<th>Date</th>
													<th>Amount</th>
													<th>Status</th>
												</tr>
											</thead>
											<tbody class="font-size-base">
												<tr>
													<td><a href="#">Invoice #10022</a></td>
													<td>Oct. 24, 2020</td>
													<td>$29.00</td>
													<td><span class="badge bg-secondary">Outstanding</span></td>
												</tr>
												<tr>
													<td><a href="#">Invoice #10012</a></td>
													<td>Aug. 11, 2020</td>
													<td>$29.00</td>
													<td><span class="badge bg-success">Paid</span></td>
												</tr>
												<tr>
													<td><a href="#">Invoice #10043</a></td>
													<td>July. 5, 2020</td>
													<td>$29.00</td>
													<td><span class="badge bg-success">Paid</span></td>
												</tr>
												<tr>
													<td><a href="#">Invoice #10045</a></td>
													<td>Jun. 16, 2020</td>
													<td>$29.00</td>
													<td><span class="badge bg-success">Paid</span></td>
												</tr>
											</tbody>
										</table>
									</div>
								</li>
								<li class="col-12">
									<small class="text-muted">Don’t need anymore? <a href="#!">Cancel your account</a></small>
								</li>
							</ul> <!--[ ul.row end ]-->
						</div>
						<!--[ Start:: members ]-->
						<div class="tab-pane fade" id="setting-members" role="tabpanel">
							<div class="d-flex justify-content-between p-3">
								<h5 class="card-title fw-normal mb-0">Members</h5>
								<!--[ Dropdown ]-->
								<div class="dropdown">
									<button class="btn btn-sm btn-primary" type="button" data-bs-toggle="dropdown">Invite member</button>
									<form class="dropdown-menu dropdown-menu-end border-0 rounded-4 shadow" style="width: 300px;">
										<div class="card-header">
											<h6 class="card-title mb-0">Invite a member</h6>
											<span class="badge bg-primary">2 seats left</span>
										</div>
										<div class="card-body">
											<div class="row g-0 align-items-center mb-2">
												<div class="col-3">
													<label class="mb-0">Name</label>
												</div>
												<div class="col">
													<input class="form-control form-control-flush" type="text" placeholder="Full name">
												</div>
											</div> <!--[ row end ]-->
											<div class="row g-0 align-items-center mb-2">
												<div class="col-3">
													<label class="mb-0">Email</label>
												</div>
												<div class="col">
													<input class="form-control form-control-flush" type="text" placeholder="Email address">
												</div>
											</div> <!--[ row end ]-->
											<div class="row g-0 align-items-center">
												<div class="col-3">
													
												</div>
												<div class="col">
													<button class="btn btn-block btn-primary" type="submit">Invite member</button>
												</div>
											</div> <!--[ row end ]-->
										</div>
									</form>
								</div>
							</div>
							<div class="list-group list-group-flush mb-0">
								<div class="list-group-item py-3">
									<div class="row align-items-center">
										<div class="col-auto">
											<a href="#" class="avatar"><img src="/assets/images/xs/avatar1.jpg" alt="..." class="avatar rounded-circle"></a>
										</div>
										<div class="col-5 ms-2">
											<h5 class="mb-0 fw-light"><a href="#">Amelia Green</a></h5>
											<span class="text-muted">amelia.green@company.com</span>
										</div>
										<div class="col-auto ms-auto mr-md-3">
											<select class="form-control custom-select" data-bs-toggle="select">
												<option value="1">Admin</option>
												<option value="2">Staff</option>
												<option value="3">Custom</option>
											</select>
										</div>
										<div class="col-auto">
											<a href="#" class="dropdown-toggle after-none" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
											<ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
												<li><a class="dropdown-item" href="#">File Info</a></li>
												<li><a class="dropdown-item" href="#">Copy to</a></li>
												<li><a class="dropdown-item" href="#">Move to</a></li>
												<li><a class="dropdown-item" href="#">Rename</a></li>
												<li><a class="dropdown-item" href="#">Block</a></li>
												<li><a class="dropdown-item" href="#">Delete</a></li>
											</ul>
										</div>
									</div> <!--[ row end ]-->
								</div>
								<div class="list-group-item py-3">
									<div class="row align-items-center">
										<div class="col-auto">
											<a href="#" class="avatar"><img src="/assets/images/xs/avatar2.jpg" alt="..." class="avatar rounded-circle"></a>
										</div>
										<div class="col-5 ms-2">
											<h5 class="mb-0 fw-light"><a href="#">Charlotte green</a></h5>
											<span class="text-muted">charlotte.green@company.com</span>
										</div>
										<div class="col-auto ms-auto mr-md-3">
											<select class="form-control custom-select" data-bs-toggle="select">
												<option value="1">Admin</option>
												<option value="2">Staff</option>
												<option value="3">Custom</option>
											</select>
										</div>
										<div class="col-auto">
											<a href="#" class="dropdown-toggle after-none" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
											<ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
												<li><a class="dropdown-item" href="#">File Info</a></li>
												<li><a class="dropdown-item" href="#">Copy to</a></li>
												<li><a class="dropdown-item" href="#">Move to</a></li>
												<li><a class="dropdown-item" href="#">Rename</a></li>
												<li><a class="dropdown-item" href="#">Block</a></li>
												<li><a class="dropdown-item" href="#">Delete</a></li>
											</ul>
										</div>
									</div> <!--[ row end ]-->
								</div>
								<div class="list-group-item py-3">
									<div class="row align-items-center">
										<div class="col-auto">
											<a href="#" class="avatar"><img src="/assets/images/xs/avatar3.jpg" alt="..." class="avatar rounded-circle"></a>
										</div>
										<div class="col-5 ms-2">
											<h5 class="mb-0 fw-light"><a href="#">Susie willis</a></h5>
											<span class="text-muted">susie.willis@company.com</span>
										</div>
										<div class="col-auto ms-auto mr-md-3">
											<select class="form-control custom-select" data-bs-toggle="select">
												<option value="1">Admin</option>
												<option value="2">Staff</option>
												<option value="3">Custom</option>
											</select>
										</div>
										<div class="col-auto">
											<a href="#" class="dropdown-toggle after-none" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
											<ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-4">
												<li><a class="dropdown-item" href="#">File Info</a></li>
												<li><a class="dropdown-item" href="#">Copy to</a></li>
												<li><a class="dropdown-item" href="#">Move to</a></li>
												<li><a class="dropdown-item" href="#">Rename</a></li>
												<li><a class="dropdown-item" href="#">Block</a></li>
												<li><a class="dropdown-item" href="#">Delete</a></li>
											</ul>
										</div>
									</div> <!--[ row end ]-->
								</div>
							</div>
						</div>
						<!--[ Start:: security ]-->
						<div class="tab-pane fade" id="setting-security" role="tabpanel">
							<ul class="row g-3 list-unstyled li_animate mb-0">
								<li class="col-12 mb-4">
									<h5 class="card-title fw-normal">Change your password</h5>
									<p class="text-muted">We will email you a confirmation when changing your password, so please expect that email after submitting.</p>
									<button class="btn btn-warning">Forgot your password?</button>
								</li>
								<li class="col-12">
									<form class="row g-3 justify-content-between">
										<div class="col-lg-4 col-md-12">
											<div class="mb-3">
												<label class="form-label">Current password</label>
												<input type="password" class="form-control">
											</div>
											<div class="mb-3">
												<label class="form-label">New password</label>
												<input type="password" class="form-control">
											</div>
											<div class="mb-3">
												<label class="form-label">Confirm password</label>
												<input type="password" class="form-control">
											</div>
											<button type="button" class="btn btn-primary">Update Password</button>
											<button type="button" class="btn btn-link">Cancel</button>
										</div>
										<div class="col-lg-7 col-md-12">
											<div class="bg-light border dashed p-3">
												<h6 class="mb-2">Password Requirements</h6>
												<p class="text-muted mb-2">To create a new password, you have to meet all of the following requirements:</p>
												<!--[ List group ]-->
												<ul class="small text-muted lh-lg mb-0">
													<li>Minimum 8 character</li>
													<li>At least one special character</li>
													<li>At least one number</li>
													<li>Can’t be the same as a previous password</li>
												</ul>
											</div>
										</div>
									</form>
								</li>
								<li class="col-12 mb-4">
									<hr>
									<h5>Device History</h5>
									<p class="text-muted">If you see a device here that you believe wasn’t you, please contact our account fraud department immediately.</p>
									<button class="btn btn-dark">Log out all devices</button>
								</li>
								<li class="col-12">
									<div class="list-group list-group-custom list-group-flush mb-0">
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col-auto">
													<div class="avatar lg text-center"><i class="fa fa-mobile fa-3x"></i></div>
												</div>
												<div class="col ml-n2">
													<h6 class="mb-1">iPhone 11</h6>
													<small class="text-muted">Brownsville, VT · <span>Jun 11 at 10:05am</span></small>
												</div>
												<div class="col-auto">
													<button class="btn btn-sm btn-white">Sign out</button>                            
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col-auto">
													<div class="avatar lg text-center"><i class="fa fa-desktop fa-2x"></i></div>
												</div>
												<div class="col ml-n2">
													<h6 class="mb-1">iMac OSX · <span class="font-weight-normal">Safari 10.2</span></h6>
													<small class="text-muted">Ct, Corona, CA · <span>September 14 at 2:30pm</span></small>
												</div>
												<div class="col-auto">
													<button class="btn btn-sm btn-white">Sign out</button>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col-auto">
													<div class="avatar lg text-center"><i class="fa fa-laptop fa-3x"></i></div>
												</div>
												<div class="col ml-n2">
													<h6 class="mb-1">HP Laptop Win10</h6>
													<small class="text-muted">Ct, Corona, CA · <span>September 16 at 11:16am</span></small>
												</div>
												<div class="col-auto">
													<button class="btn btn-sm btn-white">Sign out</button>                            
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col-auto">
													<div class="avatar lg text-center"><i class="fa fa-desktop fa-2x"></i></div>
												</div>
												<div class="col ml-n2">
													<h6 class="mb-1">iMac OSX · <span class="font-weight-normal">Edge browser</span></h6>
													<small class="text-muted">Huntington Station, NY · <span>October 26 at 5:16pm</span></small>
												</div>
												<div class="col-auto">
													<button class="btn btn-sm btn-white">Sign out</button>
												</div>
											</div>
										</div>
									</div>
								</li>
							</ul> <!--[ ul.row end ]-->
						</div>
						<!--[ Start:: notifications]-->
						<div class="tab-pane fade" id="setting-notifications" role="tabpanel">
							<h5 class="mb-0">Notifications Setting</h5>
							<p class="text-muted">We may still send you important Notifications about your account outside of your Notifications settings.</p>
							<ul class="list-group list-group-flush li_animate">
								<li class="list-group-item d-flex justify-content-between align-items-start py-4">
									<div>
										<h6 class="mb-1">Comments</h6>
										<span class="text-muted">These are notifications for comments on your posts and replies to your comments.</span>
									</div>
									<div class="text-end">
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_email" checked>
											<label class="form-check-label" for="noti_email">Email</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_push" checked>
											<label class="form-check-label" for="noti_push">Push</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_sms">
											<label class="form-check-label" for="noti_sms">SMS</label>
										</div>
									</div>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start py-4">
									<div>
										<h6 class="mb-1">Reminders</h6>
										<span class="text-muted">These are notificatios to remind you of updates you might have missed.</span>
									</div>
									<div class="text-end">
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_email2" checked>
											<label class="form-check-label" for="noti_email2">Email</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_push2" checked>
											<label class="form-check-label" for="noti_push2">Push</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_sms2" checked>
											<label class="form-check-label" for="noti_sms2">SMS</label>
										</div>
									</div>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start py-4">
									<div>
										<h6 class="mb-1">Tags</h6>
										<span class="text-muted">These are notificatios for when someone tags you in comments, postof story.</span>
									</div>
									<div class="text-end">
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_email3" checked>
											<label class="form-check-label" for="noti_email3">Email</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_push3" checked>
											<label class="form-check-label" for="noti_push3">Push</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_sms3">
											<label class="form-check-label" for="noti_sms3">SMS</label>
										</div>
									</div>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start py-4">
									<div>
										<h6 class="mb-1">More activity about you</h6>
										<span class="text-muted">In semper feugiat commodo himenaeos diam integer praesent cras</span>
									</div>
									<div class="text-end text-md-start">
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_email4">
											<label class="form-check-label" for="noti_email4">Email</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_push4">
											<label class="form-check-label" for="noti_push4">Push</label>
										</div>
										<div class="form-check form-switch form-check-inline">
											<input class="form-check-input" type="checkbox" role="switch" id="noti_sms4">
											<label class="form-check-label" for="noti_sms4">SMS</label>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>

			</div>
@endsection

