@extends('layouts.app')

@section('title', 'Housekeeping Task Board')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Housekeeping & Maintenance Operations</h4>
            <p class="text-muted mb-0">Track room cleaning tasks, linen replacements, and inspection approvals.</p>
        </div>
    </div>

    <!-- Active Tasks Table -->
    <div class="card mb-4">
        <div class="card-header bg-transparent py-3">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i>Active Cleaning Tasks</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Assigned Staff</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Task Notes</th>
                        <th class="text-end">Update Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $t)
                        <tr>
                            <td><span class="fw-bold fs-6 badge bg-light text-dark border">Room {{ $t->room->room_number }}</span></td>
                            <td>{{ $t->room->roomType->name ?? 'Standard' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-user-circle text-muted fs-5"></i>
                                    <span>{{ $t->assignedTo->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($t->priority === 'HIGH' || $t->priority === 'URGENT')
                                    <span class="badge bg-danger text-uppercase">{{ $t->priority }}</span>
                                @else
                                    <span class="badge bg-secondary text-uppercase">{{ $t->priority }}</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'COMPLETED')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($t->status === 'IN_PROGRESS')
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $t->notes ?? 'Standard Cleaning' }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-success" onclick="updateRoomStatus({{ $t->room->id }}, 'AVAILABLE')">
                                    <i class="fa-solid fa-check me-1"></i>Mark Clean & Ready
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">All rooms are clean and inspected!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                alert('Room marked Clean & Available!');
                window.location.reload();
            }
        } catch(e) {
            window.location.reload();
        }
    }
</script>
@endpush
