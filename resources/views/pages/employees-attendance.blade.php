@extends('layouts.app')

@section('title', 'Employees Attendance | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index-hr.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Attendance">Attendance</li>
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
							<h3 class="fw-bold mb-0">Attendance</h3>
							<div class="col-auto d-flex mt-1 mt-sm-0">
                                <button type="button" class="btn btn-dark w-sm-100 me-2" data-bs-toggle="modal" data-bs-target="#editattendance">Edit Attendance</button>
                                <button class="btn btn-primary dropdown-toggle" type="button" id="btn_Filter" data-bs-toggle="dropdown" aria-expanded="false">Filter</button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="btn_Filter">
                                    <li><a class="dropdown-item" href="#">All</a></li>
                                    <li><a class="dropdown-item" href="#">Today Absent</a></li>
                                    <li><a class="dropdown-item" href="#">Week Absent</a></li>
                                  </ul>
                            </div>
						</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
								<ul class="nav nav-tabs px-0" role="tablist">
									<li class="nav-item dropdown">
										<a class="nav-link gray-6 active dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">Month</a>
										<ul class="dropdown-menu border-0 shadow py-3 px-2" data-popper-placement="top-start" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(0px, -44px);">
											<li><a class="dropdown-item py-1 rounded active" href="#">Jan 2023</a></li>
											<li><a class="dropdown-item py-1 rounded" href="#">Feb 2023</a></li>
											<li><a class="dropdown-item py-1 rounded" href="#">March 2023</a></li>
											<li><a class="dropdown-item py-1 rounded" href="#">Apr 2023</a></li>
											<li><a class="dropdown-item py-1 rounded" href="#">May 2023</a></li>
										</ul>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link gray-6" data-bs-toggle="tab" href="#nav-week1" role="tab" aria-selected="true">
											<i class="bi bi-check2 text-success me-1"></i>
											<span>Full Day Present</span>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link gray-6" data-bs-toggle="tab" href="#nav-week1" role="tab" aria-selected="false" tabindex="-1">
											<i class="bi bi-clock text-warning me-1"></i>
											<span>Half Day Present</span>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link gray-6" data-bs-toggle="tab" href="#nav-week1" role="tab" aria-selected="false" tabindex="-1">
											<i class="bi bi-x text-danger me-1"></i>
											<span>Full Day Absence</span>
										</a>
									</li>
								</ul>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th>Employee</th>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>
                                                <th>25</th>
                                                <th>27</th>
                                                <th>28</th>
                                                <th>29</th>
                                                <th>30</th>
                                                <th>31</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                            	<td>Joan Dyer</td>
												<td><i class="bi bi-check2 text-success"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
												<td><i class="bi bi-clock text-warning"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
												<td><i class="bi bi-clock text-warning"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
											</tr>
											<tr>
                                                <td>Ryan Randall</td>
                                                <td><i class="bi bi-x text-danger"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-x text-danger"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-x text-danger"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Phil Glover</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Victor Rampling</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Sally Graham</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Robert Anderson</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Ryan Stewart</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                            </tr>
											<tr>
                                                <td>Ryan Randall</td>
                                                <td><i class="bi bi-x text-danger"></i></td>
												<td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-x text-danger"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-x text-danger"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Phil Glover</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Victor Rampling</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Sally Graham</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-clock text-warning"></i></td>
                                            </tr>
                                            <tr>
                                                <td>Robert Anderson</td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                                <td><i class="bi bi-check2 text-success"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                  </div>
                </div><!-- Row End -->

			</div>
@endsection

