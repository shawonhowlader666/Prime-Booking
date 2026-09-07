@extends('layouts.app')

@section('title', 'Rooms & Inventory Units')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Rooms & Inventory Units</h4>
            <p class="text-muted mb-0">Manage physical rooms, floors, and real-time operational statuses.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal"><i class="fa-solid fa-plus me-2"></i>Add New Room</button>
    </div>

    <!-- Status Filters -->
    <div class="card mb-4">
        <div class="card-body py-3 d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted small fw-bold me-2">Filter Status:</span>
            <a href="{{ route('rooms') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-light' }}">All Rooms</a>
            <a href="{{ route('rooms', ['status' => 'AVAILABLE']) }}" class="btn btn-sm {{ request('status') === 'AVAILABLE' ? 'btn-success' : 'btn-outline-success' }}">Available</a>
            <a href="{{ route('rooms', ['status' => 'OCCUPIED']) }}" class="btn btn-sm {{ request('status') === 'OCCUPIED' ? 'btn-primary' : 'btn-outline-primary' }}">Occupied</a>
            <a href="{{ route('rooms', ['status' => 'DIRTY']) }}" class="btn btn-sm {{ request('status') === 'DIRTY' ? 'btn-danger' : 'btn-outline-danger' }}">Dirty</a>
            <a href="{{ route('rooms', ['status' => 'CLEANING']) }}" class="btn btn-sm {{ request('status') === 'CLEANING' ? 'btn-warning' : 'btn-outline-warning' }}">Cleaning</a>
        </div>
    </div>

    <!-- Room Units Grid -->
    <div class="row g-3">
        @foreach($rooms as $r)
            @php
                $statusColor = match($r->status) {
                    'AVAILABLE' => 'success',
                    'OCCUPIED' => 'primary',
                    'DIRTY' => 'danger',
                    'CLEANING' => 'warning',
                    default => 'secondary'
                };
            @endphp
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100 position-relative overflow-hidden border-top border-4 border-{{ $statusColor }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h3 class="fw-bold mb-0">Room {{ $r->room_number }}</h3>
                                <small class="text-muted">Floor {{ $r->floor }}</small>
                            </div>
                            <span class="badge bg-{{ $statusColor }} text-uppercase">{{ $r->status }}</span>
                        </div>

                        <div class="p-2 bg-light rounded-3 my-3">
                            <div class="fw-semibold small text-primary">{{ $r->roomType->name ?? 'Standard' }}</div>
                            <div class="text-muted small">৳{{ number_format($r->roomType->base_price ?? 0) }} / night</div>
                        </div>

                        <!-- Quick Change Status Dropdown -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <small class="text-muted">Change Status:</small>
                            <select class="form-select form-select-sm w-auto" onchange="updateRoomStatus({{ $r->id }}, this.value)">
                                <option value="AVAILABLE" {{ $r->status === 'AVAILABLE' ? 'selected' : '' }}>Available</option>
                                <option value="OCCUPIED" {{ $r->status === 'OCCUPIED' ? 'selected' : '' }}>Occupied</option>
                                <option value="DIRTY" {{ $r->status === 'DIRTY' ? 'selected' : '' }}>Dirty</option>
                                <option value="CLEANING" {{ $r->status === 'CLEANING' ? 'selected' : '' }}>Cleaning</option>
                                <option value="MAINTENANCE" {{ $r->status === 'MAINTENANCE' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function updateRoomStatus(roomId, newStatus) {
        try {
            const res = await fetch(`/api/v1/rooms/${roomId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            }
        } catch(e) {
            window.location.reload();
        }
    }
</script>
@endpush
