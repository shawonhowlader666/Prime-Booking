@extends('layouts.vendor')
@section('title', 'Manage Rooms — ' . $property->name)

@section('content')

{{-- PAGE HEADER & ACTION BUTTONS --}}
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        <div>
            <h1 class="page-title m-0 d-flex align-items-center">
                <i class="fa-solid fa-bed text-primary me-2"></i> Room Inventory: {{ $property->name }}
            </h1>
            <div class="page-breadcrumb mt-1.5">
                <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><a href="{{ route('vendor.properties.index') }}">My Properties</a>
                <span class="sep">-</span><strong style="color:#1e293b;">{{ $property->name }} Rooms</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Export Toolbar --}}
            <div class="btn-group btn-group-sm" role="group" style="height:32px;">
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportTableData('copy')" title="Copy to Clipboard" style="font-size:11.5px;">
                    <i class="fa-solid fa-copy me-1"></i> Copy
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportTableData('excel')" title="Export to Excel" style="font-size:11.5px;">
                    <i class="fa-solid fa-file-excel me-1 text-success"></i> Excel
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportTableData('csv')" title="Export to CSV" style="font-size:11.5px;">
                    <i class="fa-solid fa-file-csv me-1 text-primary"></i> CSV
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="exportTableData('pdf')" title="Export to PDF" style="font-size:11.5px;">
                    <i class="fa-solid fa-file-pdf me-1 text-danger"></i> PDF
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold px-2.5" onclick="window.print()" title="Print Page" style="font-size:11.5px;">
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
            </div>

            <a href="{{ route('vendor.availability.index') }}" class="btn btn-outline-primary btn-sm fw-bold px-3 d-inline-flex align-items-center" style="height:32px; font-size:12px; border-radius:4px;">
                <i class="fa-solid fa-calendar-days me-1.5"></i> Rates Calendar
            </a>

            <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal" style="height:32px; font-size:12px; padding:0 14px; border-radius:4px; display:inline-flex; align-items:center;">
                <i class="fa-solid fa-plus me-1.5"></i> Add Room Category
            </button>
        </div>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3" style="border-radius:6px;">
            <i class="fa-solid fa-circle-check me-1.5"></i> {{ session('success') }}
        </div>
    @endif

    {{-- TOP 4 KPI SUMMARY CARDS (2x2 on Mobile, 4 in Row on Desktop) --}}
    <div class="row g-2.5 g-sm-3" style="margin-bottom: 20px !important;">
        {{-- Card 1: Property Info --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">PROPERTY INFO</p>
                        <p class="kpi-value" style="font-size:15px; font-weight:800; color:#1e293b; margin:0;">{{ Str::limit($property->name, 18) }}</p>
                        <span style="font-size:11.5px; color:#64748b;">📍 {{ $property->city ?? 'Location' }} • {{ $property->star_rating }}★</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>

        {{-- Card 2: Total Categories --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ROOM TYPES</p>
                        <p class="kpi-value" style="font-size:16px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['total_categories'] }} Types</p>
                        <span style="font-size:11px; color:#52c41a; font-weight:600;">Active</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        {{-- Card 3: Total Units --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">TOTAL CAPACITY</p>
                        <p class="kpi-value" style="font-size:16px; font-weight:800; color:#7367f0; margin:0;">{{ $stats['total_units'] }} Units</p>
                        <span style="font-size:11px; color:#7367f0; font-weight:600;">Available</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f0eefc; color:#7367f0; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>

        {{-- Card 4: Base Rates --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">BASE RATES</p>
                        <p class="kpi-value" style="font-size:16px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($stats['avg_price']) }}</p>
                        <span style="font-size:11px; color:#64748b;">৳{{ number_format($stats['min_price']) }}–৳{{ number_format($stats['max_price']) }}</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- INTEGRATED TOOLBAR & VIEW TOGGLE --}}
    <div class="data-table-card" style="border-radius: 6px !important; background:#ffffff; border: 1px solid #e8e8e8 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.03); margin-bottom: 20px !important; padding: 14px 18px;">
        <div class="row g-3 align-items-end">
            {{-- Search Input --}}
            <div class="col-12 col-md-6 col-lg-6">
                <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                    <i class="fa-solid fa-magnifying-glass text-primary me-1"></i> Search Room Categories
                </label>
                <input type="text" id="roomSearchInput" class="form-control form-control-sm" placeholder="Filter by room name, bed type, amenities..." onkeyup="filterRoomsSearch(this.value)" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
            </div>

            {{-- Bed Type Filter --}}
            <div class="col-12 col-md-3 col-lg-3">
                <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                    <i class="fa-solid fa-filter text-primary me-1"></i> Bed Filter
                </label>
                <select id="bedTypeFilter" class="form-select form-select-sm" onchange="filterBedType(this.value)" style="height:36px; font-size:13px; font-weight:600; color:#1e293b; border:1px solid #d9d9d9; border-radius:4px;">
                    <option value="">All Bed Types</option>
                    <option value="King">King Bed</option>
                    <option value="Queen">Queen Bed</option>
                    <option value="Double">Double Bed</option>
                    <option value="Twin">Twin Bed</option>
                    <option value="Single">Single Bed</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>

            {{-- View Mode Switcher (Grid / Table) --}}
            <div class="col-12 col-md-3 col-lg-3">
                <label class="form-label mb-1.5 d-none d-md-block" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                    <i class="fa-solid fa-eye text-primary me-1"></i> View Layout
                </label>
                <div class="btn-group btn-group-sm w-100" role="group" style="height:36px;">
                    <button type="button" class="btn btn-outline-secondary active fw-bold w-50" id="btnViewTable" onclick="toggleRoomView('table')" style="font-size:12px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:4px 0 0 4px;">
                        <i class="fa-solid fa-list me-1"></i> Table
                    </button>
                    <button type="button" class="btn btn-outline-secondary fw-bold w-50" id="btnViewGrid" onclick="toggleRoomView('grid')" style="font-size:12px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:0 4px 4px 0;">
                        <i class="fa-solid fa-table-cells-large me-1"></i> Grid
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- VIEW 1: DATA TABLE VIEW (Stockifly Admin Standard) --}}
    <div id="roomsTableView" class="data-table-card p-0 mb-4" style="border-radius:6px; background:#ffffff; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div class="table-responsive">
            <table class="table table-stockifly align-middle mb-0" id="roomsTable">
                <thead>
                    <tr>
                        <th style="padding-left: 20px !important;">Room Category</th>
                        <th>Bed &amp; Guests</th>
                        <th>Base Rate / Night</th>
                        <th>Total Units</th>
                        <th>Key Inclusions</th>
                        <th style="text-align:right; padding-right: 20px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rooms as $r)
                    <tr class="room-row-item" data-name="{{ strtolower($r->name) }}" data-bed="{{ strtolower($r->bed_type ?? '') }}">
                        <td style="padding-left: 20px !important;">
                            <strong style="font-size:13.5px; color:#1e293b; display:block;">{{ $r->name }}</strong>
                            <span style="font-size:11px; color:#64748b;">ID #{{ $r->id }} • Max {{ $r->max_guests ?? ($r->max_adults + ($r->max_children ?? 0)) }} Guests</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="badge bg-light text-dark border" style="font-size:11.5px; font-weight:600;">
                                    <i class="fa-solid fa-bed text-primary me-1"></i> {{ $r->bed_type ?: 'Standard Bed' }}
                                </span>
                                <span class="badge bg-light text-secondary border" style="font-size:11px;">
                                    <i class="fa-solid fa-user me-1"></i> {{ $r->max_adults }}A @if($r->max_children) + {{ $r->max_children }}C @endif
                                </span>
                            </div>
                        </td>
                        <td>
                            <strong style="color:#2067e1; font-size:14px;">৳ {{ number_format($r->price_per_night) }}</strong>
                            <span style="font-size:10.5px; color:#64748b; display:block;">Standard Base</span>
                        </td>
                        <td>
                            <span class="badge bg-success-light text-success fw-bold px-2 py-1" style="font-size:11px; background:#f6ffed; border:1px solid #b7eb8f; border-radius:4px;">
                                <i class="fa-solid fa-door-open me-1"></i> {{ $r->total_rooms ?? 10 }} Units
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                @if($r->breakfast_included)
                                    <span class="badge bg-success text-white" style="font-size:10px; border-radius:3px;">
                                        <i class="fa-solid fa-utensils me-0.5"></i> Breakfast
                                    </span>
                                @endif
                                @if($r->free_cancellation)
                                    <span class="badge bg-info text-white" style="font-size:10px; border-radius:3px;">
                                        <i class="fa-solid fa-rotate-left me-0.5"></i> Free Cancel
                                    </span>
                                @endif
                                @if(!$r->breakfast_included && !$r->free_cancellation)
                                    <span class="text-muted" style="font-size:11px;">Standard Room Only</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right; padding-right: 20px !important;">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                {{-- Quick Calendar Rate Shortcut --}}
                                <a href="{{ route('vendor.availability.index', ['room_id' => $r->id]) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2.5 py-1" title="Manage Calendar Rates" style="font-size:11px; height:28px; border-radius:4px; display:inline-flex; align-items:center;">
                                    <i class="fa-solid fa-calendar-days me-1"></i> Rates
                                </a>

                                {{-- Edit Modal Button --}}
                                <button type="button" class="btn btn-sm btn-light border px-2 py-1" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $r->id }}" title="Edit Room" style="font-size:11px; height:28px; border-radius:4px;">
                                    <i class="fa-solid fa-pen text-primary"></i>
                                </button>

                                {{-- Delete Form --}}
                                <form action="{{ route('vendor.rooms.destroy', [$property->id, $r->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete room category: {{ $r->name }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2 py-1" title="Delete Room" style="font-size:11px; height:28px; border-radius:4px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div style="font-size:32px; color:#cbd5e1; margin-bottom:8px;"><i class="fa-solid fa-bed"></i></div>
                            <h6 class="fw-bold text-dark mb-1">No Room Categories Found</h6>
                            <p class="mb-3" style="font-size:13px; color:#64748b;">Add your first room type for this property to start managing bookings.</p>
                            <button type="button" class="btn btn-primary btn-sm fw-bold px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#addRoomModal" style="background:#2067e1; border-radius:4px;">
                                <i class="fa-solid fa-plus me-1"></i> Add Room Category
                            </button>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- VIEW 2: CARD / GRID VIEW (Modern Responsive Cards) --}}
    <div id="roomsGridView" class="mb-4" style="display:none;">
        <div class="row g-3" id="roomsGridContainer">
            @forelse($rooms as $r)
                <div class="col-12 col-md-6 col-xl-4 room-grid-item" data-name="{{ strtolower($r->name) }}" data-bed="{{ strtolower($r->bed_type ?? '') }}">
                    <div class="card h-100 border shadow-sm" style="border-radius:6px; border-color:#e8e8e8 !important;">
                        <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0.5" style="font-size:14.5px;">{{ $r->name }}</h6>
                                        <span class="text-muted" style="font-size:11px;">ID #{{ $r->id }}</span>
                                    </div>
                                    <span class="badge bg-success-light text-success fw-bold px-2 py-1" style="font-size:11px; background:#f6ffed; border:1px solid #b7eb8f; border-radius:4px;">
                                        {{ $r->total_rooms ?? 10 }} Units
                                    </span>
                                </div>

                                <div class="p-2.5 rounded mb-3" style="background:#f8fafc; border:1px solid #edf2f7;">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <span style="font-size:11.5px; color:#64748b;"><i class="fa-solid fa-bed text-primary me-1"></i> Bed Type:</span>
                                        <strong style="font-size:12px; color:#1e293b;">{{ $r->bed_type ?: 'Standard Bed' }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <span style="font-size:11.5px; color:#64748b;"><i class="fa-solid fa-users text-primary me-1"></i> Occupancy:</span>
                                        <strong style="font-size:12px; color:#1e293b;">{{ $r->max_adults }} Adults @if($r->max_children), {{ $r->max_children }} Child @endif</strong>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span style="font-size:11.5px; color:#64748b;"><i class="fa-solid fa-tag text-primary me-1"></i> Base Rate:</span>
                                        <strong style="font-size:14px; color:#2067e1;">৳ {{ number_format($r->price_per_night) }}</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-1 flex-wrap mb-3">
                                    @if($r->breakfast_included)
                                        <span class="badge bg-success text-white" style="font-size:10px; border-radius:3px;">
                                            <i class="fa-solid fa-utensils me-0.5"></i> Breakfast
                                        </span>
                                    @endif
                                    @if($r->free_cancellation)
                                        <span class="badge bg-info text-white" style="font-size:10px; border-radius:3px;">
                                            <i class="fa-solid fa-rotate-left me-0.5"></i> Free Cancel
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2">
                                <a href="{{ route('vendor.availability.index', ['room_id' => $r->id]) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2.5 flex-grow-1" style="font-size:11.5px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-calendar-days me-1"></i> Manage Rates
                                </a>
                                <button type="button" class="btn btn-sm btn-light border px-2.5" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $r->id }}" title="Edit Room" style="font-size:11.5px; height:30px;">
                                    <i class="fa-solid fa-pen text-primary"></i>
                                </button>
                                <form action="{{ route('vendor.rooms.destroy', [$property->id, $r->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete room category: {{ $r->name }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2.5" title="Delete Room" style="font-size:11.5px; height:30px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <p>No room categories available.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ➕ ADD ROOM CATEGORY MODAL --}}
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('vendor.rooms.store', $property->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-2.5 px-4" style="background:#f8fafc; border-radius:8px 8px 0 0;">
                    <div>
                        <h6 class="modal-title fw-bold text-dark m-0 d-flex align-items-center" style="font-size:15px;">
                            <i class="fa-solid fa-plus-circle text-primary me-2"></i> Add New Room Category
                        </h6>
                        <span class="text-muted" style="font-size:11.5px; font-weight:500;">
                            Configure inventory specifications &amp; pricing for <strong class="text-dark">{{ Str::limit($property->name, 35) }}</strong>
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Room Name / Category Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Deluxe Sea View Suite" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Bed Type <span class="text-danger">*</span>
                            </label>
                            <select name="bed_type" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="King Bed">King Bed</option>
                                <option value="Queen Bed">Queen Bed</option>
                                <option value="Double Bed">Double Bed</option>
                                <option value="Twin Beds">Twin Beds</option>
                                <option value="Single Bed">Single Bed</option>
                                <option value="Suite">Executive Suite</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Base Price / Night (৳ BDT) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="price_per_night" class="form-control form-control-sm" placeholder="e.g. 8500" required step="0.01" min="0" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Room Size (m²)
                            </label>
                            <input type="number" name="room_size_sqm" class="form-control form-control-sm" placeholder="e.g. 35" min="1" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Total Room Units <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="total_rooms" class="form-control form-control-sm" value="10" required min="1" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Max Adults <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_adults" class="form-control form-control-sm" value="2" required min="1" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Max Children
                            </label>
                            <input type="number" name="max_children" class="form-control form-control-sm" value="1" min="0" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Popular In-Room Amenities (Select All Applicable)
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-2.5 rounded border" style="background:#f8fafc;">
                                @foreach(['Air Conditioning', 'Free Wi-Fi', 'Smart Flat TV', 'Sea / City View', 'Private Balcony', 'Hot Water / Geyser', 'Tea & Coffee Maker', 'Mini Fridge', 'Work Desk', 'Safety Locker'] as $amenity)
                                    <label class="form-check-label d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded border bg-white" style="font-size:11.5px; font-weight:600; color:#334155; cursor:pointer;">
                                        <input class="form-check-input m-0" type="checkbox" name="amenities[]" value="{{ $amenity }}" style="cursor:pointer;">
                                        {{ $amenity }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded border" style="background:#fafafa;">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="breakfast_included" value="1" id="addBreakfastCheck" style="cursor:pointer;">
                                    <label class="form-check-label fw-bold text-dark" for="addBreakfastCheck" style="font-size:12.5px; cursor:pointer;">
                                        Free Breakfast Included
                                    </label>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="addCancelCheck" checked style="cursor:pointer;">
                                    <label class="form-check-label fw-bold text-dark" for="addCancelCheck" style="font-size:12.5px; cursor:pointer;">
                                        Free Cancellation Allowed
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Additional Amenities &amp; Notes (1 per line)
                            </label>
                            <textarea name="facilities_text" class="form-control" rows="3" placeholder="Extra Pillows&#10;Bathtub&#10;Bathrobes" style="font-size:12px; border:1px solid #d9d9d9; border-radius:4px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4" style="background:#f8fafc; border-radius:0 0 8px 8px;">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="height:34px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="background:#2067e1; height:34px; border-radius:4px;">
                        <i class="fa-solid fa-save me-1"></i> Save Room Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ✏️ EDIT ROOM MODALS --}}
@foreach($rooms as $r)
<div class="modal fade" id="editRoomModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('vendor.rooms.update', [$property->id, $r->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-2.5 px-4" style="background:#f8fafc; border-radius:8px 8px 0 0;">
                    <div>
                        <h6 class="modal-title fw-bold text-dark m-0 d-flex align-items-center" style="font-size:15px;">
                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Room Category
                        </h6>
                        <span class="text-muted" style="font-size:11.5px; font-weight:500;">
                            Update details &amp; base pricing for <strong class="text-dark">{{ Str::limit($r->name, 35) }}</strong>
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Room Name / Category Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $r->name }}" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Bed Type <span class="text-danger">*</span>
                            </label>
                            <select name="bed_type" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                @foreach(['King Bed', 'Queen Bed', 'Double Bed', 'Twin Beds', 'Single Bed', 'Suite'] as $bt)
                                    <option value="{{ $bt }}" {{ ($r->bed_type == $bt) ? 'selected' : '' }}>{{ $bt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Base Price / Night (৳ BDT) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="price_per_night" class="form-control form-control-sm" value="{{ (float)$r->price_per_night }}" required step="0.01" min="0" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Room Size (m²)
                            </label>
                            <input type="number" name="room_size_sqm" class="form-control form-control-sm" value="{{ $r->room_size_sqm }}" placeholder="e.g. 35" min="1" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Total Room Units <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="total_rooms" class="form-control form-control-sm" value="{{ $r->total_rooms ?? 10 }}" required min="1" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Max Adults <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_adults" class="form-control form-control-sm" value="{{ $r->max_adults ?? 2 }}" required min="1" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Max Children
                            </label>
                            <input type="number" name="max_children" class="form-control form-control-sm" value="{{ $r->max_children ?? 1 }}" min="0" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Popular In-Room Amenities
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-2.5 rounded border" style="background:#f8fafc;">
                                @php $currentFacs = is_array($r->facilities) ? $r->facilities : []; @endphp
                                @foreach(['Air Conditioning', 'Free Wi-Fi', 'Smart Flat TV', 'Sea / City View', 'Private Balcony', 'Hot Water / Geyser', 'Tea & Coffee Maker', 'Mini Fridge', 'Work Desk', 'Safety Locker'] as $amenity)
                                    <label class="form-check-label d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded border bg-white" style="font-size:11.5px; font-weight:600; color:#334155; cursor:pointer;">
                                        <input class="form-check-input m-0" type="checkbox" name="amenities[]" value="{{ $amenity }}" {{ in_array($amenity, $currentFacs) ? 'checked' : '' }} style="cursor:pointer;">
                                        {{ $amenity }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded border" style="background:#fafafa;">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="breakfast_included" value="1" id="editBreakfastCheck{{ $r->id }}" {{ $r->breakfast_included ? 'checked' : '' }} style="cursor:pointer;">
                                    <label class="form-check-label fw-bold text-dark" for="editBreakfastCheck{{ $r->id }}" style="font-size:12.5px; cursor:pointer;">
                                        Free Breakfast Included
                                    </label>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="editCancelCheck{{ $r->id }}" {{ $r->free_cancellation ? 'checked' : '' }} style="cursor:pointer;">
                                    <label class="form-check-label fw-bold text-dark" for="editCancelCheck{{ $r->id }}" style="font-size:12.5px; cursor:pointer;">
                                        Free Cancellation Allowed
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Additional Custom Amenities (1 per line)
                            </label>
                            @php
                                $presetList = ['Air Conditioning', 'Free Wi-Fi', 'Smart Flat TV', 'Sea / City View', 'Private Balcony', 'Hot Water / Geyser', 'Tea & Coffee Maker', 'Mini Fridge', 'Work Desk', 'Safety Locker'];
                                $customLines = array_diff($currentFacs, $presetList);
                            @endphp
                            <textarea name="facilities_text" class="form-control" rows="3" style="font-size:12px; border:1px solid #d9d9d9; border-radius:4px;">{{ implode("\n", $customLines) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4" style="background:#f8fafc; border-radius:0 0 8px 8px;">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="height:34px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="background:#2067e1; height:34px; border-radius:4px;">
                        <i class="fa-solid fa-save me-1"></i> Update Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<style>
/* 60 FPS GPU-Accelerated Animations */
.room-grid-item .card {
    transition: transform 0.12s ease, box-shadow 0.12s ease, border-color 0.12s ease;
    contain: content;
    transform: translateZ(0);
}
.room-grid-item .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08) !important;
    border-color: #2067e1 !important;
}
</style>

<script>
/**
 * Switch View: Table View vs Grid / Card View
 */
function toggleRoomView(mode) {
    const tableView = document.getElementById('roomsTableView');
    const gridView  = document.getElementById('roomsGridView');
    const btnTable  = document.getElementById('btnViewTable');
    const btnGrid   = document.getElementById('btnViewGrid');

    if (mode === 'grid') {
        if (tableView) tableView.style.display = 'none';
        if (gridView)  gridView.style.display  = 'block';
        if (btnGrid)   btnGrid.classList.add('active');
        if (btnTable)  btnTable.classList.remove('active');
        localStorage.setItem('vendor_room_view_mode', 'grid');
    } else {
        if (gridView)  gridView.style.display  = 'none';
        if (tableView) tableView.style.display = 'block';
        if (btnTable)  btnTable.classList.add('active');
        if (btnGrid)   btnGrid.classList.remove('active');
        localStorage.setItem('vendor_room_view_mode', 'table');
    }
}

// Restore saved view preference
document.addEventListener('DOMContentLoaded', function () {
    const savedMode = localStorage.getItem('vendor_room_view_mode') || 'table';
    toggleRoomView(savedMode);
});

/**
 * Filter Room Categories by Search Term
 */
function filterRoomsSearch(term) {
    const query = term.toLowerCase().trim();
    
    // Filter Table Rows
    document.querySelectorAll('.room-row-item').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });

    // Filter Grid Cards
    document.querySelectorAll('.room-grid-item').forEach(card => {
        const text = card.innerText.toLowerCase();
        card.style.display = text.includes(query) ? '' : 'none';
    });
}

/**
 * Filter by Bed Type Dropdown
 */
function filterBedType(bed) {
    const query = bed.toLowerCase().trim();

    // Table
    document.querySelectorAll('.room-row-item').forEach(row => {
        const bedData = row.getAttribute('data-bed') || '';
        row.style.display = (!query || bedData.includes(query)) ? '' : 'none';
    });

    // Grid
    document.querySelectorAll('.room-grid-item').forEach(card => {
        const bedData = card.getAttribute('data-bed') || '';
        card.style.display = (!query || bedData.includes(query)) ? '' : 'none';
    });
}

/**
 * Enterprise Table Export Tools (Copy, Excel, CSV, PDF)
 */
function exportTableData(type) {
    const table = document.getElementById('roomsTable');
    if (!table) return;

    let rows = [];
    const trs = table.querySelectorAll('tr');
    trs.forEach(tr => {
        let rowData = [];
        const thsOrTds = tr.querySelectorAll('th, td');
        // Exclude the last action column
        for (let i = 0; i < thsOrTds.length - 1; i++) {
            rowData.push(thsOrTds[i].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim());
        }
        if (rowData.length > 0) rows.push(rowData);
    });

    if (type === 'copy') {
        const text = rows.map(r => r.join('\t')).join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('✅ Room Inventory copied to clipboard!');
        });
    } else if (type === 'csv' || type === 'excel') {
        let csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => `"${cell.replace(/"/g, '""')}"`).join(",")).join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `room_inventory_${new Date().toISOString().slice(0,10)}.${type === 'excel' ? 'xls' : 'csv'}`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else if (type === 'pdf') {
        window.print();
    }
}
</script>
@endsection
