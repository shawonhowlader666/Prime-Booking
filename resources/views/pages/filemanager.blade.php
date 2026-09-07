@extends('layouts.app')

@section('title', 'Filemanager | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="tuning.html" title="tuning">Home</a></li>
					<li class="breadcrumb-item"><a href="app.html" title="Apps">Apps</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="File manager">File manager</li>
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
				
				<div class="card border border-primary bg-transparent">
					<div class="card-header pb-0 z-2">
						<h6 class="card-title mb-0">File manager</h6>
						<div class="w-100 mt-4">
							<ul class="row g-lg-4 g-2 list-unstyled li_animate mb-4 mb-lg-5">
								<li class="col-xl-4 col-lg-5 col-md-5 col-12">
									<div class="d-flex align-items-center">
										<img class="avatar lg rounded-circle border border-3" src="/assets/images/profile_av.png" alt="avatar">
										<div class="ms-3">
											<h4 class="mb-0 text-gradient title-font">Michelle!</h4>
											<span class="text-muted small">michelle.hotelair@gmail.com</span>
										</div>
									</div>
								</li>
								<li class="col-xl-8 col-lg-7 col-md-7 col-12">
									<ul class="list-unstyled d-none d-lg-flex align-items-start justify-content-md-end p-0 mb-0 ps-5 ps-md-0 ms-4 ms-md-0">
										<li class="px-lg-4 px-3 ps-0">
											<h6>Google drive</h6>
											<small class="text-muted">15 GB (Basic Plan)</small>
											<div class="progress mt-1" style="height: 2px;">
												<div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</li>
										<li class="px-lg-4 px-3 border-start">
											<h6>Dropbox</h6>
											<small class="text-muted">2 GB (Free Plan)</small>
											<div class="progress mt-1" style="height: 2px;">
												<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</li>
										<li class="ps-lg-4 ps-3 border-start">
											<h6>FTP Drive</h6>
											<small class="text-muted">30 GB (Regular plan)</small>
											<div class="progress mt-1" style="height: 2px;">
												<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</li>
									</ul>
								</li>
							</ul>
							<ul class="nav nav-tabs tab-card li_animate px-1 mb-0" role="tablist">
								<li class="nav-item btn-group me-xl-4 me-3 fs-6">
									<a class="nav-link active px-0" data-bs-toggle="tab" href="#drive-my"><span>MY</span> Drive</a>
									<div class="dropdown">
										<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><span class="visually-hidden">Toggle Dropdown</span></a>
										<ul class="li_animate dropdown-menu shadow rounded-4 p-2">
											<li><a class="dropdown-item rounded-pill" href="#">Project's</a></li>
											<li><a class="dropdown-item rounded-pill" href="#">Documentation</a></li>
											<li><a class="dropdown-item rounded-pill" href="#">Bank Statment</a></li>
											<li><hr class="dropdown-divider"></li>
											<li><a class="dropdown-item rounded-pill" href="#">Gallery</a></li>
											<li><a class="dropdown-item rounded-pill" href="#">KYC</a></li>
										</ul>
									</div>
								</li>
								<li class="nav-item"><a class="nav-link px-0 me-xl-4 me-3 fs-6" data-bs-toggle="tab" href="#drive-starred"><i class="fa fa-star"></i><span class="d-none d-md-inline-flex ps-1">Starred</span></a></li>
								<li class="nav-item"><a class="nav-link px-0 me-xl-4 me-3 fs-6" data-bs-toggle="tab" href="#drive-withme"><i class="fa fa-share-alt"></i> <span class="d-none d-md-inline-flex ps-1">Shared with me</span></a></li>
								<li class="nav-item"><a class="nav-link px-0 me-xl-4 me-3 fs-6" data-bs-toggle="tab" href="#drive-trash"><i class="fa fa-trash"></i><span class="d-none d-sm-inline-flex ps-1">Trash</span></a></li>
								<li class="nav-item dropdown ms-auto">
									<a class="btn btn-primary fs-6" href="#" role="button" data-bs-toggle="dropdown"><i class="fa fa-plus-circle me-1"></i>Create</a>
									<ul class="li_animate dropdown-menu dropdown-menu-end shadow rounded-4 p-2">
										<li><a class="dropdown-item rounded-pill" href="#">New folder</a></li>
										<li><hr class="dropdown-divider"></li>
										<li><a class="dropdown-item rounded-pill" href="#">File upload</a></li>
										<li><a class="dropdown-item rounded-pill" href="#">Folder upload</a></li>
										<li><hr class="dropdown-divider"></li>
										<li><a class="dropdown-item rounded-pill" href="#">Create Invoice</a></li>
										<li><a class="dropdown-item rounded-pill" href="#">Create Project</a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body z-1">
						<div class="tab-content">
							<div class="tab-pane fade show active" id="drive-my" role="tabpanel" tabindex="0">
								<ul class="row g-3 list-unstyled li_animate mb-0">
									<li class="col-12">
										<h5 class="mb-0">My Folder</h5>
										<p class="text-muted small">Last Update your drive: 11 Jan 2023</p>
										<div class="file_folder d-flex flex-wrap text-center">
											<a class="card card-border p-4 me-1 mb-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color1);">
												<i class="fa fa-folder fa-2x mb-2 theme-text-color1"></i>
												<h6>Analytics</h6>
												<div class="card-footer pb-0 pt-2">
													<small>215 File, 50MB</small>
												</div>
											</a>
											<a class="card card-border p-4 me-1 mb-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color2);">
												<i class="fa fa-folder fa-2x mb-2 theme-text-color2"></i>
												<h6>Assets</h6>
												<div class="card-footer pb-0 pt-2">
													<small>215 File, 50MB</small>
												</div>
											</a>
											<a class="card card-border p-4 me-1 mb-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color3);">
												<i class="fa fa-folder fa-2x mb-2 theme-text-color3"></i>
												<h6>Marketing</h6>
												<div class="card-footer pb-0 pt-2">
													<small>215 File, 50MB</small>
												</div>
											</a>
											<a class="card card-border p-4 me-1 mb-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color4);">
												<i class="fa fa-folder fa-2x mb-2 theme-text-color4"></i>
												<h6>New App Design</h6>
												<div class="card-footer pb-0 pt-2">
													<small>215 File, 50MB</small>
												</div>
											</a>
										</div>
									</li>
									<li class="col-12 mt-5">
										<h5>Recently Accessed Files</h5>
										<table class="table align-middle table-hover mb-0 dataTable table-border" style="width: 100%;">
											<thead>
												<tr>
													<th class="border-top-0"></th>
													<th class="border-top-0">Name</th>
													<th class="border-top-0">Share With</th>
													<th class="border-top-0">Owner</th>
													<th class="border-top-0">Last Update</th>
													<th class="border-top-0">Size</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style="width: 50px;"><i class="fa fa-folder"></i></td>
													<td><span class="folder-name">Work</span></td>
													<td>
														<div class="avatar-list ps-3">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar1.jpg" data-bs-toggle="tooltip" title="Avatar">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar2.jpg" data-bs-toggle="tooltip" title="Avatar">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar3.jpg" data-bs-toggle="tooltip" title="Avatar">
														</div>
													</td>
													<td><span>Me</span></td>
													<td><span>Oct 7, 2020</span></td>
													<td><span class="size"> - </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-folder"></i></td>
													<td><span class="folder-name">Family</span></td>
													<td>
														-
													</td>
													<td><span>Me</span></td>
													<td><span>Oct 17, 2020</span></td>
													<td><span class="size"> - </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-folder theme-text-color1"></i></td>
													<td><span class="folder-name">Holidays</span></td>
													<td>
														-
													</td>
													<td><span>Jony</span></td>
													<td><span>Oct 18, 2020</span></td>
													<td><span class="size"> 50MB </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-folder"></i></td>
													<td><span class="folder-name">Mobile Work </span></td>
													<td>
														-
													</td>
													<td><span>Me</span></td>
													<td><span>April 7, 2021</span></td>
													<td><span class="size"> 1GB </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-folder theme-text-color3"></i></td>
													<td><span class="folder-name">Photoshop Data</span></td>
													<td>
														<div class="avatar-list ps-3">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar1.jpg" data-bs-toggle="tooltip" title="Avatar">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar2.jpg" data-bs-toggle="tooltip" title="Avatar">
														</div>
													</td>
													<td><span>Andrew</span></td>
													<td><span>Nov 23, 2021</span></td>
													<td><span class="size"> - </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-folder theme-text-color6"></i></td>
													<td><span class="folder-name">Holidays</span></td>
													<td>
														<div class="avatar-list ps-3">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar5.jpg" data-bs-toggle="tooltip" title="Avatar">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar6.jpg" data-bs-toggle="tooltip" title="Avatar">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar1.jpg" data-bs-toggle="tooltip" title="Avatar">
															<img class="avatar sm rounded-circle" src="/assets/images/xs/avatar4.jpg" data-bs-toggle="tooltip" title="Avatar">
														</div>
													</td>
													<td><span>Me</span></td>
													<td><span>Dec 5, 2020</span></td>
													<td><span class="size"> 100MB </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-file-excel-o text-success"></i></td>
													<td><span class="folder-name">new timesheet.xlsx</span></td>
													<td>
														-
													</td>
													<td><span>Me</span></td>
													<td><span>Dec 5, 2020</span></td>
													<td><span class="size"> 52KB </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-file-word-o text-info"></i></td>
													<td><span class="folder-name">new project.doc</span></td>
													<td>
														-
													</td>
													<td><span>Me</span></td>
													<td><span>May 5, 2021</span></td>
													<td><span class="size"> 152KB </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-file-pdf-o text-warning"></i></td>
													<td><span class="folder-name">report.pdf</span></td>
													<td>
														-
													</td>
													<td><span>Me</span></td>
													<td><span>May 2, 2021</span></td>
													<td><span class="size"> 841KB </span></td>
												</tr>
												<tr>
													<td style="width: 50px;"><i class="fa fa-file-pdf-o text-warning"></i></td>
													<td><span class="folder-name">report-2020.pdf</span></td>
													<td>
														-
													</td>
													<td><span>Me</span></td>
													<td><span>Oct 16, 2020</span></td>
													<td><span class="size"> 541KB </span></td>
												</tr>
											</tbody>
										</table>
									</li>
								</ul><!--[ ul.row end ]-->
							</div>
							<div class="tab-pane fade" id="drive-starred" role="tabpanel" tabindex="0">
								<div class="card">
									<div class="card-body text-center p-5">
										<svg width="86" height="73" viewBox="0 0 86 73" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path fill="var(--primary-color)" d="M21.59 18.6738L12.985 61.8549C12.4006 64.7875 14.344 67.6982 17.2767 68.2826L18.5913 68.5446L60.4578 76.8876C63.3904 77.472 66.3011 75.5286 66.8855 72.596L77.3646 20.0101L77.6468 18.5943C78.2312 15.6617 76.2878 12.751 73.3551 12.1666L40.9946 5.71787"/>
											<path d="M21.59 18.6738L12.985 61.8549C12.4006 64.7875 14.344 67.6982 17.2767 68.2826L18.5913 68.5446L60.4578 76.8876C63.3904 77.472 66.3011 75.5286 66.8855 72.596L77.3646 20.0101L77.6468 18.5943C78.2312 15.6617 76.2878 12.751 73.3551 12.1666L40.9946 5.71787" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M40.9951 5.71802L37.7708 21.8983L21.5905 18.6739L40.9951 5.71802Z" fill="#4D6173" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill="var(--accent-color)" d="M58.0265 16.0765L68.6699 58.8008C69.3927 61.7025 67.5896 64.702 64.6879 65.4249L63.3872 65.7489L21.9635 76.0682C19.0619 76.7911 16.0623 74.9879 15.3395 72.0863L2.37804 20.0566L2.02908 18.6558C1.30623 15.7542 3.10938 12.7546 6.01103 12.0317L38.0293 4.05546"/>
											<path d="M58.0265 16.0765L68.6699 58.8008C69.3927 61.7025 67.5896 64.702 64.6879 65.4249L63.3872 65.7489L21.9635 76.0682C19.0619 76.7911 16.0623 74.9879 15.3395 72.0863L2.37804 20.0566L2.02908 18.6558C1.30623 15.7542 3.10938 12.7546 6.01103 12.0317L38.0293 4.05546" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M38.0293 4.05542L42.0174 20.0645L58.0266 16.0764L38.0293 4.05542Z" fill="#4D6173" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M68.4955 22.2298V66.26C68.4955 69.2503 66.0207 71.7251 63.0304 71.7251H61.6899H19.0003C16.0099 71.7251 13.5352 69.2503 13.5352 66.26V12.6402V11.1965C13.5352 8.20621 16.0099 5.73145 19.0003 5.73145H51.9971" fill="white"/>
											<path d="M68.4955 22.2298V66.26C68.4955 69.2503 66.0207 71.7251 63.0304 71.7251H61.6899H19.0003C16.0099 71.7251 13.5352 69.2503 13.5352 66.26V12.6402V11.1965C13.5352 8.20621 16.0099 5.73145 19.0003 5.73145H51.9971" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M51.9971 5.73145V22.2298H68.4955L51.9971 5.73145Z" fill="#4D6173" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill="var(--primary-color)" d="M74.8404 65.1407L60.1856 56.0998L62.2604 52.7397L76.908 61.7922L74.8404 65.1407Z" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<path d="M82.15 69.695L70.9111 62.7552C69.9726 62.1757 69.68 60.9388 70.2595 60.0003C70.8391 59.0618 72.076 58.7693 73.0145 59.3488L84.2534 66.2886C85.1919 66.8681 85.4844 68.105 84.9049 69.0435C84.3254 69.982 83.0885 70.2745 82.15 69.695Z" fill="#4D6173" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<path fill="var(--primary-color)" d="M81.6411 70.4286L67.1811 61.4999C66.8451 61.2924 66.741 60.852 66.9462 60.5066L69.2285 56.8105C69.436 56.4745 69.8764 56.3703 70.2218 56.5756L84.6818 65.5043C85.0178 65.7118 85.1219 66.1522 84.9167 66.4976L82.6344 70.1937C82.4176 70.5319 81.9771 70.6361 81.6411 70.4286Z" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<path fill="var(--primary-color)" d="M37.7403 21.1073C25.3338 24.0414 17.6571 36.4737 20.5912 48.8802C23.523 61.2774 35.9576 68.9634 48.3547 66.0316C60.7612 63.0975 68.4379 50.6652 65.5039 38.2587C62.5698 25.8522 50.1375 18.1755 37.7403 21.1073ZM47.3802 61.8689C37.4194 64.2245 27.4123 57.9334 25.0212 47.8227C22.6301 37.7119 28.7612 27.6141 38.722 25.2584C48.6829 22.9028 58.6899 29.1939 61.081 39.3046C63.4721 49.4154 57.3316 59.5154 47.3802 61.8689Z" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<ellipse cx="43.0139" cy="43.4264" rx="18.0856" ry="18.2322" transform="rotate(-13.3056 43.0139 43.4264)" fill="white"/>
											<path fill="var(--primary-color)" d="M50.2239 44.0262C51.5954 44.0212 52.7667 42.8413 52.7616 41.4698C52.7566 40.0983 51.5768 38.9271 50.2053 38.9321C48.8338 38.9371 47.6625 40.117 47.6675 41.4885C47.6729 42.9579 48.7544 44.0316 50.2239 44.0262Z"/>
											<path fill="var(--primary-color)" d="M48.7353 50.8377L47.4618 50.8423C46.3756 48.4951 43.5314 47.6238 41.2825 48.8076C40.4023 49.2027 39.6214 49.9893 39.2328 50.8724L37.9593 50.8771C39.0258 47.8362 42.5468 46.2559 45.49 47.4207C46.8636 48.0035 48.1415 49.1744 48.7353 50.8377Z"/>
											<path fill="var(--primary-color)" d="M35.6536 44.0978C37.0251 44.0927 38.1963 42.9129 38.1913 41.5414C38.1863 40.1699 37.0065 38.9986 35.635 39.0036C34.2635 39.0086 33.0922 40.1885 33.0972 41.56C33.2005 43.0291 34.2821 44.1028 35.6536 44.0978Z"/>
										</svg>
											
										<div class="mt-4 mb-3">
											<span class="text-muted">No data to show</span>
										</div>
										<button type="button" class="btn btn-white border">Get Started</button>
										<button type="button" class="btn btn-primary border">Back to Home</button>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="drive-withme" role="tabpanel" tabindex="0">
								<div class="card">
									<div class="card-body text-center p-5">
										<svg width="86" height="73" viewBox="0 0 86 73" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path fill="var(--primary-color)" d="M21.59 18.6738L12.985 61.8549C12.4006 64.7875 14.344 67.6982 17.2767 68.2826L18.5913 68.5446L60.4578 76.8876C63.3904 77.472 66.3011 75.5286 66.8855 72.596L77.3646 20.0101L77.6468 18.5943C78.2312 15.6617 76.2878 12.751 73.3551 12.1666L40.9946 5.71787"/>
											<path d="M21.59 18.6738L12.985 61.8549C12.4006 64.7875 14.344 67.6982 17.2767 68.2826L18.5913 68.5446L60.4578 76.8876C63.3904 77.472 66.3011 75.5286 66.8855 72.596L77.3646 20.0101L77.6468 18.5943C78.2312 15.6617 76.2878 12.751 73.3551 12.1666L40.9946 5.71787" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M40.9951 5.71802L37.7708 21.8983L21.5905 18.6739L40.9951 5.71802Z" fill="#4D6173" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill="var(--accent-color)" d="M58.0265 16.0765L68.6699 58.8008C69.3927 61.7025 67.5896 64.702 64.6879 65.4249L63.3872 65.7489L21.9635 76.0682C19.0619 76.7911 16.0623 74.9879 15.3395 72.0863L2.37804 20.0566L2.02908 18.6558C1.30623 15.7542 3.10938 12.7546 6.01103 12.0317L38.0293 4.05546"/>
											<path d="M58.0265 16.0765L68.6699 58.8008C69.3927 61.7025 67.5896 64.702 64.6879 65.4249L63.3872 65.7489L21.9635 76.0682C19.0619 76.7911 16.0623 74.9879 15.3395 72.0863L2.37804 20.0566L2.02908 18.6558C1.30623 15.7542 3.10938 12.7546 6.01103 12.0317L38.0293 4.05546" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M38.0293 4.05542L42.0174 20.0645L58.0266 16.0764L38.0293 4.05542Z" fill="#4D6173" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M68.4955 22.2298V66.26C68.4955 69.2503 66.0207 71.7251 63.0304 71.7251H61.6899H19.0003C16.0099 71.7251 13.5352 69.2503 13.5352 66.26V12.6402V11.1965C13.5352 8.20621 16.0099 5.73145 19.0003 5.73145H51.9971" fill="white"/>
											<path d="M68.4955 22.2298V66.26C68.4955 69.2503 66.0207 71.7251 63.0304 71.7251H61.6899H19.0003C16.0099 71.7251 13.5352 69.2503 13.5352 66.26V12.6402V11.1965C13.5352 8.20621 16.0099 5.73145 19.0003 5.73145H51.9971" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M51.9971 5.73145V22.2298H68.4955L51.9971 5.73145Z" fill="#4D6173" stroke="#4D6173" stroke-width="1.61896" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
											<path fill="var(--primary-color)" d="M74.8404 65.1407L60.1856 56.0998L62.2604 52.7397L76.908 61.7922L74.8404 65.1407Z" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<path d="M82.15 69.695L70.9111 62.7552C69.9726 62.1757 69.68 60.9388 70.2595 60.0003C70.8391 59.0618 72.076 58.7693 73.0145 59.3488L84.2534 66.2886C85.1919 66.8681 85.4844 68.105 84.9049 69.0435C84.3254 69.982 83.0885 70.2745 82.15 69.695Z" fill="#4D6173" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<path fill="var(--primary-color)" d="M81.6411 70.4286L67.1811 61.4999C66.8451 61.2924 66.741 60.852 66.9462 60.5066L69.2285 56.8105C69.436 56.4745 69.8764 56.3703 70.2218 56.5756L84.6818 65.5043C85.0178 65.7118 85.1219 66.1522 84.9167 66.4976L82.6344 70.1937C82.4176 70.5319 81.9771 70.6361 81.6411 70.4286Z" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<path fill="var(--primary-color)" d="M37.7403 21.1073C25.3338 24.0414 17.6571 36.4737 20.5912 48.8802C23.523 61.2774 35.9576 68.9634 48.3547 66.0316C60.7612 63.0975 68.4379 50.6652 65.5039 38.2587C62.5698 25.8522 50.1375 18.1755 37.7403 21.1073ZM47.3802 61.8689C37.4194 64.2245 27.4123 57.9334 25.0212 47.8227C22.6301 37.7119 28.7612 27.6141 38.722 25.2584C48.6829 22.9028 58.6899 29.1939 61.081 39.3046C63.4721 49.4154 57.3316 59.5154 47.3802 61.8689Z" stroke="#4D6173" stroke-width="0.930904" stroke-miterlimit="10"/>
											<ellipse cx="43.0139" cy="43.4264" rx="18.0856" ry="18.2322" transform="rotate(-13.3056 43.0139 43.4264)" fill="white"/>
											<path fill="var(--primary-color)" d="M50.2239 44.0262C51.5954 44.0212 52.7667 42.8413 52.7616 41.4698C52.7566 40.0983 51.5768 38.9271 50.2053 38.9321C48.8338 38.9371 47.6625 40.117 47.6675 41.4885C47.6729 42.9579 48.7544 44.0316 50.2239 44.0262Z"/>
											<path fill="var(--primary-color)" d="M48.7353 50.8377L47.4618 50.8423C46.3756 48.4951 43.5314 47.6238 41.2825 48.8076C40.4023 49.2027 39.6214 49.9893 39.2328 50.8724L37.9593 50.8771C39.0258 47.8362 42.5468 46.2559 45.49 47.4207C46.8636 48.0035 48.1415 49.1744 48.7353 50.8377Z"/>
											<path fill="var(--primary-color)" d="M35.6536 44.0978C37.0251 44.0927 38.1963 42.9129 38.1913 41.5414C38.1863 40.1699 37.0065 38.9986 35.635 39.0036C34.2635 39.0086 33.0922 40.1885 33.0972 41.56C33.2005 43.0291 34.2821 44.1028 35.6536 44.0978Z"/>
										</svg>
											
										<div class="mt-4 mb-3">
											<span class="text-muted">No data to show</span>
										</div>
										<button type="button" class="btn btn-white border">Get Started</button>
										<button type="button" class="btn btn-primary border">Back to Home</button>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="drive-trash" role="tabpanel" tabindex="0">
								<div class="file_folder d-flex flex-wrap text-center">
									<a class="card card-border p-4 m-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color1);">
										<i class="fa fa-folder fa-2x mb-2 theme-text-color1"></i>
										<h6>Analytics</h6>
										<div class="card-footer pb-0 pt-2">
											<small>215 File, 50MB</small>
										</div>
									</a>
									<a class="card card-border p-4 m-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color2);">
										<i class="fa fa-folder fa-2x mb-2 theme-text-color2"></i>
										<h6>Assets</h6>
										<div class="card-footer pb-0 pt-2">
											<small>215 File, 50MB</small>
										</div>
									</a>
									<a class="card card-border p-4 m-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color3);">
										<i class="fa fa-folder fa-2x mb-2 theme-text-color3"></i>
										<h6>Marketing</h6>
										<div class="card-footer pb-0 pt-2">
											<small>215 File, 50MB</small>
										</div>
									</a>
									<a class="card card-border p-4 m-1" href="javascript:void(0);" style="--dynamic-color: var(--theme-color4);">
										<i class="fa fa-folder fa-2x mb-2 theme-text-color4"></i>
										<h6>New App Design</h6>
										<div class="card-footer pb-0 pt-2">
											<small>215 File, 50MB</small>
										</div>
									</a>
								</div>
								<p class="small text-danger mt-3">Items in trash are deleted forever after 30 days</p>
							</div>
						</div>
					</div>
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
