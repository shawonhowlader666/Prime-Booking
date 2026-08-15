@extends('layouts.vendor')
@section('title', 'Manage Rooms — ' . $property->name)

@section('content')

{{-- PAGE HEADER & ACTION BUTTONS --}}
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        <div>
            <h1 class="page-title m-0 d-flex align-items-center">
                <i class="fa-solid fa-bed text-primary me-2"></i> Room Inventory
            </h1>
            <div class="page-breadcrumb mt-1.5">
                <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><a href="{{ route('vendor.properties.index') }}">Properties</a>
                <span class="sep">-</span><strong style="color:#1e293b;">{{ Str::limit($property->name, 26) }}</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Export Toolbar --}}
            <div class="btn-group btn-group-sm" role="group" style="height:32px;">
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

            <a href="{{ route('vendor.availability.index') }}" class="btn btn-outline-primary btn-sm fw-bold px-3 d-inline-flex align-items-center gap-2" style="height:34px; font-size:12px; border-radius:4px;">
                <i class="fa-solid fa-calendar-days"></i> Rates Calendar
            </a>

            <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addRoomModal" style="height:34px; font-size:12px; padding:0 16px; border-radius:4px;">
                <i class="fa-solid fa-plus"></i> Add Room Type
            </button>
        </div>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3" style="border-radius:6px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- MAIN CLEAN DATA CARD --}}
    <div class="data-table-card p-0 mb-4" style="border-radius:6px; background:#ffffff; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        {{-- Card Header with Property Context, Title, and Search --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2.5" style="background:#fafafa;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-white text-dark border px-3 py-1.5 d-inline-flex align-items-center gap-1.5" style="font-size:12px; font-weight:600;">
                    <i class="fa-solid fa-hotel text-primary"></i> {{ $property->name }}
                </span>
                <span class="badge bg-primary-light text-primary fw-bold px-2.5 py-1" style="font-size:11px; background:#e6f7ff; border:1px solid #91d5ff; border-radius:4px;">
                    {{ $rooms->count() }} Room Categories
                </span>
            </div>
            <div style="width:260px; max-width:100%;">
                <input type="text" id="roomSearchInput" class="form-control form-control-sm" placeholder="🔍 Quick search room name, bed..." onkeyup="filterRoomsSearch(this.value)" style="height:32px; font-size:12.5px; border:1px solid #d9d9d9; border-radius:4px;">
            </div>
        </div>

        {{-- Table View --}}
        <div class="table-responsive">
            <table class="table table-stockifly align-middle mb-0" id="roomsTable">
                <thead>
                    <tr>
                        <th style="padding-left: 20px !important;">Room Category</th>
                        <th>Bed &amp; Occupancy</th>
                        <th>Base Rate / Night</th>
                        <th>Inventory Units</th>
                        <th>Inclusions</th>
                        <th style="text-align:right; padding-right: 20px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rooms as $r)
                    <tr class="room-row-item" data-name="{{ strtolower($r->name) }}" data-bed="{{ strtolower($r->bed_type ?? '') }}">
                        <td style="padding-left: 20px !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:38px; height:38px; border-radius:6px; background:#f0f7ff; color:#2067e1; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; border:1px solid #d0e2ff;">
                                    <i class="fa-solid fa-bed"></i>
                                </div>
                                <div>
                                    <strong style="font-size:13.5px; color:#1e293b; display:block;">{{ $r->name }}</strong>
                                    <span style="font-size:11px; color:#64748b;">
                                        ID #{{ $r->id }} • {{ $r->room_size_sqm ? $r->room_size_sqm . ' m² • ' : '' }}{{ !empty($r->facilities) ? count($r->facilities) . ' amenities' : 'Standard setup' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size:11.5px; font-weight:600;">
                                    <i class="fa-solid fa-bed text-primary"></i> {{ $r->bed_type ?: 'Standard Bed' }}
                                </span>
                                <span class="badge bg-light text-secondary border px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size:11px;">
                                    <i class="fa-solid fa-user"></i> {{ $r->max_adults }} Adults @if($r->max_children) + {{ $r->max_children }} Child @endif
                                </span>
                            </div>
                        </td>
                        <td>
                            <strong style="color:#2067e1; font-size:14.5px;">৳ {{ number_format($r->price_per_night) }}</strong>
                            <small class="text-muted d-block" style="font-size:10.5px;">per night</small>
                        </td>
                        <td>
                            <span class="badge bg-success-light text-success fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size:11.5px; background:#f6ffed; border:1px solid #b7eb8f; border-radius:4px;">
                                <i class="fa-solid fa-door-open"></i> {{ $r->total_rooms ?? 10 }} Units Available
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                @if($r->breakfast_included)
                                    <span class="badge bg-success text-white px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size:11px; border-radius:4px; font-weight:600;">
                                        <i class="fa-solid fa-utensils"></i> Breakfast
                                    </span>
                                @endif
                                @if($r->free_cancellation)
                                    <span class="badge bg-info text-white px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size:11px; border-radius:4px; font-weight:600;">
                                        <i class="fa-solid fa-rotate-left"></i> Free Cancel
                                    </span>
                                @endif
                                @if(!$r->breakfast_included && !$r->free_cancellation)
                                    <span class="text-muted small" style="font-size:11.5px;">Room Only</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right; padding-right: 20px !important;">
                            <div class="d-inline-flex gap-2 align-items-center">
                                {{-- Quick Calendar Rate Shortcut --}}
                                <a href="{{ route('vendor.availability.index', ['room_id' => $r->id]) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2.5 py-1 d-inline-flex align-items-center gap-1.5" title="Manage Calendar Rates" style="font-size:11.5px; height:28px; border-radius:4px;">
                                    <i class="fa-solid fa-calendar-days"></i> Rates
                                </a>

                                {{-- Edit Modal Button --}}
                                <button type="button" class="btn btn-sm btn-light border px-2.5 py-1 d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $r->id }}" title="Edit Room Details" style="font-size:11.5px; height:28px; border-radius:4px;">
                                    <i class="fa-solid fa-pen text-primary"></i> Edit
                                </button>

                                {{-- Delete Form --}}
                                <form action="{{ route('vendor.rooms.destroy', [$property->id, $r->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete room category: {{ $r->name }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2 py-1" title="Delete Room" style="font-size:11.5px; height:28px; border-radius:4px;">
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
                            <h6 class="fw-bold text-dark mb-1">No Room Categories Configured</h6>
                            <p class="mb-3" style="font-size:13px; color:#64748b;">Add your first room type for this property to start managing inventory.</p>
                            <button type="button" class="btn btn-primary btn-sm fw-bold px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#addRoomModal" style="background:#2067e1; border-radius:4px;">
                                <i class="fa-solid fa-plus me-1"></i> Add Room Type
                            </button>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
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

@php
    $allBathroomFeatures = [
        ['name' => 'Private Bathroom', 'icon' => 'fa-solid fa-shower text-primary'],
        ['name' => 'Hot Water Geyser', 'icon' => 'fa-solid fa-fire text-danger'],
        ['name' => 'Bathtub / Jacuzzi', 'icon' => 'fa-solid fa-bath text-info'],
        ['name' => 'Hairdryer', 'icon' => 'fa-solid fa-wind text-secondary'],
        ['name' => 'Free Luxury Toiletries', 'icon' => 'fa-solid fa-pump-soap text-success'],
        ['name' => 'Bathrobe & Slippers', 'icon' => 'fa-solid fa-vest text-warning'],
        ['name' => 'Dental & Shaving Kit', 'icon' => 'fa-solid fa-tooth text-info'],
        ['name' => 'Vanity Mirror', 'icon' => 'fa-solid fa-circle-notch text-secondary'],
    ];

    $allRoomAmenities = [
        ['name' => 'Air Conditioning', 'icon' => 'fa-solid fa-snowflake text-info'],
        ['name' => 'Free Wi-Fi', 'icon' => 'fa-solid fa-wifi text-primary'],
        ['name' => 'Smart Flat TV', 'icon' => 'fa-solid fa-tv text-dark'],
        ['name' => 'Tea & Coffee Maker', 'icon' => 'fa-solid fa-mug-hot text-warning'],
        ['name' => 'Mini Fridge', 'icon' => 'fa-solid fa-box text-primary'],
        ['name' => 'Work Desk', 'icon' => 'fa-solid fa-laptop text-secondary'],
        ['name' => 'Safety Locker', 'icon' => 'fa-solid fa-vault text-warning'],
        ['name' => 'Electric Kettle', 'icon' => 'fa-solid fa-fire-burner text-danger'],
        ['name' => 'Ironing Facilities', 'icon' => 'fa-solid fa-shirt text-primary'],
        ['name' => 'Soundproofing', 'icon' => 'fa-solid fa-volume-xmark text-secondary'],
        ['name' => 'Blackout Curtains', 'icon' => 'fa-solid fa-moon text-dark'],
        ['name' => 'Daily Housekeeping', 'icon' => 'fa-solid fa-broom text-success'],
    ];
@endphp

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Bed Type <span class="text-danger">*</span>
                            </label>
                            <select name="bed_type" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="King Bed">🛏️ 1 King Size Bed</option>
                                <option value="Queen Bed">🛏️ 1 Queen Size Bed</option>
                                <option value="Twin Beds">🛏️🛏️ 2 Twin / Single Beds</option>
                                <option value="Triple Bed">🛏️🛏️🛏️ 3 Single Beds (Triple)</option>
                                <option value="Executive Suite">👑 Super King Bed + Jacuzzi</option>
                                <option value="Bunk Bed">🛌 Bunk Beds (Family Room)</option>
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

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Room View <span class="text-danger">*</span>
                            </label>
                            <select name="view_type" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="Sea View / Ocean Front">🌊 Direct Ocean / Sea View</option>
                                <option value="Beachfront View">🏖️ Beachfront &amp; Wave View</option>
                                <option value="City Skyline View">🏙️ City Skyline View</option>
                                <option value="Mountain / Hill View">⛰️ Scenic Mountain / Hill View</option>
                                <option value="Garden & Pool View">🌴 Lush Garden &amp; Pool View</option>
                                <option value="Lake / River View">⛵ Lake / River View</option>
                                <option value="Sunset View">🌅 Sunset Panoramic View</option>
                                <option value="Courtyard View">🌿 Quiet Inner Courtyard View</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Attached Bathrooms <span class="text-danger">*</span>
                            </label>
                            <select name="bathroom_count" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="1">🚿 1 Attached Bathroom</option>
                                <option value="2">🚿🚿 2 Bathrooms</option>
                                <option value="3">🚿🚿🚿 3 Bathrooms</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Smoking Policy <span class="text-danger">*</span>
                            </label>
                            <select name="smoking_policy" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="Non-Smoking">🚭 100% Non-Smoking</option>
                                <option value="Smoking Allowed">🚬 Smoking Allowed</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Balcony / Terrace
                            </label>
                            <select name="balcony_type" class="form-select form-select-sm" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="Private Balcony">🏞️ Private Balcony</option>
                                <option value="Terrace">🌅 Large Terrace</option>
                                <option value="French Balcony">🪟 French Balcony</option>
                                <option value="No Balcony">No Balcony</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-8">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Bathroom Features &amp; Toiletries
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-2.5 rounded-2 border" style="background:#f8fafc;">
                                @foreach($allBathroomFeatures as $bFeat)
                                    <label class="d-inline-flex align-items-center px-3 py-1.5 rounded-2 border bg-white shadow-xs" style="font-size:12px; font-weight:600; color:#334155; cursor:pointer; user-select:none;">
                                        <input class="form-check-input me-2 flex-shrink-0" type="checkbox" name="bathroom_features[]" value="{{ $bFeat['name'] }}" checked style="margin:0; width:15px; height:15px; cursor:pointer;">
                                        <i class="{{ $bFeat['icon'] }} me-1.5" style="width:14px;"></i>
                                        <span>{{ $bFeat['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Popular In-Room Amenities (Select All Applicable)
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-2.5 rounded-2 border" style="background:#f8fafc;">
                                @foreach($allRoomAmenities as $amenity)
                                    <label class="d-inline-flex align-items-center px-3 py-1.5 rounded-2 border bg-white shadow-xs" style="font-size:12px; font-weight:600; color:#334155; cursor:pointer; user-select:none;">
                                        <input class="form-check-input me-2 flex-shrink-0" type="checkbox" name="amenities[]" value="{{ $amenity['name'] }}" style="margin:0; width:15px; height:15px; cursor:pointer;">
                                        <i class="{{ $amenity['icon'] }} me-1.5" style="width:14px;"></i>
                                        <span>{{ $amenity['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-2 border h-100 d-flex align-items-center" style="background:#fafafa;">
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input me-2" type="checkbox" name="extra_bed_allowed" value="1" id="addExtraBedCheck" style="cursor:pointer; width:34px; height:18px; margin:0;">
                                    <label class="form-check-label fw-bold text-dark mb-0" for="addExtraBedCheck" style="font-size:12.5px; cursor:pointer;">
                                        Extra Bed / Rollaway Allowed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-2 border h-100 d-flex align-items-center" style="background:#fafafa;">
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input me-2" type="checkbox" name="breakfast_included" value="1" id="addBreakfastCheck" style="cursor:pointer; width:34px; height:18px; margin:0;">
                                    <label class="form-check-label fw-bold text-dark mb-0" for="addBreakfastCheck" style="font-size:12.5px; cursor:pointer;">
                                        Free Breakfast Included
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-2 border h-100 d-flex align-items-center" style="background:#fafafa;">
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input me-2" type="checkbox" name="free_cancellation" value="1" id="addCancelCheck" checked style="cursor:pointer; width:34px; height:18px; margin:0;">
                                    <label class="form-check-label fw-bold text-dark mb-0" for="addCancelCheck" style="font-size:12.5px; cursor:pointer;">
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
                                @foreach([
                                    'King Bed' => '🛏️ 1 King Size Bed',
                                    'Queen Bed' => '🛏️ 1 Queen Size Bed',
                                    'Twin Beds' => '🛏️🛏️ 2 Twin / Single Beds',
                                    'Triple Bed' => '🛏️🛏️🛏️ 3 Single Beds (Triple)',
                                    'Executive Suite' => '👑 Super King Bed + Jacuzzi',
                                    'Bunk Bed' => '🛌 Bunk Beds (Family Room)'
                                ] as $btVal => $btLbl)
                                    <option value="{{ $btVal }}" {{ ($r->bed_type == $btVal) ? 'selected' : '' }}>{{ $btLbl }}</option>
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

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Room View <span class="text-danger">*</span>
                            </label>
                            <select name="view_type" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                @foreach([
                                    'Sea View / Ocean Front' => '🌊 Direct Ocean / Sea View',
                                    'Beachfront View' => '🏖️ Beachfront & Wave View',
                                    'City Skyline View' => '🏙️ City Skyline View',
                                    'Mountain / Hill View' => '⛰️ Scenic Mountain / Hill View',
                                    'Garden & Pool View' => '🌴 Lush Garden & Pool View',
                                    'Lake / River View' => '⛵ Lake / River View',
                                    'Sunset View' => '🌅 Sunset Panoramic View',
                                    'Courtyard View' => '🌿 Quiet Inner Courtyard View'
                                ] as $vVal => $vLbl)
                                    <option value="{{ $vVal }}" {{ ($r->view_type == $vVal) ? 'selected' : '' }}>{{ $vLbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Attached Bathrooms <span class="text-danger">*</span>
                            </label>
                            <select name="bathroom_count" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="1" {{ ($r->bathroom_count == 1) ? 'selected' : '' }}>🚿 1 Attached Bathroom</option>
                                <option value="2" {{ ($r->bathroom_count == 2) ? 'selected' : '' }}>🚿🚿 2 Bathrooms</option>
                                <option value="3" {{ ($r->bathroom_count == 3) ? 'selected' : '' }}>🚿🚿🚿 3 Bathrooms</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Smoking Policy <span class="text-danger">*</span>
                            </label>
                            <select name="smoking_policy" class="form-select form-select-sm" required style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="Non-Smoking" {{ ($r->smoking_policy == 'Non-Smoking') ? 'selected' : '' }}>🚭 100% Non-Smoking</option>
                                <option value="Smoking Allowed" {{ ($r->smoking_policy == 'Smoking Allowed') ? 'selected' : '' }}>🚬 Smoking Allowed</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Balcony / Terrace
                            </label>
                            <select name="balcony_type" class="form-select form-select-sm" style="height:36px; font-size:13px; border:1px solid #d9d9d9; border-radius:4px;">
                                <option value="Private Balcony" {{ ($r->balcony_type == 'Private Balcony') ? 'selected' : '' }}>🏞️ Private Balcony</option>
                                <option value="Terrace" {{ ($r->balcony_type == 'Terrace') ? 'selected' : '' }}>🌅 Large Terrace</option>
                                <option value="French Balcony" {{ ($r->balcony_type == 'French Balcony') ? 'selected' : '' }}>🪟 French Balcony</option>
                                <option value="No Balcony" {{ ($r->balcony_type == 'No Balcony') ? 'selected' : '' }}>No Balcony</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-8">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Bathroom Features &amp; Toiletries
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-2.5 rounded-2 border" style="background:#f8fafc;">
                                @php $currentBFeats = is_array($r->bathroom_features) ? $r->bathroom_features : ['Private Bathroom', 'Hot Water Geyser']; @endphp
                                @foreach($allBathroomFeatures as $bFeat)
                                    <label class="d-inline-flex align-items-center px-3 py-1.5 rounded-2 border bg-white shadow-xs" style="font-size:12px; font-weight:600; color:#334155; cursor:pointer; user-select:none;">
                                        <input class="form-check-input me-2 flex-shrink-0" type="checkbox" name="bathroom_features[]" value="{{ $bFeat['name'] }}" {{ in_array($bFeat['name'], $currentBFeats) ? 'checked' : '' }} style="margin:0; width:15px; height:15px; cursor:pointer;">
                                        <i class="{{ $bFeat['icon'] }} me-1.5" style="width:14px;"></i>
                                        <span>{{ $bFeat['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size:11.5px; font-weight:700; color:#475569; text-transform:uppercase;">
                                Popular In-Room Amenities
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-2.5 rounded-2 border" style="background:#f8fafc;">
                                @php $currentFacs = is_array($r->facilities) ? $r->facilities : []; @endphp
                                @foreach($allRoomAmenities as $amenity)
                                    <label class="d-inline-flex align-items-center px-3 py-1.5 rounded-2 border bg-white shadow-xs" style="font-size:12px; font-weight:600; color:#334155; cursor:pointer; user-select:none;">
                                        <input class="form-check-input me-2 flex-shrink-0" type="checkbox" name="amenities[]" value="{{ $amenity['name'] }}" {{ in_array($amenity['name'], $currentFacs) ? 'checked' : '' }} style="margin:0; width:15px; height:15px; cursor:pointer;">
                                        <i class="{{ $amenity['icon'] }} me-1.5" style="width:14px;"></i>
                                        <span>{{ $amenity['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-2 border h-100 d-flex align-items-center" style="background:#fafafa;">
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input me-2" type="checkbox" name="extra_bed_allowed" value="1" id="editExtraBedCheck{{ $r->id }}" {{ $r->extra_bed_allowed ? 'checked' : '' }} style="cursor:pointer; width:34px; height:18px; margin:0;">
                                    <label class="form-check-label fw-bold text-dark mb-0" for="editExtraBedCheck{{ $r->id }}" style="font-size:12.5px; cursor:pointer;">
                                        Extra Bed / Rollaway Allowed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-2 border h-100 d-flex align-items-center" style="background:#fafafa;">
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input me-2" type="checkbox" name="breakfast_included" value="1" id="editBreakfastCheck{{ $r->id }}" {{ $r->breakfast_included ? 'checked' : '' }} style="cursor:pointer; width:34px; height:18px; margin:0;">
                                    <label class="form-check-label fw-bold text-dark mb-0" for="editBreakfastCheck{{ $r->id }}" style="font-size:12.5px; cursor:pointer;">
                                        Free Breakfast Included
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-2 border h-100 d-flex align-items-center" style="background:#fafafa;">
                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input me-2" type="checkbox" name="free_cancellation" value="1" id="editCancelCheck{{ $r->id }}" {{ $r->free_cancellation ? 'checked' : '' }} style="cursor:pointer; width:34px; height:18px; margin:0;">
                                    <label class="form-check-label fw-bold text-dark mb-0" for="editCancelCheck{{ $r->id }}" style="font-size:12.5px; cursor:pointer;">
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
