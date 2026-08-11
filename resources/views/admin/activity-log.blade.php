@extends('layouts.admin')
@section('title', 'Activity Log & Audit Trail | Admin')

@section('content')

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Activity Log</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title"><i class="fa-solid fa-shield-halved me-2" style="color:#7367f0;"></i>Admin Activity Log & Audit Trail</h1>
        <form action="{{ route('admin.activity.clear') }}" method="POST" onsubmit="return confirm('Clear all logs older than 90 days?')">
            @csrf
            <button type="submit" class="btn-export-csv" style="border-color:#ffccc7; color:#ff4d4f; background:#fff1f0;">
                <i class="fa-solid fa-broom"></i> Clear Old Logs (>90 days)
            </button>
        </form>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="page-filters-bar" style="margin-bottom:16px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div>
                <label class="form-label">Action Type</label>
                <select name="action" class="form-select" onchange="this.form.submit()" style="min-width:130px;">
                    <option value="">All Actions</option>
                    @foreach(['created','updated','deleted','login','logout'] as $a)
                        <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Model Type</label>
                <select name="model_type" class="form-select" onchange="this.form.submit()" style="min-width:130px;">
                    <option value="">All Models</option>
                    @foreach(['Property','Booking','User','Coupon','Review'] as $m)
                        <option value="{{ $m }}" {{ request('model_type') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Search</label>
                <div style="display:flex;">
                    <input type="text" name="search" class="form-control" placeholder="User name or action..." value="{{ request('search') }}" style="min-width:200px; border-radius:6px 0 0 6px; border-right:none;">
                    <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            @if(request()->hasAny(['action','model_type','search']))
            <a href="{{ route('admin.activity.index') }}" class="btn-export-csv" style="align-self:flex-end; border-color:#d9d9d9; color:#595959;">
                <i class="fa-solid fa-x"></i> Clear
            </a>
            @endif
        </form>
    </div>

    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Audit Trail — {{ $logs->total() }} events</h6>
            <span style="font-size:12px; color:#8c8c8c;">Last 50 shown per page</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Admin / User</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th style="text-align:right;">Del</th>
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
                        <td style="font-size:11.5px; color:#8c8c8c; white-space:nowrap;">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span style="font-size:10.5px;">{{ $log->created_at->format('h:i:s A') }}</span>
                        </td>
                        <td>
                            <strong style="font-size:12.5px; color:#1e293b;">{{ $log->user_name }}</strong>
                            @if($log->user)
                                <span style="display:block; font-size:10.5px; color:#8c8c8c;">{{ $log->user->email }}</span>
                            @endif
                        </td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:5px; background:{{ $c }}20; color:{{ $c }}; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; border:1px solid {{ $c }}40;">
                                <i class="fa-solid {{ $i }}"></i> {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td style="font-size:12px; color:#595959;">
                            @if($log->model_type)
                                <span style="background:#f0f0f0; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600;">{{ $log->model_type }}</span>
                                @if($log->model_id)
                                    <span style="font-size:11px; color:#8c8c8c; margin-left:4px;">#{{ $log->model_id }}</span>
                                @endif
                            @else
                                <span style="color:#d9d9d9;">—</span>
                            @endif
                        </td>
                        <td style="font-size:12.5px; color:#1e293b; max-width:280px;">
                            {{ Str::limit($log->description, 60) }}
                        </td>
                        <td style="font-size:11.5px; color:#8c8c8c; font-family:monospace;">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td style="text-align:right;">
                            <form action="{{ route('admin.activity.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Delete this log entry?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-table-action danger" style="padding:3px 8px; font-size:11px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:40px; color:#8c8c8c;">
                            <i class="fa-solid fa-clipboard-list" style="font-size:32px; color:#d9d9d9; display:block; margin-bottom:10px;"></i>
                            <strong style="display:block; font-size:14px; color:#1e293b; margin-bottom:6px;">No Activity Logs</strong>
                            <span style="font-size:12px;">Admin actions will be automatically logged here.</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div style="padding:12px 16px; border-top:1px solid #f0f0f0;">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
