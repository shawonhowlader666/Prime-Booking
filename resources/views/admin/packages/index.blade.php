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
            <a href="{{ route('admin.packages.create') }}" class="btn-add-primary" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>New Tour Package</span>
            </a>
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
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL TOUR PACKAGES</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ count($packages) }} Listed</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE LIVE</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $packages->where('status', 'active')->count() }} Active</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">SUNDARBANS &amp; CRUISE</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $packages->filter(fn($p) => str_contains(strtolower($p->destination), 'sundarban'))->count() }} Packages</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">HILL TRACTS &amp; SAJEK</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $packages->filter(fn($p) => str_contains(strtolower($p->destination), 'sajek'))->count() }} Packages</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-suitcase-rolling me-2 text-primary"></i> Tour Packages Inventory ({{ count($packages) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search packages..." onkeyup="filterTableSearch('packagesTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="packagesTable">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:260px;">Package Banner &amp; Title</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Price / Person</th>
                        <th>Inclusions</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $pkg)
                    <tr>
                        <td><strong>#{{ $pkg->id }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="{{ $pkg->featured_image ?: 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=300&q=80' }}" alt="" style="width: 52px; height: 38px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                <div>
                                    <div style="font-weight:700; font-size:13px; color:#1e293b;">
                                        {{ $pkg->title }}
                                    </div>
                                    <small style="color:#64748b; font-size:11px;">Added: {{ $pkg->created_at?->format('d M Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark" style="font-size:11px; padding:4px 8px; border-radius:4px;">{{ $pkg->destination }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:12px; font-weight:600; padding:4px 8px; border-radius:4px;">
                                <i class="fa-solid fa-clock me-1 text-primary"></i> {{ $pkg->duration_days }}D / {{ $pkg->duration_nights }}N
                            </span>
                        </td>
                        <td style="font-weight:700; color:#28c76f; font-size:13.5px;">
                            ৳ {{ number_format($pkg->price_per_person) }} BDT
                        </td>
                        <td style="max-width:240px;">
                            @if(is_array($pkg->inclusions) && count($pkg->inclusions) > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(array_slice($pkg->inclusions, 0, 2) as $inc)
                                <span class="badge bg-light text-secondary border" style="font-size:10.5px;">✓ {{ $inc }}</span>
                                @endforeach
                                @if(count($pkg->inclusions) > 2)
                                <span class="badge bg-light text-primary border" style="font-size:10.5px;">+{{ count($pkg->inclusions) - 2 }} more</span>
                                @endif
                            </div>
                            @else
                            <span style="color:#94a3b8; font-size:11px;">Standard inclusions</span>
                            @endif
                        </td>
                        <td>
                            @if($pkg->status === 'active')
                            <span class="badge-status confirmed">🟢 Active</span>
                            @else
                            <span class="badge-status cancelled">⚪ Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.packages.edit', $pkg) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Tour Package
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this tour package permanently?');">
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
                        <td colspan="8" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-suitcase-rolling"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Tour Packages Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no tour packages or holiday itineraries registered in the system database yet.</p>
                                <a href="{{ route('admin.packages.create') }}" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;">
                                    <i class="fa-solid fa-plus"></i> Create First Package
                                </a>
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
@endsection
