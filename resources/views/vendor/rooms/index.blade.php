@extends('layouts.vendor')
@section('title', 'Manage Room Types — ' . $property->name)

@php use App\Services\CurrencyService; @endphp

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Room Inventory: {{ $property->name }}</h1>
        <div>
            <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="fa-solid fa-plus me-1"></i> Add Room Category
            </button>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('vendor.properties.index') }}">My Properties</a>
        <span class="sep">-</span><strong style="color:#333;">{{ $property->name }} Rooms</strong>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:8px;">
        <div class="saas-table-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 border-bottom">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-bed me-1.5 text-primary"></i> Registered Room Categories ({{ $property->rooms->count() }})</h6>
            <div style="width:220px;">
                <input type="text" class="form-control form-control-sm" placeholder="Search room type..." onkeyup="filterTableSearch('roomsTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table stockifly-data-table align-middle mb-0" id="roomsTable">
                <thead>
                    <tr>
                        <th>Room Category Name</th>
                        <th>Bed Type</th>
                        <th>Max Occupancy</th>
                        <th>Base Price / Night</th>
                        <th>Total Units</th>
                        <th>Inclusions</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($property->rooms as $r)
                    <tr>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $r->name }}</strong>
                            <span style="font-size:11px; color:#64748b;">ID #{{ $r->id }}</span>
                        </td>
                        <td><span style="font-size:12.5px; color:#334155;">{{ $r->bed_type ?: 'Standard Bed' }}</span></td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:11.5px;">
                                <i class="fa-solid fa-user me-1"></i> {{ $r->max_adults ?? 2 }} Adults @if($r->max_children), {{ $r->max_children }} Child @endif
                            </span>
                        </td>
                        <td><strong style="color:#2067e1; font-size:14px;">BDT ৳{{ number_format($r->price_per_night) }}</strong></td>
                        <td><span class="badge bg-success-light text-success fw-bold" style="font-size:11px;">{{ $r->total_rooms ?? 10 }} Units</span></td>
                        <td>
                            @if($r->breakfast_included)
                                <span class="badge bg-success text-white" style="font-size:10px;">Free Breakfast</span>
                            @endif
                            @if($r->free_cancellation)
                                <span class="badge bg-info text-white ms-1" style="font-size:10px;">Free Cancellation</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $r->id }}" title="Edit Room">
                                    <i class="fa-solid fa-pen text-primary"></i>
                                </button>
                                <form action="{{ route('vendor.rooms.destroy', [$property->id, $r->id]) }}" method="POST" onsubmit="return confirm('Delete this room type?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete Room">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- EDIT ROOM MODAL --}}
                            <div class="modal fade text-start" id="editRoomModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius:8px;">
                                        <form action="{{ route('vendor.rooms.update', [$property->id, $r->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom py-2.5 px-3">
                                                <h6 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-primary me-1"></i> Edit Room: {{ $r->name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Room Name <span style="color:#ff4d4f;">*</span></label>
                                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $r->name }}" required>
                                                </div>
                                                <div class="row g-2 mb-2">
                                                    <div class="col-6">
                                                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Price / Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                                                        <input type="number" step="0.01" name="price_per_night" class="form-control form-control-sm" value="{{ $r->price_per_night }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Bed Configuration</label>
                                                        <input type="text" name="bed_type" class="form-control form-control-sm" value="{{ $r->bed_type }}">
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-2">
                                                    <div class="col-4">
                                                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Max Adults</label>
                                                        <input type="number" name="max_adults" class="form-control form-control-sm" value="{{ $r->max_adults ?? 2 }}" required>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Max Children</label>
                                                        <input type="number" name="max_children" class="form-control form-control-sm" value="{{ $r->max_children ?? 1 }}">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Total Units</label>
                                                        <input type="number" name="total_rooms" class="form-control form-control-sm" value="{{ $r->total_rooms ?? 10 }}">
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-3 mb-2 pt-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="breakfast_included" value="1" id="editBf{{ $r->id }}" {{ $r->breakfast_included ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="editBf{{ $r->id }}" style="font-size:12px;">Free Breakfast</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="editFc{{ $r->id }}" {{ $r->free_cancellation ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="editFc{{ $r->id }}" style="font-size:12px;">Free Cancellation</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2 px-3">
                                                <button type="button" class="btn btn-light btn-sm text-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Update Room</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No rooms registered for this property. Click "Add Room Category" above to register room types.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ADD ROOM MODAL --}}
<div class="modal fade text-start" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:8px;">
            <form action="{{ route('vendor.rooms.store', $property->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-2.5 px-3">
                    <h6 class="modal-title fw-bold text-primary"><i class="fa-solid fa-plus me-1"></i> Add Room Category to {{ $property->name }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Room Category Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Deluxe Ocean View Suite" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Price / Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" step="0.01" name="price_per_night" class="form-control form-control-sm" placeholder="e.g. 7500" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Bed Configuration <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="bed_type" class="form-control form-control-sm" placeholder="e.g. 1 King Bed or 2 Twin Beds" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Max Adults <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" name="max_adults" class="form-control form-control-sm" value="2" required min="1">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Max Children</label>
                            <input type="number" name="max_children" class="form-control form-control-sm" value="1" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Total Units</label>
                            <input type="number" name="total_rooms" class="form-control form-control-sm" value="10" min="1">
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-2 pt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="breakfast_included" value="1" id="addBf">
                            <label class="form-check-label" for="addBf" style="font-size:12px;">Free Breakfast</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="addFc">
                            <label class="form-check-label" for="addFc" style="font-size:12px;">Free Cancellation</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-light btn-sm text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-save me-1"></i> Save Room Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
