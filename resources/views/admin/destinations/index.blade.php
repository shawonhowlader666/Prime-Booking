@extends('layouts.admin')
@section('title', 'Destination Banners & Media | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Destination Banners &amp; Media Manager</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('destinationsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('destinationsTable', 'Destinations')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('destinationsTable', 'Destinations')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('destinationsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('destinationsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#createDestinationModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add Destination Banner</span>
            </button>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS Pages</span>
        <span class="sep">-</span><strong style="color:#333;">Destination Banners</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stockifly KPI Summary Cards Row --}}
    @php
        $totalDestCount = count($destinations);
        $activeDestCount = $destinations->where('is_active', true)->count();
        $featuredDestCount = $destinations->where('is_featured', true)->count();
        $inactiveDestCount = $destinations->where('is_active', false)->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">ACTIVE DESTINATIONS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $activeDestCount }} Cities</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">HOMEPAGE FEATURED</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $featuredDestCount }} Cards</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">INACTIVE / DRAFT</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $inactiveDestCount }} Cities</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">TOTAL BANNERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $totalDestCount }} Total</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-map-location-dot me-2 text-primary"></i> Popular Travel Destinations ({{ count($destinations) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search destinations..." onkeyup="filterTableSearch('destinationsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="destinationsTable">
                <thead>
                    <tr>
                        <th style="width:75px;">Media</th>
                        <th>City &amp; Country</th>
                        <th>Tagline &amp; Highlight</th>
                        <th>Live Hotels Linked</th>
                        <th>Starting Price</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($destinations as $d)
                    <tr>
                        <td>
                            <img src="{{ $d->image_url }}" alt="{{ $d->city }}" class="rounded border shadow-sm" style="width:58px; height:38px; object-fit:cover; border-radius:4px;">
                        </td>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $d->city }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $d->country ?? 'Bangladesh' }}</span>
                        </td>
                        <td>
                            <span style="font-size:12px; color:#475569;">{{ $d->description ?: 'Explore stays & resorts' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 fw-bold" style="font-size:11px; padding:4px 8px; border-radius:4px;">
                                <i class="fa-solid fa-hotel me-1"></i> {{ $d->property_count }} Hotels
                            </span>
                        </td>
                        <td>
                            @if($d->min_price > 0)
                                <span style="font-size:12.5px; font-weight:700; color:#16a34a;">৳ {{ number_format($d->min_price) }} /night</span>
                            @else
                                <span style="font-size:11.5px; color:#94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">
                                Order #{{ $d->sort_order }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status {{ $d->is_active ? 'confirmed' : 'cancelled' }}">
                                {{ $d->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $d->id }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Destination Banner
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.destinations.destroy', $d->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this destination banner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Destination
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- EDIT DESTINATION MODAL --}}
                    <div class="modal fade" id="editModal{{ $d->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <form action="{{ route('admin.destinations.update', $d->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                        <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                            <i class="fa-solid fa-pen text-primary me-2"></i> Edit Destination: {{ $d->city }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="padding:20px;">
                                        <div class="mb-3 text-center">
                                            <img src="{{ $d->image_url }}" class="img-fluid rounded border shadow-sm" style="height:90px; width:100%; object-fit:cover; border-radius:4px;">
                                        </div>
                                        <div class="row g-2.5 mb-3">
                                            <div class="col-7">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">City / District <span style="color:#ff4d4f;">*</span></label>
                                                <input type="text" name="city" class="form-control" value="{{ $d->city }}" required style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                            <div class="col-5">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Country</label>
                                                <input type="text" name="country" class="form-control" value="{{ $d->country ?? 'Bangladesh' }}" style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Upload Banner Image from Computer / Gallery</label>
                                            <input type="file" name="image_file" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;">
                                            <span style="font-size:10.5px; color:#94a3b8;">OR keep image URL below:</span>
                                            <input type="text" name="image_url" class="form-control" value="{{ $d->image_url }}" style="font-size:13px; height:38px; border-radius:4px;">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Tagline Description</label>
                                            <textarea name="description" class="form-control" rows="2" style="font-size:13px; border-radius:4px;">{{ $d->description }}</textarea>
                                        </div>
                                        <div class="row g-2.5 mb-3">
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sort Order</label>
                                                <input type="number" name="sort_order" class="form-control" value="{{ $d->sort_order }}" style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                            <div class="col-6 pt-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck{{ $d->id }}" {{ $d->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="activeCheck{{ $d->id }}" style="font-size:12.5px;">Active Status</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featuredCheck{{ $d->id }}" {{ $d->is_featured ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold text-primary" for="featuredCheck{{ $d->id }}" style="font-size:12.5px;">Featured Card</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                        <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Save Changes <i class="fa-solid fa-check ms-1"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Destination Banners Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no travel destination banners listed in the database.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#createDestinationModal">
                                    <i class="fa-solid fa-plus"></i> Add First Destination
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$destinations" :perPage="15" />
    </div>

</div>

{{-- CREATE DESTINATION MODAL --}}
<div class="modal fade" id="createDestinationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                        <i class="fa-solid fa-plus text-primary me-2"></i> Add New Destination Banner
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="row g-2.5 mb-3">
                        <div class="col-7">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">City / District <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Cox's Bazar, Sylhet" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-5">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Country</label>
                            <input type="text" name="country" class="form-control" value="Bangladesh" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Upload Image from Gallery / Computer</label>
                        <input type="file" name="image_file" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;">
                        <span style="font-size:10.5px; color:#94a3b8;">OR paste image URL below:</span>
                        <input type="text" name="image_url" class="form-control" placeholder="https://images.unsplash.com/..." style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Tagline Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g. World's longest natural sea beach with 5-star resorts" style="font-size:13px; border-radius:4px;"></textarea>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Starting Price (BDT / Night)</label>
                            <input type="number" step="0.01" name="min_price_override" class="form-control" placeholder="e.g. 2500" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Hotels Count Override</label>
                            <input type="number" name="property_count_override" class="form-control" placeholder="Auto-calculated if blank" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ count($destinations) + 1 }}" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6 pt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addActiveCheck" checked>
                                <label class="form-check-label fw-bold" for="addActiveCheck" style="font-size:12.5px;">Active Status</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="addFeaturedCheck" checked>
                                <label class="form-check-label fw-bold text-primary" for="addFeaturedCheck" style="font-size:12.5px;">Featured Card</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Destination <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
