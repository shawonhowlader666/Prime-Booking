@extends('layouts.app')

@section('title', 'Employees Loan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">HRMS</a> / <span>Staff Loans & Advances</span>
        </div>
        <button class="btn btn-sm text-white px-3" style="background-color: var(--ha-primary);">
            <i class="fa-solid fa-plus me-1"></i> Disburse New Loan
        </button>
    </div>

    <!-- Loan KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Total Loan Disbursed</div>
                <div class="ha-kpi-value">৳2,50,000</div>
                <div>
                    <span class="ha-kpi-badge badge-purple">Total</span>
                    <span class="ha-kpi-subtitle">All active loans</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Recovered Amount</div>
                <div class="ha-kpi-value text-success">৳1,80,000</div>
                <div>
                    <span class="ha-kpi-badge badge-green">Recovered</span>
                    <span class="ha-kpi-subtitle">Via salary deductions</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Outstanding Balance</div>
                <div class="ha-kpi-value text-danger">৳70,000</div>
                <div>
                    <span class="ha-kpi-badge badge-orange">Due</span>
                    <span class="ha-kpi-subtitle">Remaining principal</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Monthly Recovery</div>
                <div class="ha-kpi-value">৳25,000</div>
                <div>
                    <span class="ha-kpi-badge badge-cyan">EMI</span>
                    <span class="ha-kpi-subtitle">Current month cycle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Records Table -->
    <div class="card ha-chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="ha-card-heading mb-0">Active Employee Loan Accounts</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Employee</th>
                        <th>Principal Amount</th>
                        <th>Monthly EMI</th>
                        <th>Tenure</th>
                        <th>Paid Amount</th>
                        <th>Remaining Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold font-monospace text-danger">LN-2026-001</td>
                        <td>
                            <div class="fw-bold">Sadia Islam</div>
                            <small class="text-muted">Front Desk Receptionist</small>
                        </td>
                        <td class="fw-bold">৳50,000</td>
                        <td>৳5,000 / mo</td>
                        <td>10 Months</td>
                        <td class="text-success fw-semibold">৳35,000 (7 EMIs)</td>
                        <td class="text-danger fw-bold">৳15,000</td>
                        <td><span class="badge bg-success">Active & Repaying</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold font-monospace text-danger">LN-2026-002</td>
                        <td>
                            <div class="fw-bold">Karim Mia</div>
                            <small class="text-muted">Housekeeper</small>
                        </td>
                        <td class="fw-bold">৳30,000</td>
                        <td>৳3,000 / mo</td>
                        <td>10 Months</td>
                        <td class="text-success fw-semibold">৳18,000 (6 EMIs)</td>
                        <td class="text-danger fw-bold">৳12,000</td>
                        <td><span class="badge bg-success">Active & Repaying</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
