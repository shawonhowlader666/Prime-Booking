@extends('layouts.vendor')
@section('title', 'My Properties | Vendor Portal')
@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        <div>
            <h1 class="page-title m-0 d-flex align-items-center">
                <i class="fa-solid fa-hotel text-primary me-2"></i> My Hotel Properties
            </h1>
            <div class="page-breadcrumb mt-1.5">
                <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><strong style="color:#1e293b;">My Properties</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal" style="display:inline-flex; align-items:center; gap:7px; font-size:12.5px; padding:0 16px; height:34px; border-radius:4px; border:none; cursor:pointer;">
                <i class="fa-solid fa-plus"></i> Add New Property
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

    @if(isset($errors) && $errors->any())
        <div class="admin-alert error mb-3" style="border-radius:6px;">
            <i class="fa-solid fa-circle-xmark me-2"></i>
            <strong>Submission Error:</strong>
            <span class="ms-2">{{ implode(', ', $errors->all()) }}</span>
        </div>
    @endif

    {{-- KPI Cards (2x2 on Mobile, 4 in row on Desktop) --}}
    <div class="row g-2.5 g-sm-3 mb-3">
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">TOTAL LISTINGS</p>
                        <p class="kpi-value" style="font-size:18px; font-weight:800; color:#1890ff; margin:0;">{{ $stats['total'] ?? 0 }}</p>
                        <span style="font-size:11px; color:#64748b;">Properties listed</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE LIVE</p>
                        <p class="kpi-value" style="font-size:18px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['active'] ?? 0 }}</p>
                        <span style="font-size:11px; color:#52c41a; font-weight:600;">Accepting Bookings</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">PENDING REVIEW</p>
                        <p class="kpi-value" style="font-size:18px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['pending'] ?? 0 }}</p>
                        <span style="font-size:11px; color:#64748b;">Under Admin Check</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="kpi-card" style="border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">INACTIVE</p>
                        <p class="kpi-value" style="font-size:18px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['inactive'] ?? 0 }}</p>
                        <span style="font-size:11px; color:#64748b;">Paused Listings</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff2f0; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
    </div>

    {{-- Main Property Table Card with Sleek Right-Aligned Auto-Filter Toolbar --}}
    <div class="data-table-card p-0 mb-4" style="border-radius:6px; background:#ffffff; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        {{-- Card Header Toolbar --}}
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2.5" style="background:#fafafa;">
            {{-- Left Title & Dynamic Count Badge --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h6 class="m-0 fw-bold text-dark" style="font-size:14px;">All Listed Properties</h6>
                <span class="badge bg-primary text-white" id="propertiesCountBadge" style="font-size:11px; border-radius:4px;">{{ $properties->count() }} Properties</span>
            </div>

            {{-- Right Auto-Filter Controls (Category, Status, and Search Box) --}}
            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                {{-- Category Auto-Filter --}}
                <select id="propertyTypeFilter" class="form-select form-select-sm" onchange="autoFilterProperties()" style="height:32px; font-size:12px; width:145px; border:1px solid #d9d9d9; border-radius:4px; font-weight:500;">
                    <option value="">All Categories</option>
                    <option value="hotel">Hotel &amp; Resort</option>
                    <option value="resort">Beach Resort</option>
                    <option value="houseboat">Ship / Houseboat</option>
                    <option value="homestay">Eco Homestay</option>
                    <option value="apartment">Serviced Apartment</option>
                </select>

                {{-- Status Auto-Filter --}}
                <select id="propertyStatusFilter" class="form-select form-select-sm" onchange="autoFilterProperties()" style="height:32px; font-size:12px; width:130px; border:1px solid #d9d9d9; border-radius:4px; font-weight:500;">
                    <option value="">All Status</option>
                    <option value="active">Active Live</option>
                    <option value="pending">Pending Review</option>
                    <option value="inactive">Inactive</option>
                    <option value="rejected">Rejected</option>
                </select>

                {{-- Sleek Compact Search Box --}}
                <div style="width:220px; max-width:100%;">
                    <input type="text" id="propertySearchInput" class="form-control form-control-sm" placeholder="🔍 Search name, city..." onkeyup="autoFilterProperties()" style="height:32px; font-size:12.5px; border:1px solid #d9d9d9; border-radius:4px;">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly align-middle mb-0" id="propertiesTable">
                <thead>
                    <tr>
                        <th style="padding-left: 20px !important;">#</th>
                        <th>Property &amp; Details</th>
                        <th>City / Location</th>
                        <th>Category</th>
                        <th>Price / Night</th>
                        <th>Bookings</th>
                        <th>Status</th>
                        <th style="text-align:right; padding-right: 20px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($properties as $property)
                    <tr class="property-row-item" data-name="{{ strtolower($property->name) }}" data-city="{{ strtolower($property->city ?? '') }}" data-type="{{ strtolower($property->type ?? '') }}" data-status="{{ strtolower($property->status ?? 'active') }}">
                        <td style="padding-left: 20px !important; font-size:12px; color:#64748b; font-weight:600;">
                            {{ $loop->iteration + ($properties->currentPage() - 1) * $properties->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="{{ $property->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=120&h=80&fit=crop' }}" width="56" height="40" style="object-fit:cover; border-radius:4px; flex-shrink:0; border:1px solid #e2e8f0;" onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=120&h=80&fit=crop'">
                                <div>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $property->name }}</strong>
                                    <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                        <span class="text-warning" style="font-size:11px;">
                                            @for($i=0; $i<($property->star_rating ?? 5); $i++)★@endfor
                                        </span>
                                        <span class="badge bg-light text-dark border" style="font-size:10px;">
                                            <i class="fa-solid fa-bed text-primary me-0.5"></i> {{ $property->rooms_count ?? 0 }} Room Types
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12.5px; color:#334155;">
                            <i class="fa-solid fa-location-dot text-danger me-1" style="font-size:11px;"></i> {{ $property->city }}
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border fw-semibold" style="font-size:11px; text-transform:capitalize;">
                                {{ $property->type }}
                            </span>
                        </td>
                        <td>
                            <strong style="font-size:13.5px; color:#2067e1;">৳ {{ number_format($property->price_per_night) }}</strong>
                            <small class="text-muted d-block" style="font-size:10px;">Base MRP</small>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge bg-light text-secondary border fw-bold px-2 py-1" style="font-size:11px;">
                                <i class="fa-solid fa-ticket text-primary me-1"></i> {{ $property->bookings_count ?? 0 }}
                            </span>
                        </td>
                        <td>
                            @if(($property->status ?? 'active') === 'active')
                                <span class="badge bg-success-light text-success fw-bold px-2 py-1" style="font-size:11px; background:#f6ffed; border:1px solid #b7eb8f; border-radius:4px;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Active
                                </span>
                            @elseif(($property->status ?? '') === 'pending')
                                <span class="badge bg-warning-light text-warning fw-bold px-2 py-1" style="font-size:11px; background:#fffbe6; border:1px solid #ffe58f; color:#d48806 !important; border-radius:4px;">
                                    <i class="fa-solid fa-clock me-1"></i> Pending Review
                                </span>
                            @elseif(($property->status ?? '') === 'rejected')
                                <span class="badge bg-danger-light text-danger fw-bold px-2 py-1" style="font-size:11px; background:#fff2f0; border:1px solid #ffccc7; border-radius:4px;" title="Feedback: {{ $property->rejection_reason ?? 'Requires update' }}">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-secondary-light text-secondary fw-bold px-2 py-1" style="font-size:11px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px;">
                                    <i class="fa-solid fa-eye-slash me-1"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td style="text-align:right; padding-right: 20px !important;">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                {{-- Direct Room Inventory Shortcut --}}
                                <a href="{{ route('vendor.rooms.index', $property->id) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2.5 py-1" title="Manage Room Categories" style="font-size:11.5px; height:28px; border-radius:4px; display:inline-flex; align-items:center;">
                                    <i class="fa-solid fa-bed me-1"></i> Rooms
                                </a>

                                {{-- Direct Rate Calendar Shortcut --}}
                                <a href="{{ route('vendor.availability.index') }}" class="btn btn-sm btn-light border fw-semibold px-2.5 py-1 text-dark" title="Rates & Availability Calendar" style="font-size:11.5px; height:28px; border-radius:4px; display:inline-flex; align-items:center;">
                                    <i class="fa-solid fa-calendar-days text-success me-1"></i> Rates
                                </a>

                                {{-- Actions Dropdown --}}
                                <div class="action-gear-dropdown">
                                    <button class="btn btn-sm btn-light border px-2 py-1" style="height:28px; border-radius:4px;" title="More Actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end py-1 shadow-sm border" style="font-size:12.5px; min-width:180px;">
                                        <a href="{{ route('vendor.properties.edit', $property->id) }}" class="dropdown-item py-1.5">
                                            <i class="fa-solid fa-pen me-2 text-primary"></i> Edit Property
                                        </a>
                                        <a href="{{ route('hotels.show', $property->id) }}" target="_blank" class="dropdown-item py-1.5">
                                            <i class="fa-solid fa-arrow-up-right-from-square me-2 text-info"></i> View on Main Web
                                        </a>
                                        @if(($property->status ?? '') !== 'pending')
                                            <form action="{{ route('vendor.properties.toggle-status', $property->id) }}" method="POST" class="d-inline">
                                                 @csrf
                                                 <button type="submit" class="dropdown-item py-1.5">
                                                     <i class="fa-solid fa-toggle-on me-2 text-warning"></i> {{ $property->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                 </button>
                                            </form>
                                        @else
                                            <span class="dropdown-item text-muted disabled py-1.5" style="font-size:11.5px;">
                                                <i class="fa-solid fa-clock me-2 text-warning"></i> Awaiting Review
                                            </span>
                                        @endif
                                        <div class="dropdown-divider my-1"></div>
                                        <form action="{{ route('vendor.properties.destroy', $property->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this property permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger py-1.5">
                                                <i class="fa-solid fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div style="font-size:32px; color:#cbd5e1; margin-bottom:8px;"><i class="fa-solid fa-hotel"></i></div>
                            <h6 class="fw-bold text-dark mb-1">No Properties Found</h6>
                            <p class="mb-3" style="font-size:13px; color:#64748b;">Get started by listing your hotel, resort, or ship to receive guest bookings.</p>
                            <button type="button" class="btn btn-primary btn-sm fw-bold px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#addPropertyModal" style="background:#2067e1; border-radius:4px;">
                                <i class="fa-solid fa-plus me-1"></i> Add New Property
                            </button>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($properties, 'hasPages') && $properties->hasPages())
            <div class="stockifly-table-footer p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#fafafa;">
                <div style="font-size:12px; color:#64748b;">Showing {{ $properties->firstItem() }}–{{ $properties->lastItem() }} of {{ $properties->total() }} Properties</div>
                <div>{{ $properties->links() }}</div>
            </div>
        @endif
    </div>
</div>

{{-- ====================================================================== --}}
{{-- INTEGRATED ADD PROPERTY FORM MODAL (Clean Professional Design)        --}}
{{-- ====================================================================== --}}
<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.12);">
            <div class="modal-header" style="background:#fafafa; border-bottom:1px solid #f0f0f0; padding:16px 24px;">
                <div>
                    <h5 class="modal-title fw-bold text-dark m-0" id="addPropertyModalLabel" style="font-size:15.5px;">
                        <i class="fa-solid fa-hotel text-primary me-2"></i> Add New Property Listing
                    </h5>
                    <small class="text-muted" style="font-size:11.5px;">Enter your property details below to add to your inventory.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor.properties.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4" style="font-size:13px;">

                    {{-- SECTION 1: BASIC INFORMATION --}}
                    <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-3">
                        <span class="badge bg-primary-light text-primary fw-bold" style="background:#e6f7ff; font-size:11px; padding:4px 8px; border-radius:4px;">1</span>
                        <strong class="text-dark" style="font-size:13.5px;">Basic Information</strong>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Property Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Ocean Paradise Resort &amp; Spa" required style="font-size:12.5px; height:36px;">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark m-0" style="font-size:12px;">Category <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptAddCustomCategory('propCategorySelect')">
                                    + Add Custom Type
                                </button>
                            </div>
                            <select name="type" id="propCategorySelect" class="form-select form-select-sm" required style="font-size:12.5px; height:36px;">
                                <option value="hotel">Hotel &amp; Resort</option>
                                <option value="resort">Beach Resort</option>
                                <option value="houseboat">Ship / Houseboat</option>
                                <option value="homestay">Eco Homestay</option>
                                <option value="apartment">Serviced Apartment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark m-0" style="font-size:12px;">Destination City <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptAddCustomCity('propCitySelect')">
                                    + Add New City/Area
                                </button>
                            </div>
                            <select name="city" id="propCitySelect" class="form-select form-select-sm" required style="font-size:12.5px; height:36px;">
                                <option value="Cox's Bazar Sea Beach">Cox's Bazar Sea Beach</option>
                                <option value="Dhaka City">Dhaka City</option>
                                <option value="Sylhet & Sreemangal">Sylhet &amp; Sreemangal</option>
                                <option value="Sajek Valley & Rangamati">Sajek Valley &amp; Rangamati</option>
                                <option value="Sundarbans & Mongla">Sundarbans &amp; Mongla</option>
                                <option value="Kuakata Sunset Beach">Kuakata Sunset Beach</option>
                                <option value="Chittagong City">Chittagong City</option>
                                <option value="Bandarban Hill District">Bandarban Hill District</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Star Rating <span class="text-danger">*</span></label>
                            <select name="star_rating" class="form-select form-select-sm" required style="font-size:12.5px; height:36px;">
                                <option value="5">★★★★★ — 5 Star Luxury</option>
                                <option value="4">★★★★ — 4 Star Premium</option>
                                <option value="3">★★★ — 3 Star Standard</option>
                                <option value="2">★★ — 2 Star Budget</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control form-control-sm" placeholder="e.g. Marine Drive Road, Kolatoli" required style="font-size:12.5px; height:36px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Nearest Landmark</label>
                            <input type="text" name="nearest_landmark" class="form-control form-control-sm" placeholder="e.g. Near Kolatoli Beach Point" style="font-size:12.5px; height:36px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Real-Time GPS Coordinates (Lat, Long)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="latitude" class="form-control" placeholder="Lat: 21.4272" style="font-size:12px; height:36px;">
                                <input type="text" name="longitude" class="form-control" placeholder="Long: 91.9702" style="font-size:12px; height:36px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Google Maps Embed / Location Link</label>
                            <input type="url" name="map_embed_url" class="form-control form-control-sm" placeholder="https://maps.google.com/..." style="font-size:12.5px; height:36px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Check-in / Check-out Hours</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-secondary" style="font-size:11px;">In</span>
                                <input type="text" name="checkin_time" class="form-control" value="14:00" placeholder="14:00" style="font-size:12px; height:36px;">
                                <span class="input-group-text bg-light text-secondary" style="font-size:11px;">Out</span>
                                <input type="text" name="checkout_time" class="form-control" value="12:00" placeholder="12:00" style="font-size:12px; height:36px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Hotel Contact Phone &amp; Email</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="contact_phone" class="form-control" placeholder="+8801700000000" style="font-size:12px; height:36px;">
                                <input type="email" name="contact_email" class="form-control" placeholder="info@hotel.com" style="font-size:12px; height:36px;">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: PRICING & POLICIES --}}
                    <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-3 mt-4">
                        <span class="badge bg-success-light text-success fw-bold" style="background:#f6ffed; font-size:11px; padding:4px 8px; border-radius:4px;">2</span>
                        <strong class="text-dark" style="font-size:13.5px;">Pricing &amp; Booking Policies</strong>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Base Price / Night (BDT ৳) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                                <input type="number" name="price_per_night" class="form-control" placeholder="e.g. 8500" required style="font-size:12.5px; height:36px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Regular MRP Price (BDT ৳)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                                <input type="number" name="original_price" class="form-control" placeholder="e.g. 11000 (Optional discount MRP)" style="font-size:12.5px; height:36px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2.5 border rounded d-flex align-items-center justify-content-between bg-light">
                                <div>
                                    <strong class="d-block text-dark" style="font-size:12px;">Free Cancellation</strong>
                                    <small class="text-muted" style="font-size:10.5px;">Allows guest free cancellation before check-in</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" checked style="cursor:pointer; width:34px; height:18px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2.5 border rounded d-flex align-items-center justify-content-between bg-light">
                                <div>
                                    <strong class="d-block text-dark" style="font-size:12px;">Pay at Hotel</strong>
                                    <small class="text-muted" style="font-size:10.5px;">Allows guest payment upon arrival</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" style="cursor:pointer; width:34px; height:18px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: MEDIA & GALLERY --}}
                    <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-3 mt-4">
                        <span class="badge bg-purple-light text-primary fw-bold" style="background:#f0f5ff; font-size:11px; padding:4px 8px; border-radius:4px;">3</span>
                        <strong class="text-dark" style="font-size:13.5px;">Photos, Gallery &amp; Video Tour</strong>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Cover Photo (Device Upload)</label>
                            <input type="file" name="primary_image_file" class="form-control form-control-sm" accept="image/*" style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">OR Cover Photo URL</label>
                            <input type="url" name="primary_image" class="form-control form-control-sm" placeholder="https://images.unsplash.com/..." style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Gallery Photos (Multiple Files)</label>
                            <input type="file" name="gallery_image_files[]" class="form-control form-control-sm" multiple accept="image/*" style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">OR Gallery URLs (One per line)</label>
                            <textarea name="gallery_images" class="form-control form-control-sm" rows="1" placeholder="https://... (One per line)" style="font-size:11.5px;"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Upload Video Tour (MP4 / WebM)</label>
                            <input type="file" name="video_file" class="form-control form-control-sm" accept="video/*" style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">OR YouTube Video Tour URL</label>
                            <input type="url" name="video_url" class="form-control form-control-sm" placeholder="https://www.youtube.com/watch?v=..." style="font-size:12px;">
                        </div>
                    </div>

                    {{-- SECTION 4: AMENITIES & DESCRIPTION --}}
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3 mt-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning-light text-warning fw-bold" style="background:#fffbe6; color:#d48806 !important; font-size:11px; padding:4px 8px; border-radius:4px;">4</span>
                            <strong class="text-dark" style="font-size:13.5px;">Amenities &amp; Description</strong>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        @foreach([
                            ['wifi','fa-wifi','Free Wi-Fi'],
                            ['pool','fa-person-swimming','Swimming Pool'],
                            ['parking','fa-car','Free Parking'],
                            ['ac','fa-snowflake','Air Conditioning'],
                            ['restaurant','fa-utensils','Restaurant'],
                            ['breakfast','fa-mug-hot','Free Breakfast'],
                            ['gym','fa-dumbbell','Gym / Fitness'],
                            ['beachfront','fa-water','Beachfront View'],
                            ['transfer','fa-van-shuttle','Airport Shuttle'],
                            ['frontdesk','fa-clock','24/7 Front Desk'],
                            ['elevator','fa-elevator','Elevator / Lift'],
                            ['spa','fa-spa','Spa & Wellness'],
                        ] as $am)
                        <div class="col-6 col-md-3">
                            <label class="p-2 border rounded d-flex align-items-center gap-2 bg-light w-100 mb-0" style="cursor:pointer; font-size:11.5px;">
                                <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" class="form-check-input mt-0">
                                <span><i class="fa-solid {{ $am[1] }} text-primary me-1"></i> {{ $am[2] }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    {{-- Dynamic Custom Amenity Tag Builder --}}
                    <div class="p-2.5 border rounded bg-light mb-3">
                        <label class="form-label fw-semibold text-dark mb-1.5 d-flex align-items-center justify-content-between" style="font-size:11.5px;">
                            <span><i class="fa-solid fa-plus-circle text-primary me-1"></i> + Add Custom Hotel Facility / Amenity</span>
                            <small class="text-muted">Type any custom facility and click + Add</small>
                        </label>
                        <div class="input-group input-group-sm mb-2" style="max-width:380px;">
                            <input type="text" id="modalCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi..." style="font-size:12px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addModalCustomAmenity();}">
                            <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12px;" onclick="addModalCustomAmenity()">
                                + Add
                            </button>
                        </div>
                        <div id="modalCustomAmenitiesContainer" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size:12px;">Property Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Briefly describe property highlights, location benefits, and dining features..." required style="font-size:12.5px;"></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light" style="border-top:1px solid #f0f0f0; padding:12px 24px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal" style="font-size:12.5px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="background-color:#2067e1; border:none; font-size:12.5px; border-radius:4px;">
                        <i class="fa-solid fa-plus me-1"></i> Save &amp; Submit Property
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function promptAddCustomCategory(selectId) {
    const select = document.getElementById(selectId);
    const custom = prompt("Enter new Property Category (e.g. Glamping Tent, Floating Resort, Luxury Villa, Heritage Palace):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "✨ " + custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function promptAddCustomCity(selectId) {
    const select = document.getElementById(selectId);
    const custom = prompt("Enter new Destination City or Region (e.g. Saint Martin Island, Kaptai Lake, Jaflong, Bangkok, Dubai):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "📍 " + custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function addModalCustomAmenity() {
    const input = document.getElementById('modalCustomAmenityInput');
    const val = input.value.trim();
    if (!val) return;
    const container = document.getElementById('modalCustomAmenitiesContainer');
    const pill = document.createElement('span');
    pill.className = 'badge bg-white text-dark border d-inline-flex align-items-center gap-1.5 p-2 shadow-xs';
    pill.style.fontSize = '11.5px';
    pill.innerHTML = `<i class="fa-solid fa-circle-check text-success"></i> ${val} <input type="hidden" name="amenities[]" value="${val}"> <button type="button" class="btn-close ms-1" style="font-size:7px;" onclick="this.parentElement.remove()" title="Remove"></button>`;
    container.appendChild(pill);
    input.value = '';
}
</script>

@endsection

@section('scripts')
<script>
/**
 * Instant Automatic Filter for Properties Table
 * Triggers on every keystroke in Search or dropdown selection change (Category & Status)
 */
function autoFilterProperties() {
    const searchVal = (document.getElementById('propertySearchInput')?.value || '').toLowerCase().trim();
    const typeVal   = (document.getElementById('propertyTypeFilter')?.value || '').toLowerCase().trim();
    const statusVal = (document.getElementById('propertyStatusFilter')?.value || '').toLowerCase().trim();

    let visibleCount = 0;
    const rows = document.querySelectorAll('.property-row-item');

    rows.forEach(row => {
        const name   = (row.getAttribute('data-name') || '').toLowerCase();
        const city   = (row.getAttribute('data-city') || '').toLowerCase();
        const type   = (row.getAttribute('data-type') || '').toLowerCase();
        const status = (row.getAttribute('data-status') || '').toLowerCase();

        const matchSearch = !searchVal || name.includes(searchVal) || city.includes(searchVal) || type.includes(searchVal);
        const matchType   = !typeVal || type.includes(typeVal);
        const matchStatus = !statusVal || status === statusVal;

        if (matchSearch && matchType && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const badge = document.getElementById('propertiesCountBadge');
    if (badge) {
        badge.innerText = `${visibleCount} Properties`;
    }
}
</script>
@endsection
