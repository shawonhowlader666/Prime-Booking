@extends('layouts.admin')
@section('title', 'Activity Log & Audit Trail | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Activity Log &amp; Audit Trail</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('activityLogsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('activityLogsTable', 'Activity_Logs')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('activityLogsTable', 'Activity_Logs')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('activityLogsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('activityLogsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <form action="{{ route('admin.activity.clear') }}" method="POST" class="m-0" onsubmit="return confirm('Clear all logs older than 90 days?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold" style="height:36px; border-radius:4px; font-size:12.5px; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-broom"></i> <span>Clear Old Logs (>90 days)</span>
                </button>
            </form>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>System Audit</span>
        <span class="sep">-</span><strong style="color:#333;">Activity Log</strong>
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
        $totalLogs = $logs->total();
        $createCount = \App\Models\ActivityLog::where('action', 'created')->count();
        $updateCount = \App\Models\ActivityLog::where('action', 'updated')->count();
        $loginCount  = \App\Models\ActivityLog::whereIn('action', ['login', 'logout'])->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">TOTAL AUDIT EVENTS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $totalLogs }} Logs</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">CREATE ACTIONS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $createCount }} Events</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">UPDATE ACTIONS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $updateCount }} Events</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">SECURITY &amp; LOGINS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $loginCount }} Events</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0;">
            <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between; width:100%;">
                <div class="d-flex align-items:center gap-2 flex-wrap">
                    <h6 class="mb-0 fw-bold text-dark me-2" style="font-size:14px;"><i class="fa-solid fa-shield-halved me-1.5 text-primary"></i> System Activity Logs</h6>
                    <select name="action" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size:12.5px; border-radius:4px; height:34px; width:130px;">
                        <option value="">All Actions</option>
                        @foreach(['created','updated','deleted','login','logout'] as $a)
                            <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>
                    <select name="model_type" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size:12.5px; border-radius:4px; height:34px; width:130px;">
                        <option value="">All Models</option>
                        @foreach(['Property','Booking','User','Coupon','Review','FeaturedDestination'] as $m)
                            <option value="{{ $m }}" {{ request('model_type') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['action','model_type','search']))
                        <a href="{{ route('admin.activity.index') }}" class="btn btn-light border btn-sm" style="font-size:12px; height:34px; border-radius:4px;">
                            <i class="fa-solid fa-xmark me-1"></i> Clear
                        </a>
                    @endif
                </div>
                <div style="width:240px;">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search user or action..." value="{{ request('search') }}" onkeyup="filterTableSearch('activityLogsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="activityLogsTable">
                <thead>
                    <tr>
                        <th style="width:140px;">Timestamp</th>
                        <th>Admin / User</th>
                        <th>Action Type</th>
                        <th>Target Model</th>
                        <th>Description Detail</th>
                        <th>IP Address</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    @php
                        $colors = ['created'=>'#28c76f','updated'=>'#1890ff','deleted'=>'#ea5455','login'=>'#7367f0','logout'=>'#8c8c8c'];
                        $icons  = ['created'=>'fa-plus-circle','updated'=>'fa-pen','deleted'=>'fa-trash','login'=>'fa-right-to-bracket','logout'=>'fa-right-from-bracket'];
                        $c = $colors[$log->action] ?? '#ff9f43';
                        $i = $icons[$log->action] ?? 'fa-circle-dot';
                    @endphp
                    <tr>
                        <td style="font-size:12px; color:#64748b; white-space:nowrap;">
                            <strong style="color:#0f172a; display:block;">{{ $log->created_at->format('d M Y') }}</strong>
                            <span style="font-size:11px;">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $log->user_name }}</strong>
                            @if($log->user)
                                <span style="font-size:11px; color:#64748b;">{{ $log->user->email }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $c }}15; color:{{ $c }}; border:1px solid {{ $c }}40; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px;">
                                <i class="fa-solid {{ $i }} me-1"></i> {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>
                            @if($log->model_type)
                                <span class="badge bg-light text-dark border" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">
                                    {{ $log->model_type }} #{{ $log->model_id }}
                                </span>
                            @else
                                <span style="font-size:11.5px; color:#94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td style="font-size:12.5px; color:#334155; max-width:280px;">
                            {{ Str::limit($log->description, 60) }}
                        </td>
                        <td>
                            <code style="font-size:11.5px; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px;">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </code>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#logDetailModal{{ $log->id }}">
                                            <i class="fa-solid fa-eye text-info me-2"></i> View Audit Detail
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.activity.destroy', $log->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this activity log entry?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Log Entry
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- LOG DETAIL MODAL --}}
                    <div class="modal fade" id="logDetailModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                        <i class="fa-solid fa-shield-halved text-primary me-2"></i> Audit Event #{{ $log->id }}
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding:20px;">
                                    <div class="mb-3 p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <span style="font-size:11px; color:#64748b; font-weight:600; display:block;">USER / OPERATOR</span>
                                                <strong style="font-size:13px; color:#0f172a;">{{ $log->user_name }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <span style="font-size:11px; color:#64748b; font-weight:600; display:block;">ACTION TYPE</span>
                                                <span class="badge" style="background:{{ $c }}15; color:{{ $c }}; font-size:11px; font-weight:700;">{{ ucfirst($log->action) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:4px;">Full Action Description</label>
                                        <div class="p-3 rounded" style="background:#f1f5f9; font-size:13px; color:#0f172a; border:1px solid #cbd5e1;">
                                            {{ $log->description }}
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label" style="font-size:11.5px; font-weight:600; color:#64748b; margin-bottom:2px;">Target Model</label>
                                            <p class="mb-0 fw-bold" style="font-size:12.5px; color:#0f172a;">{{ $log->model_type ?? 'N/A' }} #{{ $log->model_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label" style="font-size:11.5px; font-weight:600; color:#64748b; margin-bottom:2px;">IP Address</label>
                                            <p class="mb-0 fw-bold font-monospace" style="font-size:12.5px; color:#0f172a;">{{ $log->ip_address ?? '127.0.0.1' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Activity Logs Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:0;">System audit events will be automatically recorded here.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$logs" :perPage="50" />
    </div>

</div>

@endsection
