@extends('layouts.admin')
@section('title', 'Property Inventory & Hotel Listings — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Property Inventory &amp; Hotel Listings</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('inventoryTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('inventoryTable', 'properties')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('inventoryTable', 'properties')" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('inventoryTable', 'properties')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('inventoryTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
            <a href="{{ route('admin.properties.create') }}" class="btn-add-primary ms-1"><i class="fa-solid fa-plus me-1"></i> Add New Listing</a></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Inventory</span>
        <span class="sep">-</span><strong style="color:#333;">Properties &amp; Listings</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI SUMMARY ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL PROPERTIES</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] ?? count($properties) }} Listings</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE &amp; PUBLISHED</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['active'] ?? 0 }} Active</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('admin.properties.index', ['status' => 'pending']) }}" style="text-decoration:none;">
                <div class="kpi-card" style="border: {{ ($stats['pending'] ?? 0) > 0 ? '1.5px solid #ff9f43' : 'none' }};">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                        <div>
                            <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">PENDING ADMIN REVIEW</p>
                            <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['pending'] ?? 0 }} Pending</p>
                        </div>
                        <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">FEATURED LISTINGS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $stats['featured'] ?? 0 }} Featured</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f0eefc; color:#7367f0; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- STOCKIFLY FILTER BAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <form method="GET" action="{{ route('admin.properties.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="city" class="form-select form-select-sm" style="font-size: 13px;">
                    <option value="">All Cities / Regions</option>
                    <option value="Cox's Bazar" {{ request('city') == "Cox's Bazar" ? 'selected' : '' }}>Cox's Bazar</option>
                    <option value="Dhaka" {{ request('city') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                    <option value="Sylhet" {{ request('city') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                    <option value="Sajek" {{ request('city') == 'Sajek' ? 'selected' : '' }}>Sajek</option>
                    <option value="Sundarban" {{ request('city') == 'Sundarban' ? 'selected' : '' }}>Sundarban</option>
                    <option value="Kuakata" {{ request('city') == 'Kuakata' ? 'selected' : '' }}>Kuakata</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm" style="font-size: 13px;">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Property Types</option>
                    <option value="hotel" {{ request('type') == 'hotel' ? 'selected' : '' }}>Hotel &amp; Resort</option>
                    <option value="houseboat" {{ request('type') == 'houseboat' ? 'selected' : '' }}>Ship &amp; Houseboat</option>
                    <option value="homestay" {{ request('type') == 'homestay' ? 'selected' : '' }}>Eco Cottage &amp; Homestay</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="font-size: 13px;">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Properties</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active / Listed</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Draft / Inactive</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Featured Only</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by name..." style="font-size: 13px;">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="background-color: #2067e1; font-size: 12.5px;">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['city', 'type', 'status', 'search']))
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-light btn-sm text-secondary border fw-bold" title="Reset Filters" style="font-size: 12.5px;">Reset</a>
                @endif
            </div>
        </form>
    </div>
    {{-- SAAS DATA TABLE CARD --}}
    <form action="{{ route('admin.properties.bulk-action') }}" method="POST" id="bulkPropertiesForm">
        @csrf
        <div class="data-table-card p-0">
            <div class="saas-table-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-hotel me-1 text-primary"></i> All Inventory Items ({{ isset($properties) ? ($properties->total() ?? count($properties)) : 0 }} Listed)</h6>
                    {{-- Bulk Action Dropdown --}}
                    <div class="d-flex align-items-center gap-1 ms-3">
                        <select name="bulk_action" class="form-select form-select-sm" style="font-size:12px; width:150px; height:32px;">
                            <option value="">Bulk Actions...</option>
                            <option value="approve">✅ Approve Selected</option>
                            <option value="deactivate">⏸️ Deactivate Selected</option>
                            <option value="delete">❌ Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary fw-bold" style="height:32px; font-size:11.5px;" onclick="return confirm('Apply bulk action to selected properties?')">Apply</button>
                    </div>
                </div>
                <div style="width:240px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Quick search properties..." onkeyup="filterTableSearch('inventoryTable', this.value)">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table stockifly-data-table align-middle mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('inventoryTable', this)" title="Select All Rows"></th>
                            <th>Property Image &amp; Title</th>
                            <th>Category</th>
                            <th>City / Location</th>
                            <th>Base Price/Night</th>
                            <th>Rating</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($properties ?? [] as $p)
                        <tr>
                            <td style="text-align:center;"><input type="checkbox" name="ids[]" value="{{ $p->id }}" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ $p->primary_image ?? 'https://placehold.co/50x38/1890ff/white?text=Hotel' }}"
                                         style="width:50px; height:38px; object-fit:cover; border-radius:5px; border:1px solid #e8e8e8;" alt="">
                                    <div>
                                        <strong style="font-size:13px; color:#1e293b; display:block;">{{ Str::limit($p->name, 35) }}</strong>
                                        <div class="d-flex align-items-center gap-1 mt-0.5">
                                            <span style="font-size:10px; color:#8c8c8c;">#PROP-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            @if(!empty($p->vendor_id) && $p->vendor)
                                                <span class="badge bg-light text-dark border" style="font-size:9.5px; padding:1px 5px;" title="Registered Direct Vendor">
                                                    <i class="fa-solid fa-user-tie text-primary me-0.5"></i> {{ Str::limit($p->vendor->name, 14) }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-secondary border" style="font-size:9.5px; padding:1px 5px;" title="Imported via OTA API Feed or Admin Direct">
                                                    <i class="fa-solid fa-cloud text-info me-0.5"></i> API Feed / System
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-gateway">{{ strtoupper($p->type ?? 'HOTEL') }}</span></td>
                            <td><strong style="font-size:12.5px; color:#334155;"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $p->city ?? 'Bangladesh' }}</strong></td>
                            <td><strong style="color:#2067e1; font-size:13px;">BDT {{ number_format($p->price_per_night ?? 0) }}</strong></td>
                            <td><span style="color:#ff9f43; font-size:12px;">{{ str_repeat('★', $p->star_rating ?? 5) }}</span></td>
                            <td>
                                @if($p->is_featured ?? false)
                                    <span class="badge-status confirmed">Featured</span>
                                @else
                                    <span style="font-size:11px; color:#8c8c8c;">Normal</span>
                                @endif
                            </td>
                            <td>
                                @if(($p->status ?? '') == 'active')
                                    <span class="badge-status active">Active</span>
                                @elseif(($p->status ?? '') == 'pending')
                                    <span class="badge-status pending" style="background:#fff7e6; color:#d46b08; border:1px solid #ffd591; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11.5px;">Pending Review</span>
                                @elseif(($p->status ?? '') == 'rejected')
                                    <span class="badge-status rejected" style="background:#fff2f0; color:#ff4d4f; border:1px solid #ffccc7; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11.5px;" title="{{ $p->rejection_reason ?? 'Rejected' }}">Rejected</span>
                                @else
                                    <span class="badge-status cancelled">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                @if(($p->status ?? '') == 'pending')
                                    <form action="{{ route('admin.properties.approve', $p->id) }}" method="POST" class="d-inline-block me-1 m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success text-white" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:12px;" title="Approve & Publish Live">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger me-1" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:12px;" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}" title="Reject with Feedback">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>

                                    {{-- REJECT MODAL --}}
                                    <div class="modal fade text-start" id="rejectModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content" style="border-radius:8px;">
                                                <form action="{{ route('admin.properties.reject', $p->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header border-bottom py-2.5 px-3">
                                                        <h6 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Reject Property Listing</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <p class="mb-2 text-dark" style="font-size:13px;">Please provide rejection feedback for <strong>{{ $p->name }}</strong>:</p>
                                                        <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" placeholder="e.g. Trade license missing or image quality insufficient..." required style="font-size:12.5px;"></textarea>
                                                    </div>
                                                    <div class="modal-footer border-top py-2 px-3">
                                                        <button type="button" class="btn btn-light btn-sm text-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger btn-sm fw-bold"><i class="fa-solid fa-paper-plane me-1"></i> Submit Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="dropdown action-gear-dropdown d-inline-block">
                                    <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                        <i class="fa-solid fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.properties.edit', $p->id) }}">
                                                <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Listing
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.rooms.index', $p->id) }}">
                                                <i class="fa-solid fa-bed text-success me-2"></i> Manage Rooms
                                            </a>
                                        </li>
                                        @if(($p->status ?? '') == 'pending')
                                            <li>
                                                <form action="{{ route('admin.properties.approve', $p->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 text-success fw-bold">
                                                        <i class="fa-solid fa-circle-check me-2"></i> Approve &amp; Publish Live
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('admin.properties.toggle-status', $p->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 text-secondary">
                                                        <i class="fa-solid {{ ($p->status ?? 'active') == 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-secondary' }} me-2"></i> 
                                                        {{ ($p->status ?? 'active') == 'active' ? 'Deactivate' : 'Approve / Activate' }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3" href="{{ route('hotels.preview', $p->id) }}" target="_blank">
                                                <i class="fa-solid fa-eye text-primary me-2"></i> Preview on Web
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.properties.destroy', $p->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this listing permanently?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Delete Property
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5" style="background:#ffffff;">
                                <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                    <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                        <i class="fa-solid fa-hotel"></i>
                                    </div>
                                    <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Properties Found</h6>
                                    <p style="font-size:12px; color:#64748b; margin-bottom:16px;">No hotel, ship or cottage listings match your search criteria.</p>
                                    <a href="{{ route('admin.properties.create') }}" class="btn btn-primary btn-sm fw-bold px-3" style="background-color: #2067e1;">
                                        <i class="fa-solid fa-plus me-1"></i> Add First Property
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($properties, 'hasPages') && $properties->hasPages())
                <div class="stockifly-table-footer border-top">
                    <div>Showing {{ $properties->firstItem() }}–{{ $properties->lastItem() }} of {{ $properties->total() }} Properties</div>
                    <div>{{ $properties->links() }}</div>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
