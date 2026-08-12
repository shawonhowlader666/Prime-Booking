@extends('layouts.admin')
@section('title', 'User & Account Management | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Users</span>
        <span class="sep">-</span><strong style="color:#333;">All Accounts</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:6px;">
        <h1 class="page-title">User Accounts &amp; Role Management</h1>
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('usersTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('usersTable', 'users')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('usersTable', 'users')" title="Export to CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('usersTable', 'users')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('usersTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button>
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

    {{-- FILTER BAR --}}
    <div class="page-filters-bar">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="height:32px; font-size:12px;">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Role Filter</label>
                    <select name="role" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                        <option value="vendor" {{ request('role') == 'vendor' ? 'selected' : '' }}>Vendors / Partners</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Guests / Customers</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Account Status</label>
                    <select name="status" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned / Suspended</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px;">Search Users</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, email, phone..." style="height:32px; font-size:12px;">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="height:32px; font-size:12px; font-weight:600;" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border btn-sm" style="height:32px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; justify-content:center;" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#7367f0;"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['total'] ?? 0 }}</p>
                        <p class="kpi-label">Registered Accounts</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-user-check"></i> System Users</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#28c76f;"><i class="fa-solid fa-hotel"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['vendors'] ?? 0 }}</p>
                        <p class="kpi-label">Vendor Partners</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-city"></i> Hotel &amp; Ship Operators</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#00cfe8;"><i class="fa-solid fa-user-gear"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['admins'] ?? 0 }}</p>
                        <p class="kpi-label">Administrators</p>
                        <p class="kpi-growth-up"><i class="fa-solid fa-shield"></i> Full System Access</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="kpi-icon" style="background:#ea5455;"><i class="fa-solid fa-user-slash"></i></div>
                    <div>
                        <p class="kpi-value">{{ $stats['banned'] ?? 0 }}</p>
                        <p class="kpi-label">Suspended / Banned</p>
                        <p class="kpi-growth-down"><i class="fa-solid fa-ban"></i> Restricted Access</p>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h6 style="margin:0;">User Directory &amp; Permissions Control</h6>
                <span class="live-feed-badge">Active Directory</span>
            </div>
            <div class="tbl-search-wrap">
                <i class="fa-solid fa-magnifying-glass tbl-search-icon"></i>
                <input type="text" class="tbl-search-input" placeholder="Quick search users..." onkeyup="filterTableSearch('usersTable', this.value)">
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="usersTable" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('usersTable', this)" title="Select All Rows"></th>
                        <th>User &amp; Auth Provider</th>
                        <th>Phone &amp; Location</th>
                        <th>Role</th>
                        <th>Bookings / Total Spent</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions <div style="position:relative; display:inline-block; margin-left:4px;"><button type="button" class="btn-tbl-gear" onclick="toggleColVis('usersTable', this)" title="Column Settings"><i class="fa-solid fa-gear"></i></button><div class="col-vis-dropdown" id="colVisDropdown_usersTable" style="display:none;"></div></div></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                @if($u->avatar)
                                    <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #2067e1; font-size: 14px;">
                                        {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $u->name }}</strong>
                                    <span style="font-size:11px; color:#8c8c8c;">{{ $u->email }}</span>
                                    @if($u->google_id || $u->socialAccounts->contains('provider', 'google'))
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold ms-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-brands fa-google me-1"></i> Google OAuth
                                        </span>
                                    @elseif($u->socialAccounts->contains('provider', 'facebook'))
                                        <span class="badge bg-info bg-opacity-10 text-info fw-semibold ms-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-brands fa-facebook-f me-1"></i> Facebook
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold ms-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-solid fa-envelope me-1"></i> Email
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12.5px; color:#334155;">{{ $u->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge-gateway" style="text-transform:uppercase; font-weight:700;">
                                {{ $u->role ?? 'CUSTOMER' }}
                            </span>
                        </td>
                        <td>
                            <strong class="d-block text-dark" style="font-size: 13px;">{{ $u->bookings_count ?? 0 }} Bookings</strong>
                            <small class="text-primary fw-bold" style="font-size: 11.5px;">{{ \App\Services\CurrencyService::format($u->bookings ? $u->bookings->sum('total_price') : 0) }}</small>
                        </td>
                        <td style="font-size:12px; color:#8c8c8c;">
                            {{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-status {{ $u->status == 'banned' ? 'cancelled' : 'confirmed' }}">
                                {{ ucfirst($u->status ?? 'Active') }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.users.show', $u->id) }}">
                                            <i class="fa-solid fa-eye text-primary me-2"></i> View User Profile
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.users.ban', $u->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 {{ $u->status == 'banned' ? 'text-success' : 'text-danger' }}">
                                                <i class="fa-solid fa-ban me-2"></i> {{ $u->status == 'banned' ? 'Unban Account' : 'Ban Account' }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:#8c8c8c;">
                            <i class="fa-solid fa-user-slash" style="font-size:28px; color:#d9d9d9; display:block; margin-bottom:8px;"></i>
                            No users matching filter criteria.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$users" :perPage="25" />
    </div>

</div>
@endsection

