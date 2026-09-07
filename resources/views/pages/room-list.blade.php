@extends('layouts.app')

@section('title', 'Room List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Room List">Room List</li>
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
							<form method="GET" action="{{ route('rooms.list') }}">
								<div class="row g-3 mt-3">
									<div class="col-12">
										<label class="form-label small text-muted">Sorted by:</label>
										<input type="search" name="search" class="form-control" placeholder="Search room or type" value="{{ request('search') }}">
									</div>
									<div class="col-12 text-end mt-4">
										<a href="{{ route('rooms.list') }}" class="btn btn-sm btn-light me-1">Reset</a>
										<button type="submit" class="btn btn-sm btn-primary">Apply</button>
									</div>
								</div>
							</form>
						</div>
					</li>
				</ul>
			</div>

<div class="px-xl-5 px-lg-4 px-3 py-3 page-body">
                <div class="row g-3">
					<div class="col-sm-12">
						<div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="fw-bold mb-0">Room List</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="offcanvas" data-bs-target="#add-room" aria-controls="add-room">Add Room</button>
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
											<th>Room Price</th>
											<th>Bed Charge</th> 
											<th>Capacity</th>
											<th>Room Size</th>
											<th>Bed No</th>
											<th>Bed Type</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										@if(isset($rooms) && $rooms->count() > 0)
											@foreach($rooms as $room)
												<tr>
													<td>{{ $room->room_number }}</td>
													<td><strong>{{ $room->roomType?->name ?? 'Standard' }}</strong></td>
													<td>${{ number_format($room->roomType?->base_price ?? 5000, 2) }}</td>
													<td>$100</td>
													<td>{{ $room->roomType?->capacity ?? 2 }}</td>
													<td>King</td>
													<td>1</td>
													<td>Standard Double</td>
													<td>
														<button type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#edit-room" aria-controls="edit-room"><i class="bi bi-pencil-square"></i></button>
														<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>101</td>
												<td>Vip Guest</td>
												<td>$4,000.00</td>
												<td>$100</td>
												<td>3</td>
												<td>King</td>
												<td>2</td>
												<td>Standard Double</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#edit-room" aria-controls="edit-room"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>102</td>
												<td>Vip</td>
												<td>$10,000.00</td>
												<td>$400</td>
												<td>4</td>
												<td>King</td>
												<td>1</td>
												<td>Electric Bed</td>
												<td>
													<button type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#edit-room" aria-controls="edit-room"><i class="bi bi-pencil-square"></i></button>
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

	<!-- Add: Room Offcanvas -->
	<div class="offcanvas offcanvas-end" tabindex="-1" id="add-room" aria-labelledby="add-room">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title">Add Room</h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			<form action="{{ route('rooms.store') }}" method="POST">
				@csrf
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Number <span class="text-danger">*</span></label>
					<input type="text" name="room_number" class="form-control" placeholder="e.g. 101, 102" required>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Type <span class="text-danger">*</span></label>
					<select name="room_type_id" class="form-select">
						@if(isset($roomTypes))
							@foreach($roomTypes as $type)
								<option value="{{ $type->id }}">{{ $type->name }} (${{ number_format($type->base_price, 2) }})</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Floor Number</label>
					<input type="number" name="floor" class="form-control" value="1">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Capacity <span class="text-danger">*</span></label>
					<input type="number" name="capacity" class="form-control" placeholder="Capacity" value="2">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Price<span class="text-danger">*</span></label>
					<input type="number" name="room_price" class="form-control" placeholder="Room Price" value="5000">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Bed Charge<span class="text-danger">*</span></label>
					<input type="number" class="form-control" placeholder="Bed Charge" value="100">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Size<span class="text-danger">*</span></label>
					<select class="form-select">
						<option selected>King</option>
						<option value="1">Single</option>
						<option value="2">Double</option>
						<option value="3">Triple</option>
						<option value="4">Quad</option>
						<option value="6">Queen</option>
						<option value="7">King</option>
						<option value="8">Others</option>
					</select>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Bed Number<span class="text-danger">*</span></label>
					<input type="number" class="form-control" placeholder="Bed Number" value="1">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Bed Type<span class="text-danger">*</span></label>
					<select class="form-select">
						<option selected>King Bed</option>
						<option value="1">King Bed</option>
						<option value="2">Queen Bed</option>
						<option value="3">Electric Bed</option>
						<option value="4">Futon Bed</option>
					</select>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Description</label>
					<textarea class="form-control"></textarea>
				</div>
				<div class="d-flex">
					<button type="reset" class="btn w-100 me-1 py-2 btn-secondary">Clear</button>
					<button type="submit" class="btn w-100 ms-1 py-2 btn-dark">Save</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Edit: Room Offcanvas -->
	<div class="offcanvas offcanvas-end" tabindex="-1" id="edit-room" aria-labelledby="edit-room">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title">Edit Room</h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			<form>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Type <span class="text-danger">*</span></label>
					<input type="text" class="form-control" value="VIP-Guest">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Capacity <span class="text-danger">*</span></label>
					<input type="number" class="form-control" value="3">
				</div>
				<div class="d-flex">
					<button type="button" class="btn w-100 me-1 py-2 btn-secondary" data-bs-dismiss="offcanvas">Close</button>
					<button type="button" class="btn w-100 ms-1 py-2 btn-primary" data-bs-dismiss="offcanvas">Update</button>
				</div>
			</form>
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
