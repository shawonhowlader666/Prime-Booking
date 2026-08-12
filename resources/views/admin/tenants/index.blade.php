@extends('layouts.admin')
@section('title', 'SaaS Vendor Tenants | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>SaaS</span>
        <span class="sep">-</span><strong style="color:#333;">Vendor Tenants</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">SaaS Vendor Tenants &amp; Commission Management</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#modalAddTenant">
                <i class="fa-solid fa-plus"></i> Add New Tenant / Partner
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-users-gear"></i></div>
                    <div>
                        <p class="kpi-value">{{ $tenants->total() }}</p>
                        <p class="kpi-label">Active Tenants</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-arrow-up"></i> Real-time DB Count</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-percent"></i></div>
                    <div>
                        <p class="kpi-value">{{ number_format($tenants->avg('commission_rate') ?? 10, 1) }}%</p>
                        <p class="kpi-label">Avg Commission Rate</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-chart-line"></i> Dynamic SaaS Revenue</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
    </div>

    {{-- Tenants Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>SaaS Vendor Tenant Directory</h6>
            <span class="live-feed-badge">Live DB Feed</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
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
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $tenant->name }}</strong>
                            <span style="font-size:11px; color:#8c8c8c;">ID #{{ $tenant->id }}</span>
                        </td>
                        <td>
                            <strong style="font-size:12.5px; color:#334155; display:block;">{{ $tenant->owner_name ?: $tenant->name }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $tenant->email }} | {{ $tenant->phone ?: 'N/A' }}</span>
                        </td>
                        <td><span class="badge-gateway">{{ $tenant->saas_plan }}</span></td>
                        <td><strong style="color:#28c76f; font-size:13px;">{{ $tenant->commission_rate }}%</strong></td>
                        <td>
                            <span class="badge-status {{ $tenant->status }}">
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
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.tenants.update', $tenant->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Tenant #{{ $tenant->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3 text-start">
                                            <label class="form-label fw-bold">Business / Tenant Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $tenant->name }}" required>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label fw-bold">Owner Name</label>
                                            <input type="text" name="owner_name" class="form-control" value="{{ $tenant->owner_name }}">
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6 text-start">
                                                <label class="form-label fw-bold">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $tenant->email }}" required>
                                            </div>
                                            <div class="col-6 text-start">
                                                <label class="form-label fw-bold">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $tenant->phone }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6 text-start">
                                                <label class="form-label fw-bold">SaaS Plan</label>
                                                <select name="saas_plan" class="form-select">
                                                    <option value="Starter" {{ $tenant->saas_plan == 'Starter' ? 'selected' : '' }}>Starter</option>
                                                    <option value="Pro Partner" {{ $tenant->saas_plan == 'Pro Partner' ? 'selected' : '' }}>Pro Partner</option>
                                                    <option value="Enterprise" {{ $tenant->saas_plan == 'Enterprise' ? 'selected' : '' }}>Enterprise</option>
                                                </select>
                                            </div>
                                            <div class="col-6 text-start">
                                                <label class="form-label fw-bold">Commission Rate (%)</label>
                                                <input type="number" step="0.1" name="commission_rate" class="form-control" value="{{ $tenant->commission_rate }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3 text-start">
                                            <label class="form-label fw-bold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" {{ $tenant->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="suspended" {{ $tenant->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                <option value="pending" {{ $tenant->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-secondary">No tenants found in database.</td>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tenants.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New SaaS Tenant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Business Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Royal Tulip Resort Group" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Owner / Manager Name</label>
                        <input type="text" name="owner_name" class="form-control" placeholder="e.g. Kazi Tanvir">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 text-start">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="vendor@domain.com" required>
                        </div>
                        <div class="col-6 text-start">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="01711...">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 text-start">
                            <label class="form-label fw-bold">SaaS Plan</label>
                            <select name="saas_plan" class="form-select">
                                <option value="Starter">Starter</option>
                                <option value="Pro Partner">Pro Partner</option>
                                <option value="Enterprise">Enterprise</option>
                            </select>
                        </div>
                        <div class="col-6 text-start">
                            <label class="form-label fw-bold">Commission Rate (%)</label>
                            <input type="number" step="0.1" name="commission_rate" class="form-control" value="10.0" required>
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

