@extends('layouts.vendor')
@section('title', 'My Properties | Vendor Portal')
@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="page-title m-0">My Hotel Properties</h1>
        <a href="{{ route('vendor.properties.create') }}" class="btn-add-primary" style="display:inline-flex;align-items:center;gap:7px;font-size:13px;padding:0 18px;height:36px;">
            <i class="fa-solid fa-plus"></i> Add New Property
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">My Properties</strong>
    </div>
</div>

<div class="page-content-area">
    @if(session('success'))
        <div class="admin-alert success mb-3"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(24,144,255,0.1);color:#1890ff;"><i class="fa-solid fa-hotel"></i></div><div class="kpi-content"><div class="kpi-value">{{ ['total'] }}</div><div class="kpi-label">Total Listings</div></div><div class="kpi-accent-bar" style="background:#1890ff;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(40,199,111,0.1);color:#28c76f;"><i class="fa-solid fa-circle-check"></i></div><div class="kpi-content"><div class="kpi-value">{{ ['active'] }}</div><div class="kpi-label">Active Live</div></div><div class="kpi-accent-bar" style="background:#28c76f;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(255,159,67,0.1);color:#ff9f43;"><i class="fa-solid fa-clock"></i></div><div class="kpi-content"><div class="kpi-value">{{ ['pending'] }}</div><div class="kpi-label">Pending Review</div></div><div class="kpi-accent-bar" style="background:#ff9f43;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(234,84,85,0.1);color:#ea5455;"><i class="fa-solid fa-eye-slash"></i></div><div class="kpi-content"><div class="kpi-value">{{ ['inactive'] }}</div><div class="kpi-label">Inactive</div></div><div class="kpi-accent-bar" style="background:#ea5455;"></div></div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="page-filters-bar mb-3">
        <form action="{{ route('vendor.properties.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-5"><input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name or city..." style="height:32px;font-size:12.5px;"></div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" style="height:32px;font-size:12.5px;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending Review</option>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-primary btn-sm w-100" style="height:32px;font-size:12.5px;"><i class="fa-solid fa-search me-1"></i>Filter</button></div>
                <div class="col-md-2"><a href="{{ route('vendor.properties.index') }}" class="btn btn-outline-secondary btn-sm w-100" style="height:32px;font-size:12.5px;">Reset</a></div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="stockifly-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="propertiesTable">
                <thead class="stockifly-table-head">
                    <tr>
                        <th>#</th>
                        <th>Property</th>
                        <th>City</th>
                        <th>Type</th>
                        <th>Price/Night</th>
                        <th>Bookings</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse( as )
                    <tr>
                        <td style="font-size:12px;color:#64748b;">{{ ->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ ->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=60&h=40&fit=crop' }}" width="50" height="35" style="object-fit:cover;border-radius:4px;flex-shrink:0;" onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=60&h=40&fit=crop'">
                                <div>
                                    <div class="fw-bold" style="font-size:12.5px;">{{ ->name }}</div>
                                    <div style="font-size:11px;color:#64748b;">{{ str_repeat('★', ->star_rating ?? 0) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12.5px;">{{ ->city }}</td>
                        <td><span class="badge-gateway">{{ ucfirst(->type) }}</span></td>
                        <td style="font-size:12.5px;font-weight:600;">৳{{ number_format(->price_per_night) }}</td>
                        <td style="font-size:12.5px;text-align:center;">{{ ->bookings_count }}</td>
                        <td>
                            @if(->status === 'active')
                                <span class="badge-status active">Active</span>
                            @elseif(->status === 'pending')
                                <span class="badge-status pending">Pending Review</span>
                            @else
                                <span class="badge-status cancelled">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="action-gear-dropdown">
                                <button class="action-gear-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end py-1">
                                    <a href="{{ route('vendor.properties.edit', ->id) }}" class="dropdown-item"><i class="fa-solid fa-pen me-2 text-primary"></i>Edit Property</a>
                                    <form action="{{ route('vendor.properties.toggle-status', ->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fa-solid fa-toggle-on me-2 text-warning"></i>Toggle Status</button>
                                    </form>
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('vendor.properties.destroy', ->id) }}" method="POST" onsubmit="return confirm('Delete this property permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5" style="color:#94a3b8;font-size:13px;">
                        <i class="fa-solid fa-hotel fa-2x mb-2 d-block"></i>No properties found. <a href="{{ route('vendor.properties.create') }}">Add your first property</a>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(->hasPages())
            <div class="stockifly-table-footer"><div>Showing {{ ->firstItem() }}–{{ ->lastItem() }} of {{ ->total() }}</div><div>{{ ->links() }}</div></div>
        @endif
    </div>
</div>
@endsection
