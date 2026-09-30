@extends('layouts.admin')
@section('title', 'Vendor Tenants & Commission Management | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Vendor Tenants &amp; Commission Management</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button class="btn-tbl-copy" onclick="copyTableToClipboard('tenantsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('tenantsTable', 'Vendor_Tenants')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('tenantsTable', 'Vendor_Tenants')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('tenantsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('tenantsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#modalAddTenant" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add New Tenant</span>
            </button></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Users &amp; Vendors</span>
        <span class="sep">-</span><strong style="color:#333;">Vendor Tenants</strong>
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
        $tenantColl = method_exists($tenants, 'getCollection') ? $tenants->getCollection() : collect($tenants);
        $totalTenants = method_exists($tenants, 'total') ? $tenants->total() : $tenantColl->count();
        $activeTenants = $tenantColl->where('status', 'active')->count();
        $avgCommission = $tenantColl->avg('commission_rate') ?? 10.0;
        $enterpriseCount = $tenantColl->where('saas_plan', 'Enterprise')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">ACTIVE TENANTS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $activeTenants }} Partners</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">AVG COMMISSION RATE</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ number_format($avgCommission, 1) }}% Rate</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">ENTERPRISE PARTNERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $enterpriseCount }} Enterprise</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">TOTAL REGISTERED</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $totalTenants }} Total</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-users-gear me-2 text-primary"></i> SaaS Vendor Tenant Directory ({{ count($tenants) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search tenants..." onkeyup="filterTableSearch('tenantsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive" style="min-height: 280px; overflow-x: auto; padding-bottom: 30px;">
            <table class="table table-stockifly mb-0 align-middle" id="tenantsTable">
                <thead>
                    <tr>
                        <th style="min-width: 170px;">Vendor / Business</th>
                        <th style="min-width: 190px;">Owner &amp; Contact</th>
                        <th style="min-width: 110px;">SaaS Plan</th>
                        <th style="min-width: 120px;">Commission Rate</th>
                        <th style="min-width: 180px;">Properties &amp; Rooms</th>
                        <th style="min-width: 90px;">Status</th>
                        <th style="text-align:right; min-width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $tenant->name }}</strong>
                            <span style="font-size:11px; color:#64748b;">Tenant ID #{{ $tenant->id }}</span>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#334155; display:block;">{{ $tenant->owner_name ?: $tenant->name }}</strong>
                            <span style="font-size:11.5px; color:#64748b;">{{ $tenant->email }} | {{ $tenant->phone ?: 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary border-opacity-25" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">
                                {{ $tenant->saas_plan }}
                            </span>
                        </td>
                        <td><strong style="color:#28c76f; font-size:13.5px;">{{ $tenant->commission_rate }}%</strong></td>
                        <td>
                            @php
                                $hotelSearchParam = $tenant->user ? ['source' => $tenant->user->id] : ['search' => $tenant->name];
                            @endphp
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('admin.properties.index', $hotelSearchParam) }}" class="badge bg-primary bg-opacity-10 text-primary fw-bold text-decoration-none" style="font-size:11px; padding:3px 7px; border-radius:4px; text-align:left;" title="Filter hotels for this tenant">
                                    <i class="fa-solid fa-hotel me-1"></i> {{ $tenant->properties_count ?? 0 }} Hotels Listed
                                </a>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size:11px; padding:3px 7px; border-radius:4px; text-align:left;">
                                    <i class="fa-solid fa-bed me-1"></i> {{ $tenant->rooms_count ?? 0 }} Rooms Inventory
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-status {{ $tenant->status == 'active' ? 'confirmed' : 'cancelled' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="d-inline-flex align-items-center gap-1.5 justify-content-end">
                                {{-- 1. Direct View Button --}}
                                <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#viewTenantModal{{ $tenant->id }}" title="View Full Profile" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:12.5px;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                {{-- 2. Direct Edit Button --}}
                                <button type="button" class="btn btn-sm btn-light border text-warning" data-bs-toggle="modal" data-bs-target="#editTenantModal{{ $tenant->id }}" title="Edit Business Tenant" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:12.5px;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                {{-- 3. Direct Properties Link --}}
                                <a href="{{ route('admin.properties.index', $hotelSearchParam) }}" class="btn btn-sm btn-light border text-info" title="Manage Listed Hotels ({{ $tenant->properties_count ?? 0 }})" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:12.5px;">
                                    <i class="fa-solid fa-hotel"></i>
                                </a>

                                {{-- 4. Full Action Dropdown --}}
                                <div class="dropdown d-inline-block position-relative">
                                    <button class="btn btn-sm btn-light border text-secondary shadow-none" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="More Options" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; font-size:12.5px;">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                        <li>
                                            <a href="http://127.0.0.1:8080/" target="_blank" class="dropdown-item py-1.5 px-3 text-primary fw-bold">
                                                <i class="fa-solid fa-desktop text-primary me-2"></i> 🚀 Launch HotelAir PMS
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://127.0.0.1:8080/sync-hub" target="_blank" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-rotate text-danger me-2"></i> Prime OTA Sync Hub
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#viewTenantModal{{ $tenant->id }}">
                                                <i class="fa-solid fa-id-card text-primary me-2"></i> Profile &amp; Inventory Details
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#editTenantModal{{ $tenant->id }}">
                                                <i class="fa-solid fa-sliders text-warning me-2"></i> Edit Plan &amp; Commission
                                            </button>
                                        </li>
                                        <li>
                                            <a href="{{ $tenant->user ? route('admin.accounts.ledger', ['vendor_id' => $tenant->user->id]) : route('admin.accounts.vendor-statements') }}" class="dropdown-item py-1.5 px-3 text-secondary">
                                                <i class="fa-solid fa-file-invoice-dollar text-success me-2"></i> Financial Statement &amp; Ledger
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.properties.index', $hotelSearchParam) }}" class="dropdown-item py-1.5 px-3 text-secondary">
                                                <i class="fa-solid fa-hotel text-info me-2"></i> Manage Hotels ({{ $tenant->properties_count ?? 0 }})
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('admin.tenants.toggle', $tenant->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-1.5 px-3 {{ $tenant->status === 'active' ? 'text-warning' : 'text-success' }}">
                                                    <i class="fa-solid {{ $tenant->status === 'active' ? 'fa-pause' : 'fa-play' }} me-2"></i> {{ $tenant->status === 'active' ? 'Suspend Tenant Access' : 'Activate Tenant' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete tenant {{ $tenant->name }} permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Delete Tenant
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- VIEW PROFILE MODAL --}}
                    <div class="modal fade" id="viewTenantModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                        <i class="fa-solid fa-building text-primary me-2"></i> Tenant Profile — {{ $tenant->name }}
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding:20px;">
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Business / Resort Group:</span>
                                            <strong class="text-dark" style="font-size:13.5px;">{{ $tenant->name }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Owner / Manager:</span>
                                            <strong class="text-dark" style="font-size:13px;">{{ $tenant->owner_name ?: 'N/A' }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Email Address:</span>
                                            <span class="text-dark fw-bold" style="font-size:12.5px;">{{ $tenant->email }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Phone Contact:</span>
                                            <span class="text-dark fw-bold" style="font-size:12.5px;">{{ $tenant->phone ?: 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">SaaS Subscription Plan:</span>
                                            <span class="badge bg-primary text-white" style="font-size:11px;">{{ $tenant->saas_plan }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Agreed Commission Rate:</span>
                                            <strong class="text-success" style="font-size:14px;">{{ $tenant->commission_rate }}% Rate</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-secondary" style="font-size:12px;">Total Properties Listed:</span>
                                            <strong class="text-primary" style="font-size:13px;">{{ $tenant->properties_count ?? 0 }} Hotels / Resorts</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-secondary" style="font-size:12px;">Total Rooms Inventory:</span>
                                            <strong class="text-success" style="font-size:13px;">{{ $tenant->rooms_count ?? 0 }} Rooms Total</strong>
                                        </div>
                                    </div>
                                    @if($tenant->notes)
                                    <div class="p-3 bg-white rounded border">
                                        <span class="text-secondary d-block mb-1" style="font-size:11.5px; font-weight:700;">NOTES &amp; AGREEMENTS:</span>
                                        <p class="mb-0 text-dark" style="font-size:12.5px;">{{ $tenant->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer d-flex justify-content-between" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                    <a href="{{ route('admin.properties.index', $hotelSearchParam) }}" class="btn btn-outline-primary btn-sm fw-bold" style="border-radius:4px;">
                                        <i class="fa-solid fa-hotel me-1"></i> View Listed Hotels ({{ $tenant->properties_count ?? 0 }})
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-dismiss="modal" style="border-radius:4px;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Modal --}}
                    <div class="modal fade" id="editTenantModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <form action="{{ route('admin.tenants.update', $tenant->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                        <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                            <i class="fa-solid fa-pen text-primary me-2"></i> Edit Tenant #{{ $tenant->id }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="padding:20px;">
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Business / Tenant Name <span style="color:#ff4d4f;">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $tenant->name }}" required style="font-size:13px; height:38px; border-radius:4px;">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Owner / Manager Name</label>
                                            <input type="text" name="owner_name" class="form-control" value="{{ $tenant->owner_name }}" style="font-size:13px; height:38px; border-radius:4px;">
                                        </div>
                                        <div class="row g-2.5 mb-3">
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Email <span style="color:#ff4d4f;">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ $tenant->email }}" required style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $tenant->phone }}" style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                        </div>
                                        <div class="row g-2.5 mb-3">
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">SaaS Plan</label>
                                                <select name="saas_plan" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                                    <option value="Starter" {{ $tenant->saas_plan == 'Starter' ? 'selected' : '' }}>Starter</option>
                                                    <option value="Pro Partner" {{ $tenant->saas_plan == 'Pro Partner' ? 'selected' : '' }}>Pro Partner</option>
                                                    <option value="Enterprise" {{ $tenant->saas_plan == 'Enterprise' ? 'selected' : '' }}>Enterprise</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Commission Rate (%) <span style="color:#ff4d4f;">*</span></label>
                                                <input type="number" step="0.1" name="commission_rate" class="form-control" value="{{ $tenant->commission_rate }}" required style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Status</label>
                                            <select name="status" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                                <option value="active" {{ $tenant->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="suspended" {{ $tenant->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                <option value="pending" {{ $tenant->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            </select>
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
                        <td colspan="7" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-users-gear"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No SaaS Vendor Tenants Listed</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active hotel or resort vendor tenant accounts found in the database.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#modalAddTenant">
                                    <i class="fa-solid fa-plus"></i> Create First Tenant
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$tenants" :perPage="15" />
    </div>

</div>

{{-- Add Modal --}}
<div class="modal fade" id="modalAddTenant" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('admin.tenants.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                        <i class="fa-solid fa-users-gear text-primary me-2"></i> Add New SaaS Tenant
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Business Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Royal Tulip Resort Group" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Owner / Manager Name</label>
                        <input type="text" name="owner_name" class="form-control" placeholder="e.g. Kazi Tanvir" style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Email <span style="color:#ff4d4f;">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="vendor@domain.com" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="01711..." style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">SaaS Plan</label>
                            <select name="saas_plan" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="Starter">Starter</option>
                                <option value="Pro Partner">Pro Partner</option>
                                <option value="Enterprise">Enterprise</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Commission Rate (%) <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" step="0.1" name="commission_rate" class="form-control" value="10.0" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional internal notes..." style="font-size:13px; border-radius:4px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Tenant <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
