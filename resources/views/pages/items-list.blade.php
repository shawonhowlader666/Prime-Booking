@extends('layouts.app')

@section('title', 'Items List | HotelAir')

@section('content')
<div class="px-xl-5 px-lg-4 px-3 py-2 page-header">
	<ol class="breadcrumb mb-0 bg-transparent">
		<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" title="home">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page" title="Items List">Items List</li>
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
				<h3 class="fw-bold mb-0">Items List</h3>
				<div class="col-auto d-flex w-sm-100">
					<button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#add-items">Add Items</button>
				</div>
			</div>
	    </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table class="table dataTable align-middle table-hover table-body" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Items Id</th>
                                <th>Items Name</th>
								<th>Category Name</th>
								<th>Unit</th>
								<th>Total Items</th>
								<th>Items Used</th>
								<th>Laundry Item</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
							@forelse($items as $item)
							<tr>
								<td>{{ $item->sku ?? (1100 + $item->id) }}</td>
								<td>{{ $item->name }}</td>
								<td>{{ $item->category->name ?? 'House Keeping' }}</td>
								<td>Per {{ $item->unit->name ?? 'Pieces' }}</td>
								<td>{{ number_format($item->current_stock, 0) }}</td>
								<td>{{ number_format($item->min_alert_stock, 0) }}</td>
								<td>{{ $item->category && str_contains(strtolower($item->category->name), 'linen') ? 'Yes' : 'No' }}</td>
								<td>
									<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#edit-items{{ $item->id }}"><i class="bi bi-pencil-square"></i></button>
									<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
								</td>
							</tr>
							@empty
							<tr>
								<td>1101</td>
								<td>Bed Cover</td>
								<td>House Keeping</td>
								<td>Per Pieces</td>
								<td>70</td>
								<td>20</td>
								<td>Yes</td>
								<td>
									<button type="button" class="btn"><i class="bi bi-pencil-square"></i></button>
									<button type="button" class="btn deleterow"><i class="bi bi-trash text-danger"></i></button>
								</td>
							</tr>
							@endforelse
						</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- Row End -->
</div>

<!-- Add Facilites Modal from Original Template -->
<div class="modal fade" id="add-items" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h1 class="modal-title fs-5">Add Items</h1>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<form action="{{ route('inventory.items.store') }}" method="POST">
				@csrf
				<div class="form-group mb-3">
					<label class="form-label text-muted">Unit Name</label>
					<select name="unit_id" class="form-select">
						<option selected value="">Choose Unit</option>
						@foreach($units as $u)
							<option value="{{ $u->id }}">{{ $u->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Category Name</label>
					<select name="category_id" class="form-select">
						<option selected value="">Choose Category</option>
						@foreach($categories as $c)
							<option value="{{ $c->id }}">{{ $c->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Item Name</label>
					<input type="text" name="name" class="form-control" required placeholder="Add Item Name">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Purchase Cost</label>
					<input type="number" step="0.01" name="purchase_price" class="form-control" required placeholder="Purchase Cost">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Total Item</label>
					<input type="number" name="current_stock" class="form-control" placeholder="Total Item" value="10">
				</div>
				<div class="form-group mb-3">
					<label class="form-label text-muted">Min Alert Stock</label>
					<input type="number" name="min_alert_stock" class="form-control" placeholder="Min Alert Stock" value="5">
				</div>
				<div class="modal-footer px-0 pb-0">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready( function () {
    if ($.fn.DataTable.isDataTable('.dataTable')) {
        $('.dataTable').DataTable().destroy();
    }
    $('.dataTable')
    .addClass( 'nowrap' )
    .dataTable( {
        responsive: true,
    });
});
</script>
@endpush
