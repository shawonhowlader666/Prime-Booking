@extends('layouts.admin')
@section('title', 'Support Tickets | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0"><i class="fa-solid fa-headset me-2 text-primary"></i>Support Tickets</h1>
        <div style="font-size:12px; color:#64748b;">Vendor & Partner Help Desk</div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Support Tickets</strong>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">TOTAL TICKETS</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] }}</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">OPEN</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['open'] }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">IN PROGRESS</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#1890ff; margin:0;">{{ $stats['in_progress'] }}</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">RESOLVED</p>
                <p class="kpi-value" style="font-size:22px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['resolved'] }}</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#fff;">
        {{-- Toolbar --}}
        <div class="saas-table-toolbar" style="padding:14px 20px; border-bottom:1px solid #e2e8f0;">
            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; width:100%;">
                <h6 class="mb-0 fw-bold text-dark me-2" style="font-size:13.5px; flex-shrink:0;">
                    <i class="fa-solid fa-ticket me-1 text-primary"></i> All Tickets
                </h6>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size:12px; border-radius:4px; height:32px; width:130px;">
                    <option value="">All Status</option>
                    @foreach(['open','in_progress','resolved','closed'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size:12px; border-radius:4px; height:32px; width:120px;">
                    <option value="">All Priority</option>
                    @foreach(['urgent','high','medium','low'] as $p)
                        <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
                <select name="source" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size:12px; border-radius:4px; height:32px; width:110px;">
                    <option value="">All Source</option>
                    <option value="vendor" {{ request('source')=='vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="user"   {{ request('source')=='user'   ? 'selected' : '' }}>User</option>
                </select>
                @if(request()->hasAny(['status','priority','source','search']))
                    <a href="{{ route('admin.support.index') }}" class="btn btn-light border btn-sm" style="font-size:12px; height:32px; border-radius:4px;">
                        <i class="fa-solid fa-xmark me-1"></i>Clear
                    </a>
                @endif
                <div class="ms-auto" style="width:220px;">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search tickets..." value="{{ request('search') }}" style="font-size:12px; border-radius:4px; height:32px;">
                </div>
            </form>
        </div>

        <div class="table-responsive" style="overflow: visible !important;">
            <table class="table table-stockifly mb-0" id="supportTicketsTable">
                <thead>
                    <tr>
                        <th style="width:130px;">Ticket #</th>
                        <th>Vendor / User</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th style="width:80px;">Priority</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:70px;">Replies</th>
                        <th style="width:110px;">Submitted</th>
                        <th style="text-align:right; width:80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    @php
                        $priorityColors = ['urgent'=>'#ea5455','high'=>'#ff9f43','medium'=>'#1890ff','low'=>'#28c76f'];
                        $pc = $priorityColors[$ticket->priority] ?? '#94a3b8';
                    @endphp
                    <tr>
                        <td>
                            <code style="font-size:11.5px; background:#f1f5f9; color:#475569; padding:2px 7px; border-radius:4px;">{{ $ticket->ticket_number }}</code>
                        </td>
                        <td>
                            <strong style="font-size:13px; color:#0f172a; display:block;">{{ $ticket->user->name ?? 'N/A' }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $ticket->user->email ?? '' }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.support.show', $ticket->id) }}" style="font-size:13px; color:#1890ff; font-weight:600; text-decoration:none;">
                                {{ Str::limit($ticket->subject, 50) }}
                            </a>
                        </td>
                        <td>
                            <span style="font-size:12px; color:#475569; background:#f1f5f9; padding:2px 8px; border-radius:20px;">{{ $ticket->category }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $pc }}15; color:{{ $pc }}; border:1px solid {{ $pc }}40; font-size:10.5px; font-weight:700; padding:3px 9px; border-radius:20px;">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->statusBadgeClass() }}" style="font-size:10.5px; font-weight:700; padding:3px 9px; border-radius:4px;">
                                {{ ucwords(str_replace('_',' ',$ticket->status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:11px;">{{ $ticket->replies_count }}</span>
                        </td>
                        <td>
                            <span style="font-size:12px; color:#64748b;">{{ $ticket->created_at->format('d M Y') }}</span><br>
                            <span style="font-size:11px; color:#94a3b8;">{{ $ticket->created_at->format('H:i') }}</span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.support.show', $ticket->id) }}">
                                            <i class="fa-solid fa-eye text-info me-2"></i> View & Reply
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-success" onclick="return confirm('Mark as resolved?')">
                                                <i class="fa-solid fa-circle-check me-2"></i> Mark Resolved
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this ticket permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Ticket
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5" style="background:#fff;">
                            <div style="max-width:320px; margin:0 auto; padding:24px 0;">
                                <div style="width:64px; height:64px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:28px; margin-bottom:12px; border:1px solid #e2e8f0;">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Support Tickets</h6>
                                <p style="font-size:12px; color:#64748b; margin:0;">All vendor support requests will appear here.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$tickets" :perPage="20" />
    </div>

</div>
@endsection
