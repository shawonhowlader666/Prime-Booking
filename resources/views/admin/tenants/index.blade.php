@extends('layouts.admin')
@section('title', 'Vendor Tenants & Commission Management | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Users &amp; Vendors</span>
        <span class="sep">-</span><strong style="color:#333;">Vendor Tenants</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Vendor Tenants &amp; Commission Management</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage hotel operator accounts, multi-tenant SaaS tiers, and custom commission rates</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('tenantsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('tenantsTable', 'Vendor_Tenants')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('tenantsTable', 'Vendor_Tenants')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('tenantsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('tenantsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#modalAddTenant" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add New Tenant</span>
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

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="tenantsTable">
                <thead>
                    <tr>
                        <th>Vendor / Business</th>
                        <th>Owner &amp; Contact</th>
                        <th>SaaS Plan</th>
                        <th>Commission Rate</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $tenant->name }}</strong>
                            <span style="font-size:11px; color:#64748b;">ID #{{ $tenant->id }}</span>
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
                            <span class="badge-status {{ $tenant->status == 'active' ? 'confirmed' : 'cancelled' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#editTenantModal{{ $tenant->id }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Business Tenant
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.tenants.toggle', $tenant->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid fa-ban me-2"></i> {{ $tenant->status === 'active' ? 'Suspend Tenant' : 'Activate Tenant' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this tenant?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Tenant
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

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
                        <td colspan="6" class="text-center py-5" style="background:#ffffff;">
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
