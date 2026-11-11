@extends('layouts.admin')
@section('title', 'Ticket #{{ $ticket->ticket_number }} | Support | PRIME BOOKING Admin')

@section('content')

<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-ticket me-2 text-primary"></i>
            Ticket #{{ $ticket->ticket_number }}
        </h1>
        <a href="{{ route('admin.support.index') }}" class="btn btn-light border btn-sm fw-bold" style="font-size:12px; height:34px; border-radius:4px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to All Tickets
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
        <span class="sep">-</span>
        <a href="{{ route('admin.support.index') }}">Support</a>
        <span class="sep">-</span>
        <strong style="color:#333;">{{ $ticket->ticket_number }}</strong>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">

        {{-- LEFT: Ticket Thread ──────────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Ticket Info --}}
            <div class="data-table-card p-0 mb-3" style="border-radius:4px; border:1px solid #e2e8f0;">
                <div class="d-flex align-items-start gap-3 p-4" style="border-bottom:1px solid #e2e8f0;">
                    <div style="width:44px; height:44px; border-radius:50%; background:rgba(24,144,255,0.1); color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <strong style="font-size:14px; color:#0f172a;">{{ $ticket->user->name ?? 'Unknown' }}</strong>
                            <span style="font-size:11px; color:#64748b;">{{ $ticket->user->email ?? '' }}</span>
                            <span class="badge {{ $ticket->statusBadgeClass() }}" style="font-size:10.5px; padding:3px 9px; border-radius:4px; font-weight:700;">
                                {{ ucwords(str_replace('_',' ',$ticket->status)) }}
                            </span>
                            @php $pc = $ticket->priorityColor(); @endphp
                            <span class="badge" style="background:{{ $pc }}15; color:{{ $pc }}; border:1px solid {{ $pc }}40; font-size:10.5px; padding:3px 9px; border-radius:20px; font-weight:700;">
                                {{ ucfirst($ticket->priority) }} Priority
                            </span>
                        </div>
                        <h5 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">{{ $ticket->subject }}</h5>
                        <span style="font-size:11.5px; color:#64748b;">
                            <i class="fa-regular fa-folder me-1"></i>{{ $ticket->category }}
                            <span class="mx-2">·</span>
                            <i class="fa-regular fa-clock me-1"></i>{{ $ticket->created_at->format('d M Y, H:i') }}
                            ({{ $ticket->created_at->diffForHumans() }})
                        </span>
                    </div>
                </div>
                {{-- Original Message --}}
                <div class="p-4" style="background:#f8fafc; border-radius:0 0 4px 4px;">
                    <div style="font-size:12.5px; color:#475569; white-space:pre-wrap; line-height:1.7;">{{ $ticket->message }}</div>
                </div>
            </div>

            {{-- Reply Thread --}}
            @foreach($ticket->replies as $reply)
            <div class="data-table-card p-0 mb-2" style="border-radius:4px; border:1px solid {{ $reply->is_admin_reply ? '#e0f0ff' : '#e2e8f0' }}; background:{{ $reply->is_admin_reply ? '#f0f7ff' : '#fff' }};">
                <div class="d-flex align-items-start gap-3 p-3">
                    <div style="width:36px; height:36px; border-radius:50%; background:{{ $reply->is_admin_reply ? 'rgba(24,144,255,0.15)' : 'rgba(100,116,139,0.1)' }}; color:{{ $reply->is_admin_reply ? '#1890ff' : '#64748b' }}; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;">
                        <i class="fa-solid {{ $reply->is_admin_reply ? 'fa-shield-halved' : 'fa-user' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <strong style="font-size:12.5px; color:#0f172a;">{{ $reply->user->name ?? 'Admin' }}</strong>
                            @if($reply->is_admin_reply)
                                <span class="badge bg-primary text-white" style="font-size:10px; padding:2px 7px; border-radius:3px;">Admin Reply</span>
                            @else
                                <span class="badge bg-light text-dark border" style="font-size:10px; padding:2px 7px; border-radius:3px;">Vendor</span>
                            @endif
                            <span style="font-size:11px; color:#94a3b8; margin-left:auto;">{{ $reply->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div style="font-size:12.5px; color:#334155; white-space:pre-wrap; line-height:1.7;">{{ $reply->message }}</div>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Admin Reply Form --}}
            @if($ticket->status !== 'closed')
            <div class="data-table-card p-4" style="border-radius:4px; border:1px solid #e2e8f0; margin-top:8px;">
                <h6 class="fw-bold mb-3" style="font-size:13px; color:#0f172a;">
                    <i class="fa-solid fa-reply text-primary me-2"></i>Reply to Ticket
                </h6>
                <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="5" required placeholder="Type your reply to the vendor..." style="font-size:13px; border-radius:4px; border:1px solid #cbd5e1; padding:10px 14px; resize:vertical;"></textarea>
                        @error('message') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary fw-bold" style="height:36px; font-size:13px; border-radius:4px; background:var(--primary); border:none;">
                            <i class="fa-solid fa-paper-plane me-1"></i> Send Reply
                        </button>
                        <span style="font-size:11.5px; color:#94a3b8;">Reply will be visible to the vendor in their portal.</span>
                    </div>
                </form>
            </div>
            @else
            <div class="admin-alert" style="border-radius:4px; padding:12px 16px; background:#f8fafc; border:1px solid #e2e8f0; font-size:12.5px; color:#64748b; margin-top:8px;">
                <i class="fa-solid fa-lock me-2"></i> This ticket is <strong>closed</strong>. Reopen it to send a reply.
            </div>
            @endif

        </div>

        {{-- RIGHT: Ticket Details & Actions ─────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Status Control --}}
            <div class="data-table-card p-3 mb-3" style="border-radius:4px; border:1px solid #e2e8f0;">
                <div class="fw-bold mb-3 pb-2 border-bottom" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:var(--primary);">
                    <i class="fa-solid fa-sliders me-1"></i> Ticket Controls
                </div>
                <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label style="font-size:11px; font-weight:600; color:#475569; text-transform:uppercase; display:block; margin-bottom:5px;">Status</label>
                        <select name="status" class="form-select form-select-sm" style="font-size:12.5px; border-radius:4px;">
                            @foreach(['open','in_progress','resolved','closed'] as $s)
                                <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_',' ',$s)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label style="font-size:11px; font-weight:600; color:#475569; text-transform:uppercase; display:block; margin-bottom:5px;">Priority</label>
                        <select name="priority" class="form-select form-select-sm" style="font-size:12.5px; border-radius:4px;">
                            @foreach(['low','medium','high','urgent'] as $p)
                                <option value="{{ $p }}" {{ $ticket->priority === $p ? 'selected' : '' }}>
                                    {{ ucfirst($p) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm fw-bold w-100" style="font-size:12px; height:34px; border-radius:4px;">
                        <i class="fa-solid fa-save me-1"></i> Update Status
                    </button>
                </form>
            </div>

            {{-- Assign To --}}
            <div class="data-table-card p-3 mb-3" style="border-radius:4px; border:1px solid #e2e8f0;">
                <div class="fw-bold mb-3 pb-2 border-bottom" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:var(--primary);">
                    <i class="fa-solid fa-user-shield me-1"></i> Assign To
                </div>
                <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST">
                    @csrf
                    <select name="assigned_to" class="form-select form-select-sm mb-2" style="font-size:12.5px; border-radius:4px;">
                        <option value="">— Unassigned —</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold w-100" style="font-size:12px; height:34px; border-radius:4px;">
                        <i class="fa-solid fa-user-check me-1"></i> Assign
                    </button>
                </form>
            </div>

            {{-- Ticket Info --}}
            <div class="data-table-card p-3 mb-3" style="border-radius:4px; border:1px solid #e2e8f0;">
                <div class="fw-bold mb-3 pb-2 border-bottom" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:var(--primary);">
                    <i class="fa-solid fa-circle-info me-1"></i> Ticket Info
                </div>
                <div style="font-size:12.5px; display:flex; flex-direction:column; gap:8px;">
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Ticket #</span>
                        <code style="font-size:11.5px; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:3px;">{{ $ticket->ticket_number }}</code>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Category</span>
                        <strong style="color:#0f172a;">{{ $ticket->category }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Source</span>
                        <span class="badge bg-light text-dark border" style="font-size:10.5px;">{{ ucfirst($ticket->source) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Replies</span>
                        <strong style="color:#0f172a;">{{ $ticket->replies->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Submitted</span>
                        <span style="color:#0f172a;">{{ $ticket->created_at->format('d M Y') }}</span>
                    </div>
                    @if($ticket->resolved_at)
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Resolved</span>
                        <span style="color:#28c76f; font-weight:700;">{{ $ticket->resolved_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($ticket->assignedTo)
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Assigned To</span>
                        <strong style="color:#1890ff;">{{ $ticket->assignedTo->name }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="data-table-card p-3" style="border-radius:4px; border:1px solid #ffe0e0;">
                <div class="fw-bold mb-2 pb-2 border-bottom" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px; color:#ea5455;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Danger Zone
                </div>
                <form action="{{ route('admin.support.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Delete this ticket and all replies permanently?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold w-100" style="font-size:12px; height:34px; border-radius:4px;">
                        <i class="fa-solid fa-trash me-1"></i> Delete Ticket
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
