@extends('layouts.app')

@section('title', 'Leave Tracking')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">HRMS</a> / <span>Staff Leave Tracking & Approvals</span>
        </div>
        <button class="btn btn-sm text-white px-3" style="background-color: var(--ha-primary);" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
            <i class="fa-solid fa-plus me-1"></i> Apply For Leave
        </button>
    </div>

    <!-- 4 Leave Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Casual Leave</div>
                <div class="ha-kpi-value">14 Days</div>
                <div>
                    <span class="ha-kpi-badge badge-purple">Quota</span>
                    <span class="ha-kpi-subtitle">Per employee / year</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Sick Leave</div>
                <div class="ha-kpi-value">10 Days</div>
                <div>
                    <span class="ha-kpi-badge badge-cyan">Medical</span>
                    <span class="ha-kpi-subtitle">With doctor certificate</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Earned / Annual</div>
                <div class="ha-kpi-value">18 Days</div>
                <div>
                    <span class="ha-kpi-badge badge-green">Paid</span>
                    <span class="ha-kpi-subtitle">Carried over</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Pending Requests</div>
                <div class="ha-kpi-value text-danger">3 Pending</div>
                <div>
                    <span class="ha-kpi-badge badge-orange">Action</span>
                    <span class="ha-kpi-subtitle">Awaiting approval</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="card ha-chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="ha-card-heading mb-0">Staff Leave Applications</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Total Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th class="text-end">Manager Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=Sadia+Islam&background=4361ee&color=fff&rounded=true" width="32" height="32" class="rounded-circle">
                                <div>
                                    <div class="fw-bold">Sadia Islam</div>
                                    <small class="text-muted">Receptionist</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">Casual Leave</span></td>
                        <td>Sep 02, 2026</td>
                        <td>Sep 04, 2026</td>
                        <td><span class="fw-bold">3 Days</span></td>
                        <td><small class="text-muted">Family function in hometown.</small></td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-success me-1"><i class="fa-solid fa-check"></i> Approve</button>
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark"></i> Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=Karim+Mia&background=2ec4b6&color=fff&rounded=true" width="32" height="32" class="rounded-circle">
                                <div>
                                    <div class="fw-bold">Karim Mia</div>
                                    <small class="text-muted">Housekeeper</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">Sick Leave</span></td>
                        <td>Aug 28, 2026</td>
                        <td>Aug 29, 2026</td>
                        <td><span class="fw-bold">2 Days</span></td>
                        <td><small class="text-muted">Fever & doctor rest advice.</small></td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td class="text-end">
                            <span class="badge bg-light text-muted border">Completed</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
