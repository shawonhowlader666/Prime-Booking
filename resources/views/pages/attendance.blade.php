@extends('layouts.app')

@section('title', 'Attendance Tracking')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">HRMS</a> / <span>Daily Attendance & Shifts</span>
        </div>
        <button class="btn btn-sm text-white px-3" style="background-color: var(--ha-primary);">
            <i class="fa-solid fa-clock me-1"></i> Clock In / Out
        </button>
    </div>

    <div class="card ha-chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="ha-card-heading mb-0">Today's Attendance Roster ({{ date('M d, Y') }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Role / Department</th>
                        <th>Shift Time</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->name) }}&background=6366f1&color=fff&rounded=true" width="32" height="32" class="rounded-circle">
                                    <span class="fw-bold">{{ $emp->name }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $emp->role }}</span></td>
                            <td>08:00 AM - 04:00 PM</td>
                            <td class="text-success font-monospace fw-bold">07:55 AM</td>
                            <td class="text-muted font-monospace">--:--</td>
                            <td><span class="badge bg-success">On Duty</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
