@extends('layouts.vendor')
@section('title', 'Guest Inquiries | Vendor Portal')
@section('content')
<div class="page-header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;"><h1 class="page-title m-0">Guest Inquiries & Pending Requests</h1></div>
    <div class="page-breadcrumb mt-2"><a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a><span class="sep">-</span><strong>Guest Inquiries</strong></div>
</div>
<div class="page-content-area">
    @if(session('success'))
        <div class="admin-alert success mb-3"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
    @endif
    <div class="stockifly-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="stockifly-table-head">
                    <tr><th>#</th><th>Guest</th><th>Property</th><th>Check-In</th><th>Check-Out</th><th>Amount</th><th>Status</th><th style="text-align:right;">Actions</th></tr>
                </thead>
                <tbody>
                @forelse($inquiries as $inquiry)
                <tr>
                    <td style="font-size:12px;color:#64748b;">{{ $loop->iteration }}</td>
                    <td><div class="fw-bold" style="font-size:12.5px;">{{ $inquiry->guest_name ?? 'Guest' }}</div><div style="font-size:11px;color:#64748b;">{{ $inquiry->guest_email ?? '' }}</div></td>
                    <td style="font-size:12.5px;">{{ $inquiry->property->name ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $inquiry->check_in ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $inquiry->check_out ?? '—' }}</td>
                    <td style="font-size:12.5px;font-weight:600;">৳{{ number_format($inquiry->total_amount ?? $inquiry->total_price ?? 0) }}</td>
                    <td><span class="badge-status pending">Pending</span></td>
                    <td style="text-align:right;">
                        <form action="{{ route('vendor.inquiries.reply', $inquiry->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="reply" value="Thank you for your inquiry. We will confirm your booking shortly.">
                            <button type="submit" class="btn-table-action primary" style="font-size:11.5px;"><i class="fa-solid fa-reply me-1"></i>Reply</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5" style="color:#94a3b8;font-size:13px;"><i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>No pending inquiries</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($inquiries, 'hasPages') && $inquiries->hasPages())
            <div class="stockifly-table-footer">{{ $inquiries->links() }}</div>
        @endif
    </div>
</div>
@endsection
