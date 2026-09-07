@extends('layouts.app')

@section('title', 'Promocode List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
				<ol class="breadcrumb mb-0 bg-transparent">
					<li class="breadcrumb-item"><a href="index.html" title="home">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page" title="Promocode List">Promocode List</li>
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
							<form method="GET" action="{{ route('room.promocodes') }}">
								<div class="row g-3 mt-3">
									<div class="col-12">
										<label class="form-label small text-muted">Sorted by:</label>
										<input type="search" name="search" class="form-control" placeholder="Search Code" value="{{ request('search') }}">
									</div>
									<div class="col-12 text-end mt-4">
										<a href="{{ route('room.promocodes') }}" class="btn btn-sm btn-light me-1">Reset</a>
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
							<h3 class="fw-bold mb-0">Promocode List</h3>
							<div class="col-auto d-flex w-sm-100">
								<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-promocode">Add Promocode</button>
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
											<th>From</th>
											<th>To</th>
											<th>Discount</th>
											<th>Promocode</th>
											<th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										@if(isset($promocodes) && $promocodes->count() > 0)
											@foreach($promocodes as $item)
												<tr>
													<td>{{ $item->room_type_id ?? 'All' }}</td>
													<td><strong>{{ $item->roomType?->name ?? 'All Rooms' }}</strong></td>
													<td>{{ $item->valid_from ? \Carbon\Carbon::parse($item->valid_from)->format('d/m/Y') : 'Anytime' }}</td>
													<td>{{ $item->valid_to ? \Carbon\Carbon::parse($item->valid_to)->format('d/m/Y') : 'Lifetime' }}</td>
													<td>{{ number_format($item->discount_value, 0) }}</td>
													<td><span class="badge bg-light text-primary border">{{ $item->code }}</span></td>
													<td><span class="badge {{ $item->status === 'ACTIVE' ? 'text-bg-success' : 'text-bg-danger' }}">{{ $item->status === 'ACTIVE' ? 'Apply' : 'Expire' }}</span></td>
													<td>
														<form action="{{ route('room.promocodes.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this promocode?');">
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
												<td>VIP-Guest</td>
												<td>11/01/2024</td>
												<td>21/09/2024</td>
												<td>200</td>
												<td>flat-200</td>
												<td><span class="badge text-bg-success">Apply</span></td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-promocode"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1102</td>
												<td>VIP</td>
												<td>11/01/2024</td>
												<td>12/01/2024</td>
												<td>500</td>
												<td>flat-500</td>
												<td><span class="badge text-bg-danger">Expire</span></td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-promocode"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1103</td>
												<td>Triple Room</td>
												<td>11/01/2024</td>
												<td>14/01/2024</td>
												<td>300</td>
												<td>flat-300</td>
												<td><span class="badge text-bg-danger">Expire</span></td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-promocode"><i class="bi bi-pencil-square"></i></button>
													<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
												</td>
											</tr>
											<tr>
												<td>1104</td>
												<td>Executive Suite</td>
												<td>25/01/2024</td>
												<td>04/02/2024</td>
												<td>100</td>
												<td>flat-100</td>
												<td><span class="badge text-bg-success">Apply</span></td>
												<td>
													<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-promocode"><i class="bi bi-pencil-square"></i></button>
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

	<!-- Add Promocode Modal -->
	<div class="modal fade" id="add-promocode" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Add Promocode</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('room.promocodes.store') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label text-muted">Promocode *</label>
						<input type="text" name="code" class="form-control" required placeholder="e.g. SUMMER500">
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Room Type</label>
						<select name="room_type_id" class="form-select">
							<option value="" selected>All Room Types</option>
							@if(isset($roomTypes))
								@foreach($roomTypes as $rt)
									<option value="{{ $rt->id }}">{{ $rt->name }}</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="form-group mb-3">
						<label class="form-label text-muted">Discount Value (Flat / Amount) *</label>
						<input type="number" name="discount_value" step="0.01" class="form-control" required placeholder="500.00">
					</div>
					<div class="row g-2 mb-3">
						<div class="col-6">
							<label class="form-label text-muted">Valid From</label>
							<input type="date" name="valid_from" class="form-control">
						</div>
						<div class="col-6">
							<label class="form-label text-muted">Valid To</label>
							<input type="date" name="valid_to" class="form-control">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save Promocode</button>
				</div>
			</form>
		</div>
		</div>
	</div>

	<!-- Edit Promocode Modal -->
	<div class="modal fade" id="edit-promocode" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Edit Promocode</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form-group mb-3">
					<label class="form-label text-muted">Promocode</label>
					<input type="text" class="form-control" value="flat-200">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Room Type</label>
					<select class="form-select">
						<option selected>VIP-Guest</option>
						<option value="1">VIP</option>
						<option value="2">Triple Room</option>
						<option value="3">Executive Suite</option>
					</select>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Discount</label>
					<input type="text" class="form-control" value="200">
				</div>
				<div class="row g-2 mb-3">
					<div class="col-6">
						<label class="form-label text-muted">Start Date</label>
						<input type="date" class="form-control" value="2024-01-11">
					</div>
					<div class="col-6">
						<label class="form-label text-muted">End Date</label>
						<input type="date" class="form-control" value="2024-09-21">
					</div>
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
