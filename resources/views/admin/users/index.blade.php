@extends('layouts.admin')
@section('title', 'User Accounts & Role Management | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Users &amp; Vendors</span>
        <span class="sep">-</span><strong style="color:#333;">User Accounts</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">User Accounts &amp; Role Management</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage customer accounts, vendor access, super admin privileges, and account suspensions</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('usersTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('usersTable', 'users')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('usersTable', 'users')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="exportTablePDF('usersTable', 'users')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('usersTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-user-plus"></i> <span>Add New User</span>
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="page-filters-bar mb-4" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff; padding:16px;">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Role Filter</label>
                    <select name="role" class="form-select" style="height:36px; font-size:12.5px; border-radius:4px;" onchange="this.form.submit()">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                        <option value="vendor" {{ request('role') == 'vendor' ? 'selected' : '' }}>Vendors / Partners</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Guests / Customers</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Account Status</label>
                    <select name="status" class="form-select" style="height:36px; font-size:12.5px; border-radius:4px;" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned / Suspended</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:5px;">Search Directory</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, email address, or phone..." style="height:36px; font-size:12.5px; border-radius:4px;">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary fw-bold px-3 flex-grow-1" style="height:36px; font-size:12.5px; border-radius:4px; background-color:var(--primary); border:none;">
                        <i class="fa-solid fa-filter me-1"></i> Filter Directory
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border fw-bold text-secondary px-3" style="height:36px; font-size:12.5px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stockifly KPI Summary Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL ACCOUNTS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] ?? 0 }} Users</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">VENDOR PARTNERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['vendors'] ?? 0 }} Partners</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">ADMINISTRATORS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $stats['admins'] ?? 0 }} Admins</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">SUSPENDED / BANNED</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['banned'] ?? 0 }} Accounts</p>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-users me-2 text-primary"></i> User Directory &amp; Permissions Control ({{ count($users) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search users..." onkeyup="filterTableSearch('usersTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('usersTable', this)" title="Select All Rows"></th>
                        <th>User &amp; Auth Provider</th>
                        <th>Phone &amp; Contact</th>
                        <th>Role Privilege</th>
                        <th>Bookings / Total Spent</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                @if($u->avatar)
                                    <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover; border:1px solid #cbd5e1;">
                                @else
                                    <div class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: var(--primary); font-size: 14px;">
                                        {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $u->name }}</strong>
                                    <span style="font-size:11px; color:#64748b;">{{ $u->email }}</span>
                                    @if($u->google_id || (isset($u->socialAccounts) && $u->socialAccounts->contains('provider', 'google')))
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold ms-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-brands fa-google me-1"></i> Google OAuth
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
                            @if(in_array($u->role, ['admin', 'super_admin']))
                                <span class="badge bg-purple text-white" style="background:#7367f0; font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">ADMIN</span>
                            @elseif($u->role === 'vendor')
                                <span class="badge bg-success text-white" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">VENDOR</span>
                            @else
                                <span class="badge bg-light text-secondary border" style="font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">CUSTOMER</span>
                            @endif
                        </td>
                        <td>
                            <strong class="d-block text-dark" style="font-size: 13px;">{{ $u->bookings_count ?? 0 }} Bookings</strong>
                            <small class="text-success fw-bold" style="font-size: 11.5px;">৳ {{ number_format($u->bookings ? $u->bookings->sum('total_price') : 0) }} BDT</small>
                        </td>
                        <td style="font-size:12px; color:#64748b;">
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
                                            <i class="fa-solid fa-eye text-primary me-2"></i> View Profile &amp; History
                                        </a>
                                    </li>
                                    @if($u->role === 'customer')
                                    <li>
                                        <form action="{{ route('admin.users.promote', $u->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-info">
                                                <i class="fa-solid fa-user-gear me-2"></i> Promote to Vendor
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        @if($u->status == 'banned')
                                        <form action="{{ route('admin.users.activate', $u->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-success">
                                                <i class="fa-solid fa-user-check me-2"></i> Activate Account
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.users.ban', $u->id) }}" method="POST" class="m-0" onsubmit="return confirm('Ban user {{ $u->name }}?')">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-ban me-2"></i> Ban Account
                                            </button>
                                        </form>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:#8c8c8c;">
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

{{-- CREATE USER MODAL --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Add New User Account
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Full Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Shawon Howlader" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Email Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. user@primebooking.com.bd" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Password <span style="color:#ff4d4f;">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Confirm Password <span style="color:#ff4d4f;">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Role Privilege</label>
                            <select name="role" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="customer">Guest / Customer</option>
                                <option value="vendor">Vendor Partner</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. 01711223344" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Account <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
