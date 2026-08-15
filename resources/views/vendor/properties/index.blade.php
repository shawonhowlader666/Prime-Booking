@extends('layouts.vendor')
@section('title', 'My Properties | Vendor Portal')
@section('content')
@php use App\Services\CurrencyService; @endphp

<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="page-title m-0">My Hotel Properties</h1>
        <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal" style="display:inline-flex;align-items:center;gap:7px;font-size:13px;padding:0 18px;height:36px;border:none;cursor:pointer;">
            <i class="fa-solid fa-plus"></i> Add New Property
        </button>
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

    @if($errors->any())
        <div class="admin-alert error mb-3">
            <i class="fa-solid fa-circle-xmark me-2"></i>
            <strong>Submission Error:</strong>
            <span class="ms-2">{{ implode(', ', $errors->all()) }}</span>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(24,144,255,0.1);color:#1890ff;"><i class="fa-solid fa-hotel"></i></div><div class="kpi-content"><div class="kpi-value">{{ $stats['total'] ?? count($properties ?? []) }}</div><div class="kpi-label">Total Listings</div></div><div class="kpi-accent-bar" style="background:#1890ff;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(40,199,111,0.1);color:#28c76f;"><i class="fa-solid fa-circle-check"></i></div><div class="kpi-content"><div class="kpi-value">{{ $stats['active'] ?? 0 }}</div><div class="kpi-label">Active Live</div></div><div class="kpi-accent-bar" style="background:#28c76f;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(255,159,67,0.1);color:#ff9f43;"><i class="fa-solid fa-clock"></i></div><div class="kpi-content"><div class="kpi-value">{{ $stats['pending'] ?? 0 }}</div><div class="kpi-label">Pending Review</div></div><div class="kpi-accent-bar" style="background:#ff9f43;"></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card"><div class="kpi-icon" style="background:rgba(234,84,85,0.1);color:#ea5455;"><i class="fa-solid fa-eye-slash"></i></div><div class="kpi-content"><div class="kpi-value">{{ $stats['inactive'] ?? 0 }}</div><div class="kpi-label">Inactive</div></div><div class="kpi-accent-bar" style="background:#ea5455;"></div></div>
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
                @forelse($properties as $property)
                    <tr>
                        <td style="font-size:12px;color:#64748b;">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $property->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=60&h=40&fit=crop' }}" width="50" height="35" style="object-fit:cover;border-radius:4px;flex-shrink:0;" onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=60&h=40&fit=crop'">
                                <div>
                                    <div class="fw-bold" style="font-size:12.5px;">{{ $property->name }}</div>
                                    <div style="font-size:11px;color:#64748b;">{{ str_repeat('★', $property->star_rating ?? 0) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12.5px;">{{ $property->city }}</td>
                        <td><span class="badge-gateway">{{ ucfirst($property->type) }}</span></td>
                        <td style="font-size:12.5px;font-weight:600;">৳{{ number_format($property->price_per_night) }}</td>
                        <td style="font-size:12.5px;text-align:center;">{{ $property->bookings_count ?? 0 }}</td>
                        <td>
                            @if(($property->status ?? 'active') === 'active')
                                <span class="badge-status active">Active</span>
                            @elseif(($property->status ?? '') === 'pending')
                                <span class="badge-status pending">Pending Review</span>
                            @elseif(($property->status ?? '') === 'rejected')
                                <span class="badge-status cancelled" style="background:#fff2f0; color:#ff4d4f; border:1px solid #ffccc7; cursor:pointer;" title="Feedback: {{ $property->rejection_reason ?? 'Requires update' }}">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge-status cancelled">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="action-gear-dropdown">
                                <button class="action-gear-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end py-1">
                                    <a href="{{ route('vendor.properties.edit', $property->id) }}" class="dropdown-item"><i class="fa-solid fa-pen me-2 text-primary"></i>Edit Property</a>
                                    <a href="{{ route('vendor.rooms.index', $property->id) }}" class="dropdown-item"><i class="fa-solid fa-bed me-2 text-info"></i>Manage Rooms</a>
                                    @if(($property->status ?? '') !== 'pending')
                                        <form action="{{ route('vendor.properties.toggle-status', $property->id) }}" method="POST" class="d-inline">
                                             @csrf
                                             <button type="submit" class="dropdown-item"><i class="fa-solid fa-toggle-on me-2 text-warning"></i>Toggle Status ({{ $property->status === 'active' ? 'Deactivate' : 'Activate' }})</button>
                                        </form>
                                    @else
                                        <span class="dropdown-item text-muted disabled" style="font-size:12px;"><i class="fa-solid fa-clock me-2 text-warning"></i>Awaiting Admin Review</span>
                                    @endif
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('vendor.properties.destroy', $property->id) }}" method="POST" onsubmit="return confirm('Delete this property permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5" style="color:#94a3b8;font-size:13px;">
                        <i class="fa-solid fa-hotel fa-2x mb-2 d-block"></i>No properties found. <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addPropertyModal" class="fw-bold text-primary">Add your first property now</a>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($properties, 'hasPages') && $properties->hasPages())
            <div class="stockifly-table-footer"><div>Showing {{ $properties->firstItem() }}–{{ $properties->lastItem() }} of {{ $properties->total() }}</div><div>{{ $properties->links() }}</div></div>
        @endif
    </div>
</div>

{{-- ====================================================================== --}}
{{-- INTEGRATED ADD PROPERTY FORM MODAL (Industry Best Practice)             --}}
{{-- ====================================================================== --}}
<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background:#fafafa; border-bottom:1px solid #f0f0f0; padding:16px 24px;">
                <div>
                    <h5 class="modal-title fw-bold text-dark m-0" id="addPropertyModalLabel" style="font-size:16px;">
                        <i class="fa-solid fa-hotel text-primary me-2"></i> List New Property / Hotel / Ship
                    </h5>
                    <small class="text-muted" style="font-size:11.5px;">Add property details. Listing will be reviewed by admin before publishing.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor.properties.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4" style="font-size:13px;">

                    {{-- Notice Banner --}}
                    <div class="p-2.5 mb-3 rounded border" style="background:#f0f7ff; border-color:#bae0ff !important; font-size:11.5px;">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i>
                        New listings start as <strong>Pending Admin Review</strong>. Default contract commission (15%) applies.
                    </div>

                    {{-- SECTION 1 --}}
                    <div class="fw-bold text-dark border-bottom pb-1.5 mb-3" style="font-size:13.5px;">
                        <i class="fa-solid fa-pen-to-square text-primary me-1.5"></i> 1. Property Details &amp; Destination
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Property Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Royal Tulip Sea Pearl Beach Resort" required style="font-size:12.5px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Property Category <span class="text-danger">*</span></label>
                            <select name="type" class="form-select form-select-sm" required style="font-size:12.5px;">
                                <option value="hotel">Hotel &amp; Resort</option>
                                <option value="resort">Beach Resort &amp; Spa</option>
                                <option value="houseboat">Ship &amp; Houseboat</option>
                                <option value="homestay">Eco Cottage &amp; Homestay</option>
                                <option value="apartment">Serviced Apartment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">City / Location <span class="text-danger">*</span></label>
                            <select name="city" class="form-select form-select-sm" required style="font-size:12.5px;">
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
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Star Rating <span class="text-danger">*</span></label>
                            <select name="star_rating" class="form-select form-select-sm" required style="font-size:12.5px;">
                                <option value="5">★★★★★ — 5 Star Luxury</option>
                                <option value="4">★★★★ — 4 Star Premium</option>
                                <option value="3">★★★ — 3 Star Standard</option>
                                <option value="2">★★ — 2 Star Budget</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Street Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control form-control-sm" placeholder="e.g. Marine Drive, Kalatoli, Cox's Bazar" required style="font-size:12.5px;">
                        </div>
                    </div>

                    {{-- SECTION 2 --}}
                    <div class="fw-bold text-dark border-bottom pb-1.5 mb-3 mt-4" style="font-size:13.5px;">
                        <i class="fa-solid fa-bangladeshi-taka-sign text-success me-1.5"></i> 2. Pricing &amp; MRP Rate
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Base Nightly Price (BDT ৳) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                                <input type="number" name="price_per_night" class="form-control" placeholder="e.g. 9500" required style="font-size:12.5px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Regular MRP Price (BDT ৳) — Optional</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                                <input type="number" name="original_price" class="form-control" placeholder="e.g. 13000 (Shows discount)" style="font-size:12.5px;">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3 --}}
                    <div class="fw-bold text-dark border-bottom pb-1.5 mb-3 mt-4" style="font-size:13.5px;">
                        <i class="fa-solid fa-camera text-purple me-1.5" style="color:#7367f0;"></i> 3. Photos &amp; Media
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Upload Thumbnail (Device)</label>
                            <input type="file" name="primary_image_file" class="form-control form-control-sm" accept="image/*" style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">OR Thumbnail Image URL</label>
                            <input type="url" name="primary_image" class="form-control form-control-sm" placeholder="https://images.unsplash.com/..." style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Upload Gallery Photos (Device)</label>
                            <input type="file" name="gallery_image_files[]" class="form-control form-control-sm" multiple accept="image/*" style="font-size:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">YouTube Video Tour URL</label>
                            <input type="url" name="video_url" class="form-control form-control-sm" placeholder="https://www.youtube.com/..." style="font-size:12px;">
                        </div>
                    </div>

                    {{-- SECTION 4 --}}
                    <div class="fw-bold text-dark border-bottom pb-1.5 mb-3 mt-4" style="font-size:13.5px;">
                        <i class="fa-solid fa-align-left text-primary me-1.5"></i> 4. Overview &amp; Description
                    </div>
                    <div class="mb-3">
                        <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Describe property amenities, location highlights, breakfast policies..." required style="font-size:12.5px;"></textarea>
                    </div>

                    {{-- SECTION 5 --}}
                    <div class="fw-bold text-dark border-bottom pb-1.5 mb-3 mt-4" style="font-size:13.5px;">
                        <i class="fa-solid fa-list-check text-info me-1.5"></i> 5. Amenities &amp; Services
                    </div>
                    <div class="row g-2">
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
                            ['elevator','fa-elevator','Elevator / Lift'],
                        ] as $am)
                        <div class="col-6 col-md-3">
                            <label class="p-2 border rounded d-flex align-items-center gap-2 bg-light w-100 mb-0" style="cursor:pointer; font-size:11.5px;">
                                <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" class="form-check-input mt-0">
                                <span><i class="fa-solid {{ $am[1] }} text-primary me-1"></i> {{ $am[2] }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>

                </div>
                <div class="modal-footer bg-light" style="border-top:1px solid #f0f0f0; padding:12px 24px;">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="background-color:#2067e1; border:none;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Property Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
