@extends('layouts.vendor')
@section('title', 'Notifications | Vendor Portal')
@section('content')
<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <h1 class="page-title m-0"><i class="fa-solid fa-bell me-2 text-warning"></i>Notifications Center</h1>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong>Notifications</strong>
    </div>
</div>
<div class="page-content-area">
    <div class="stockifly-card">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold" style="font-size:13px;">Recent Activity Alerts</span>
            <span class="badge-gateway">{{ ->count() }} items</span>
        </div>
        @forelse( as )
        <div class="d-flex align-items-start gap-3 p-3 border-bottom" style="transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
            <div style="width:38px;height:38px;border-radius:50%;background:rgba(40,199,111,0.1);color:#28c76f;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-calendar-check" style="font-size:14px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="font-size:12.5px;">New Booking — {{ ->property->name ?? 'Property' }}</div>
                <div style="font-size:12px;color:#64748b;">Ref: <strong>{{ ->booking_reference }}</strong> · {{ ->guest_name ?? 'Guest' }} · {{ ->check_in ?? '' }} → {{ ->check_out ?? '' }}</div>
            </div>
            <div style="font-size:11px;color:#94a3b8;white-space:nowrap;">{{ ->created_at?->diffForHumans() }}</div>
        </div>
        @empty
        <div class="text-center py-5" style="color:#94a3b8;font-size:13px;">
            <i class="fa-solid fa-bell-slash fa-2x mb-2 d-block"></i>No new notifications
        </div>
        @endforelse
    </div>
</div>
@endsection
