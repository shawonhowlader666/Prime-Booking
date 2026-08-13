@extends('layouts.admin')

@section('title', 'Tour Packages Manager — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Tour Packages</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Tour Packages &amp; Holiday Itineraries</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage guided tours, Sundarbans cruises, and weekend getaway packages</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('packagesTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('packagesTable', 'Tour_Packages')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('packagesTable', 'Tour_Packages')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('packagesTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('packagesTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>New Tour Package</span>
            </button>
        </div>
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
        $pkgColl = method_exists($packages, 'getCollection') ? $packages->getCollection() : collect($packages);
        $totalPackages  = method_exists($packages, 'total') ? $packages->total() : $pkgColl->count();
        $activePackages = $pkgColl->where('status', 'active')->count();
        $avgPrice       = $pkgColl->avg('price_per_person');
        $multiDayPkgs   = $pkgColl->where('duration_days', '>', 1)->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL PACKAGES</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $totalPackages }} Listed</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE LIVE PACKAGES</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $activePackages }} Active</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">AVG PACKAGE RATE</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">৳ {{ number_format($avgPrice ?? 0) }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">MULTI-DAY TOURS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $multiDayPkgs }} Packages</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-suitcase-rolling me-2 text-primary"></i> Tour Packages &amp; Itineraries Directory ({{ count($packages) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search packages..." onkeyup="filterTableSearch('packagesTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="packagesTable">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;">#</th>
                        <th>Package Title &amp; Destination</th>
                        <th>Duration</th>
                        <th>Price Per Person</th>
                        <th>Inclusions Highlight</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($packages as $pkg)
                    <tr>
                        <td style="text-align:center; font-weight:600; color:#8c8c8c;">{{ $pkg->id }}</td>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $pkg->title }}</strong>
                            <span style="font-size:11.5px; color:#64748b;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $pkg->destination ?? 'Bangladesh' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:11.5px; font-weight:600;">
                                <i class="fa-solid fa-clock me-1 text-primary"></i>
                                {{ $pkg->duration_days ?? 1 }} Days / {{ $pkg->duration_nights ?? 0 }} Nights
                            </span>
                        </td>
                        <td style="font-size:13.5px; font-weight:700; color:#28c76f;">
                            ৳ {{ number_format($pkg->price_per_person ?? 0) }} BDT
                        </td>
                        <td>
                            @if(!empty($pkg->inclusions) && is_array($pkg->inclusions))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice($pkg->inclusions, 0, 3) as $inc)
                                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:10px; font-weight:600; border-radius:3px;">{{ $inc }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color:#8c8c8c; font-size:11.5px;">All Inclusive</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0" title="Click to toggle status">
                                    <span class="badge-status {{ strtolower($pkg->status ?? 'active') == 'active' ? 'confirmed' : 'cancelled' }}">
                                        {{ ucfirst($pkg->status ?? 'Active') }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.packages.edit', $pkg->id) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Package
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid fa-power-off me-2"></i> {{ strtolower($pkg->status ?? 'active') == 'active' ? 'Deactivate Package' : 'Activate Package' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete package {{ $pkg->title }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Package
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-suitcase-rolling"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Tour Packages Listed</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active tour packages or holiday itineraries created yet.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                                    <i class="fa-solid fa-plus"></i> Create First Package
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$packages" :perPage="15" />
    </div>

</div>

{{-- CREATE PACKAGE MODAL --}}
<div class="modal fade" id="addPackageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-suitcase-rolling text-primary me-2"></i> Create Tour Package &amp; Holiday Itinerary
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.packages.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Package Title <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Sundarbans Wild Safari & Cruise" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Destination City / Location <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="destination" class="form-control" placeholder="e.g. Sundarbans, Khulna" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Price Per Person (৳) <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" name="price_per_person" class="form-control" placeholder="e.g. 12500" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Duration (Days)</label>
                            <input type="number" name="duration_days" class="form-control" placeholder="e.g. 3" min="1" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Duration (Nights)</label>
                            <input type="number" name="duration_nights" class="form-control" placeholder="e.g. 2" min="0" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Featured Image URL</label>
                            <input type="url" name="featured_image" class="form-control" placeholder="https://images.unsplash.com/photo-..." style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Package <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
