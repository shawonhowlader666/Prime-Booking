@extends('layouts.app')

@section('title', 'Employee Appraisal & Performance')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">HRMS</a> / <span>Staff Appraisal & Performance Review</span>
        </div>
        <button class="btn btn-sm text-white px-3" style="background-color: var(--ha-primary);">
            <i class="fa-solid fa-star me-1"></i> New Appraisal Review
        </button>
    </div>

    <!-- Appraisal Cards Grid -->
    <div class="row g-3">
        <!-- Review 1 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="https://ui-avatars.com/api/?name=Sadia+Islam&background=ff5e7e&color=fff&rounded=true" width="48" height="48" class="rounded-circle border">
                    <div>
                        <h6 class="fw-bold mb-0">Sadia Islam</h6>
                        <small class="text-muted">Front Desk Receptionist</small>
                    </div>
                    <span class="badge bg-success ms-auto">4.8 / 5.0</span>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Guest Satisfaction:</span>
                        <span class="fw-bold text-dark">96%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 96%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Check-In Punctuality:</span>
                        <span class="fw-bold text-dark">92%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 92%"></div>
                    </div>
                </div>
                <p class="text-muted small mb-0 pt-2 border-top">"Exceptional communication skills, courteous behavior with guests, and zero billing errors during rush hours."</p>
            </div>
        </div>

        <!-- Review 2 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="https://ui-avatars.com/api/?name=Karim+Mia&background=2ec4b6&color=fff&rounded=true" width="48" height="48" class="rounded-circle border">
                    <div>
                        <h6 class="fw-bold mb-0">Karim Mia</h6>
                        <small class="text-muted">Housekeeping Staff</small>
                    </div>
                    <span class="badge bg-primary ms-auto">4.6 / 5.0</span>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Room Turnaround Speed:</span>
                        <span class="fw-bold text-dark">94%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 94%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Sanitization Quality:</span>
                        <span class="fw-bold text-dark">90%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 90%"></div>
                    </div>
                </div>
                <p class="text-muted small mb-0 pt-2 border-top">"Quick response to dirty room triggers upon check-outs, reliable sanitization, and positive teamwork attitude."</p>
            </div>
        </div>

        <!-- Review 3 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="https://ui-avatars.com/api/?name=Mahmud+Hasan&background=6366f1&color=fff&rounded=true" width="48" height="48" class="rounded-circle border">
                    <div>
                        <h6 class="fw-bold mb-0">Mahmud Hasan</h6>
                        <small class="text-muted">Operations Manager</small>
                    </div>
                    <span class="badge bg-warning text-dark ms-auto">4.9 / 5.0</span>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Staff Leadership:</span>
                        <span class="fw-bold text-dark">98%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: 98%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Night Audit Accuracy:</span>
                        <span class="fw-bold text-dark">100%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
                <p class="text-muted small mb-0 pt-2 border-top">"Superb leadership, high occupancy rates maintained, and smooth coordination with Prime Booking OTA sync."</p>
            </div>
        </div>
    </div>
</div>
@endsection
