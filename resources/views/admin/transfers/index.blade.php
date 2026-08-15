@extends('layouts.admin')
@section('title', 'Airport Transfers & Taxi Fleet | Super Admin')

@section('content')
<div class="page-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="page-title m-0"><i class="fa-solid fa-taxi text-primary me-2"></i>Airport Transfers &amp; Fleet Management</h1>
            <p class="text-muted small mb-0 mt-1">Manage standard airport transfer rates, vehicle fleet, and guest pickup requests</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-xs px-3" data-bs-toggle="modal" data-bs-target="#addTransferModal">
            <i class="fa-solid fa-plus me-1"></i> Add New Route
        </button>
    </div>
</div>

<div class="page-content-area mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs mb-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Transfers Routes Table --}}
    <div class="card border-0 shadow-xs rounded-3 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-route text-primary me-2"></i>Active Transfer Routes</h6>
            <span class="badge bg-light text-dark border">{{ $transfers->total() }} Routes</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Pickup Airport</th>
                        <th>Drop-off Destination</th>
                        <th>Vehicle &amp; Class</th>
                        <th>Capacity</th>
                        <th>Standard Rate</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $tr)
                    <tr>
                        <td class="fw-bold text-dark"><i class="fa-solid fa-plane-arrival text-info me-1.5"></i>{{ $tr->pickup_location }}</td>
                        <td><i class="fa-solid fa-location-dot text-danger me-1.5"></i>{{ $tr->dropoff_location }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $tr->vehicle_type }}</span></td>
                        <td><i class="fa-solid fa-user-group text-secondary me-1"></i>{{ $tr->capacity }} pax ({{ $tr->luggage_capacity }} bags)</td>
                        <td><strong class="text-primary">BDT {{ number_format($tr->price, 2) }}</strong></td>
                        <td>
                            @if($tr->is_active)
                                <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.transfers.toggle', $tr->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Toggle Status">
                                    <i class="fa-solid fa-power-off"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.transfers.destroy', $tr->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this transfer route?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No transfer routes found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Route Modal --}}
<div class="modal fade" id="addTransferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('admin.transfers.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="font-size: 16px;">Add New Airport Transfer Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pickup Location (Airport)</label>
                        <input type="text" name="pickup_location" class="form-control form-control-sm" placeholder="e.g. Hazrat Shahjalal Int'l Airport (DAC)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Drop-off Destination</label>
                        <input type="text" name="dropoff_location" class="form-control form-control-sm" placeholder="e.g. Gulshan-2, Dhaka" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Vehicle Type</label>
                            <input type="text" name="vehicle_type" class="form-control form-control-sm" placeholder="e.g. Sedan (Toyota Premio)" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Rate (BDT)</label>
                            <input type="number" step="0.01" name="price" class="form-control form-control-sm" placeholder="2500" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Passenger Capacity</label>
                            <input type="number" name="capacity" class="form-control form-control-sm" value="4" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Luggage Capacity</label>
                            <input type="number" name="luggage_capacity" class="form-control form-control-sm" value="2" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">Save Route</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
