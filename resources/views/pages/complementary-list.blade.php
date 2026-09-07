@extends('layouts.app')

@section('title', 'Complementary List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Complementary List">Complementary List</li>
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
							<h3 class="fw-bold mb-0">Complementary List</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-complementary">Add Complementary</button>
							</div>
						</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Room Type</th>
											<th>Complementary</th>
											<th>Complementary Rate</th> 
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@if(isset($services) && $services->count() > 0)
											@foreach($services as $item)
												<tr>
													<td>{{ $item->id + 1100 }}</td>
													<td><strong>{{ $item->title }}</strong></td>
													<td><span class="badge bg-light text-dark border">Included</span></td>
													<td><span class="badge bg-success">Free / 0%</span></td>
													<td>
														<form action="{{ route('room.complementary.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this complementary item?');">
															@csrf
															@method('DELETE')
															<button type="submit" class="btn deleterow p-0 border-0 bg-transparent"><i class="bi bi-trash text-danger"></i></button>
														</form>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>1101</td>
												<td>Family Room</td>
												<td>Online</td>
												<td>5%</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-complementary"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1102</td>
												<td>Deluxe Suite</td>
												<td>Online</td>
												<td>15%</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-complementary"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1103</td>
												<td>Standard Room</td>
												<td>Online</td>
												<td>8%</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-complementary"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1104</td>
												<td>Single Room</td>
												<td>Offline</td>
												<td>10%</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-complementary"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1105</td>
												<td>Executive Suite</td>
												<td>Online</td>
												<td>15%</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-complementary"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
										@endif
									</tbody>
                                </table>
                            </div>
                        </div>
                  </div>
                </div><!-- Row End -->
			</div>

	<!-- Add Complementary Modal -->
	<div class="modal fade" id="add-complementary" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Add Complementary</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('room.complementary.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label text-muted">Complementary Title *</label>
						<input type="text" name="title" class="form-control" required placeholder="e.g. Free Breakfast Buffet, Airport Shuttle">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Description</label>
						<textarea name="description" class="form-control" placeholder="Service details..."></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
		</div>
	</div>

	<!-- Edit Complementary Modal -->
	<div class="modal fade" id="edit-complementary" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Edit Complementary</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Type</label>
					<input type="text" class="form-control" value="Family Room">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Complementary</label>
					<input type="text" class="form-control" value="Online">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Complementary Rate</label>
					<input type="text" class="form-control" value="5%">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Update changes</button>
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
