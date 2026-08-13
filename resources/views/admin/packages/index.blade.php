@extends('layouts.admin')

@php use App\Services\CurrencyService; @endphp

@section('title', 'Tour Packages Control — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Tour Packages</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <div>
            <h1 class="page-title">Admin Tour Packages Control Center</h1>
            <span style="font-size:12px; color:#8c8c8c;">Review, approve, and manage all platform tour packages submitted by vendors and admins</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('packagesTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('packagesTable', 'Tour_Packages')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('packagesTable', 'Tour_Packages')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('packagesTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('packagesTable')"><i class="fa-solid fa-print"></i> Print</button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Summary Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL PACKAGES</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $totalCount }} Listed</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-suitcase-rolling"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE LIVE</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $activeCount }} Live</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-suitcase-rolling me-1 text-primary"></i> Tour Packages &amp; Excursions Inventory ({{ count($packages) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search packages..." onkeyup="filterTableSearch('packagesTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="packagesTable">
                <thead>
                    <tr>
                        <th style="width:220px;">Package Title</th>
                        <th>Partner / Vendor</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Price / Person</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $pkg)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $pkg->featured_image }}" alt="" style="width: 46px; height: 34px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                <div>
                                    <div style="font-weight:600; font-size:13px; color:#1e293b;">{{ $pkg->title }}</div>
                                    <small style="color:#8c8c8c;">Added: {{ $pkg->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600; font-size:13px;">{{ $pkg->vendor?->name ?? 'System Admin' }}</div>
                            <small style="color:#8c8c8c; font-size:11px;">{{ $pkg->vendor?->email ?? 'admin@primebooking.com' }}</small>
                        </td>
                        <td><span class="badge bg-info text-dark" style="font-size:11px;">{{ $pkg->destination }}</span></td>
                        <td style="font-size:12.5px; font-weight:500;">{{ $pkg->duration_days }}D / {{ $pkg->duration_nights }}N</td>
                        <td style="font-weight:700; color:#28c76f; font-size:13px;">{{ CurrencyService::format($pkg->price_per_person) }}</td>
                        <td>
                            @if($pkg->status === 'active')
                            <span class="badge-status confirmed">🟢 Active</span>
                            @else
                            <span class="badge-status pending">⏰ {{ ucfirst($pkg->status) }}</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid {{ $pkg->status === 'active' ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i>
                                                {{ $pkg->status === 'active' ? 'Deactivate Package' : 'Approve & Make Live' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to remove this package permanently?');">
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
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Tour Packages Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no tour packages or holiday itineraries registered in the system database yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($packages, 'hasPages') && $packages->hasPages())
        <div class="px-3 py-2 border-top">{{ $packages->links() }}</div>
        @endif
    </div>

</div>
@endsection
