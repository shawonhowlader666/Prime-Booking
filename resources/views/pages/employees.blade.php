@extends('layouts.app')

@section('title', 'All Employees')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">Employees</a> / <span>All Staff Directory</span>
        </div>
        <button class="btn btn-sm text-white px-3" style="background-color: var(--ha-primary);" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="fa-solid fa-plus me-1"></i> Add Employee
        </button>
    </div>

    <!-- Employee Cards Grid -->
    <div class="row g-3">
        @foreach($employees as $emp)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card ha-chart-card text-center h-100">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->name) }}&background=ff5e7e&color=fff&rounded=true" width="70" height="70" class="rounded-circle mx-auto mb-3 border">
                    <h6 class="fw-bold mb-1">{{ $emp->name }}</h6>
                    <small class="badge bg-light text-dark border mb-2">{{ $emp->role }}</small>
                    <div class="text-muted small mb-1"><i class="fa-solid fa-phone me-1"></i>{{ $emp->phone ?? '+880 1711-000000' }}</div>
                    <div class="text-muted small mb-3"><i class="fa-solid fa-envelope me-1"></i>{{ $emp->email }}</div>
                    <div class="d-flex justify-content-center gap-2 pt-2 border-top">
                        <button class="btn btn-sm btn-light border"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-sm btn-light border text-danger"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
