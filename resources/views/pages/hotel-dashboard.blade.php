@extends('layouts.app')

@section('title', 'Hotel Overview & Operations')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-breadcrumb">
            <a href="#">Hotels</a> / <span>Operations Overview</span>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-ha-action" data-bs-toggle="modal" data-bs-target="#walkInModal" title="New Booking">
                <i class="fa-solid fa-plus"></i>
            </button>
            <button class="btn-ha-action" onclick="window.location.reload()">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- 4 Hotel KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Occupancy Rate</div>
                <div class="ha-kpi-value">{{ $kpis['occupancy_rate'] }}%</div>
                <div>
                    <span class="ha-kpi-badge badge-purple">{{ $kpis['occupied_rooms'] }}/{{ $kpis['total_rooms'] }}</span>
                    <span class="ha-kpi-subtitle">Rooms Occupied</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Available Rooms</div>
                <div class="ha-kpi-value">{{ $kpis['available_rooms'] }}</div>
                <div>
                    <span class="ha-kpi-badge badge-green">Ready</span>
                    <span class="ha-kpi-subtitle">Ready for check-in</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Today Check In / Out</div>
                <div class="ha-kpi-value">{{ $kpis['today_check_ins'] }} / {{ $kpis['today_check_outs'] }}</div>
                <div>
                    <span class="ha-kpi-badge badge-cyan">Flow</span>
                    <span class="ha-kpi-subtitle">Active front desk</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ha-kpi-card">
                <div class="ha-kpi-title">Month Revenue</div>
                <div class="ha-kpi-value">৳{{ number_format($kpis['month_revenue']) }}</div>
                <div>
                    <span class="ha-kpi-badge badge-orange">+18%</span>
                    <span class="ha-kpi-subtitle">Direct & OTA Sales</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Room Quick Status Grid -->
    <div class="card ha-chart-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="ha-card-heading mb-0"><i class="fa-solid fa-grid-2 me-2" style="color: var(--ha-primary);"></i>Live Room Units Status</h6>
            <a href="{{ route('rooms') }}" class="btn btn-sm btn-outline-danger" style="border-color: var(--ha-primary); color: var(--ha-primary);">Manage Units</a>
        </div>
        <div class="row g-2">
            @foreach($rooms as $room)
                @php
                    $badgeBg = match($room->status) {
                        'AVAILABLE' => 'rgba(46, 196, 182, 0.15)',
                        'OCCUPIED' => 'rgba(67, 97, 238, 0.15)',
                        'DIRTY' => 'rgba(230, 57, 70, 0.15)',
                        default => 'rgba(255, 183, 3, 0.15)'
                    };
                    $badgeText = match($room->status) {
                        'AVAILABLE' => '#0d9488',
                        'OCCUPIED' => '#3b82f6',
                        'DIRTY' => '#ef4444',
                        default => '#d97706'
                    };
                @endphp
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="p-3 rounded-3 text-center border" style="background-color: {{ $badgeBg }}; border-color: {{ $badgeText }} !important;">
                        <div class="fw-bold fs-5" style="color: {{ $badgeText }};">{{ $room->room_number }}</div>
                        <small class="d-block text-truncate text-muted" style="font-size: 0.72rem;">{{ $room->roomType->name ?? 'Standard' }}</small>
                        <span class="badge mt-1" style="background-color: {{ $badgeText }}; color: #fff; font-size: 0.65rem;">{{ $room->status }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
