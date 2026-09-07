@extends('layouts.app')

@section('title', 'Room Images | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Room Images">Room Images</li>
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
							<h3 class="fw-bold mb-0">Room Images</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-image">Add Room Images</button>
							</div>
						</div>
				  </div>
                  <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Room Id</th>
                                            <th>Room Type</th>
											<th>Room Images</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@if(isset($images) && $images->count() > 0)
											@foreach($images as $img)
												<tr>
													<td>{{ $img->room?->room_number ?? ('RT-' . $img->room_type_id) }}</td>
													<td><strong>{{ $img->roomType?->name ?? 'Standard Room' }}</strong></td>
													<td><img src="{{ $img->image_url }}" class="img-fluid avatar xl rounded" alt="room-image"></td>
													<td>
														<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-image"><i class="bi bi-pencil-square"></i></button>
														<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>1101</td>
												<td>VIP-Guest</td>
												<td><img src="assets/images/gallery/20.jpg" class="img-fluid avatar xl rounded" alt="vip-guest"> </td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-image"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1102</td>
												<td>VIP</td>
												<td><img src="assets/images/gallery/17.jpg" class="img-fluid avatar xl rounded" alt="VIP"> </td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-image"><i class="bi bi-pencil-square"></i></button>
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

	<!-- Add Image Modal -->
	<div class="modal fade" id="add-image" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Add Room Image</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form action="{{ route('room.images.store') }}" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group mb-3">
							<label class="form-label text-muted">Room Type <span class="text-danger">*</span></label>
							<select name="room_type_id" class="form-select">
								@if(isset($roomTypes))
									@foreach($roomTypes as $type)
										<option value="{{ $type->id }}">{{ $type->name }}</option>
									@endforeach
								@endif
							</select>
						</div>
						<div class="form-group mb-3">
							<label class="form-label text-muted">Image URL / Path</label>
							<input type="text" name="image_url" class="form-control" placeholder="assets/images/gallery/20.jpg" value="assets/images/gallery/20.jpg">
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

	<!-- Edit Image Modal -->
	<div class="modal fade" id="edit-image" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Edit Room Image</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form>
						<div class="form-group mb-3">
							<label class="form-label text-muted">Room Type</label>
							<select class="form-select">
								<option selected>VIP-Guest</option>
								<option value="1">VIP</option>
								<option value="2">Triple Room</option>
								<option value="3">Single Room</option>
								<option value="4">Twin Room</option>
							</select>
						</div>
						<div class="form-group">
							<label class="form-label text-muted">Room Images</label>
							<input type="file" class="form-control">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Update changes</button>
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
