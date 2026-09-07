@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">HRMS</a> / <span>Hotel Operational Departments</span>
        </div>
        <button class="btn btn-sm text-white px-3" style="background-color: var(--ha-primary);">
            <i class="fa-solid fa-plus me-1"></i> Add Department
        </button>
    </div>

    <!-- Departments Grid -->
    <div class="row g-3">
        <!-- Dept 1 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(255, 94, 126, 0.15); color: #ff5e7e;">
                            <i class="fa-solid fa-bell-concierge fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Front Desk & Reception</h6>
                            <small class="text-muted">Head: Mahmud Hasan</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-3">Responsible for guest greetings, check-in, check-out, telephone bookings, and lobby inquiries.</p>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge bg-light text-dark border">6 Staff Members</span>
                    <button class="btn btn-sm btn-outline-secondary">Manage</button>
                </div>
            </div>
        </div>

        <!-- Dept 2 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(46, 196, 182, 0.15); color: #2ec4b6;">
                            <i class="fa-solid fa-broom fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Housekeeping & Linen</h6>
                            <small class="text-muted">Head: Karim Mia</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-3">Responsible for daily room sanitation, dirty room turnaround, deep cleaning, and linen supply management.</p>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge bg-light text-dark border">14 Staff Members</span>
                    <button class="btn btn-sm btn-outline-secondary">Manage</button>
                </div>
            </div>
        </div>

        <!-- Dept 3 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(255, 183, 3, 0.15); color: #ffb703;">
                            <i class="fa-solid fa-utensils fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Food & Beverage / Restaurant</h6>
                            <small class="text-muted">Head: Chef Alamgir</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-3">Manages the multi-cuisine dining hall, complimentary breakfast buffet, room service orders, and minibar restocks.</p>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge bg-light text-dark border">18 Staff Members</span>
                    <button class="btn btn-sm btn-outline-secondary">Manage</button>
                </div>
            </div>
        </div>

        <!-- Dept 4 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(67, 97, 238, 0.15); color: #4361ee;">
                            <i class="fa-solid fa-calculator fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Accounts & Finance</h6>
                            <small class="text-muted">Head: Tanvir Ahmed</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-3">Handles nightly audits, bank reconciliation, daily cashier drawers, tax/VAT reporting, and payroll salary disbursement.</p>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge bg-light text-dark border">4 Staff Members</span>
                    <button class="btn btn-sm btn-outline-secondary">Manage</button>
                </div>
            </div>
        </div>

        <!-- Dept 5 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card ha-chart-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(76, 201, 240, 0.15); color: #4cc9f0;">
                            <i class="fa-solid fa-shield-halved fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Security & Maintenance</h6>
                            <small class="text-muted">Head: Kamal Pasha</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-3">24/7 CCTV surveillance, fire safety compliance, elevator maintenance, AC repair, and resort premises security.</p>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge bg-light text-dark border">8 Staff Members</span>
                    <button class="btn btn-sm btn-outline-secondary">Manage</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
