@extends('layouts.admin')
@section('title', 'Guest Inquiries & Messages | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Support</span>
        <span class="sep">-</span><strong style="color:#333;">Inquiries &amp; Messages</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Customer Inquiries &amp; Support Messages</h1>
        <div style="display:flex; align-items:center; gap:8px;">
            <button class="btn-export-csv" onclick="alert('Exporting Inquiries CSV...')">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </button>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="page-filters-bar">
    <form method="GET" action="{{ route('admin.inquiries.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-md-6">
                <label class="form-label">Search Inquiries</label>
                <div style="display:flex;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, phone, or email..." style="border-radius:6px 0 0 6px; border-right:none;">
                    <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2 text-end">
                <a href="{{ route('admin.inquiries.index') }}" class="btn-table-action" style="padding: 6px 12px; height: 32px; display: inline-flex; align-items: center;">Reset</a>
            </div>
        </div>
    </form>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Main Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Inbox &amp; Booking Requests</h6>
            <span class="live-feed-badge">Live Support Feed</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Guest Name &amp; Contact</th>
                        <th>Requested Service</th>
                        <th>Destination</th>
                        <th>Date &amp; Passengers</th>
                        <th>Message Note</th>
                        <th>Submitted At</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($inquiries as $inq)
                    <tr>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $inq->name }}</strong>
                            <span style="font-size:11px; color:#8c8c8c;">{{ $inq->phone }} | {{ $inq->email ?? 'No Email' }}</span>
                        </td>
                        <td>
                            <span class="badge-gateway" style="color:var(--primary); font-weight:700;">
                                {{ $inq->service_type }}
                            </span>
                        </td>
                        <td style="font-size:12.5px; color:#334155;">{{ $inq->destination ?? 'General Inquiry' }}</td>
                        <td style="font-size:12px; color:#595959;">
                            {{ $inq->travel_date ?? 'Flexible' }} ({{ $inq->passengers ?? 1 }} pax)
                        </td>
                        <td style="font-size:12px; color:#595959; max-width:240px; white-space:normal;">
                            {{ Str::limit($inq->message ?? 'No details provided.', 80) }}
                        </td>
                        <td style="font-size:11.5px; color:#8c8c8c;">
                            {{ $inq->created_at ? (is_string($inq->created_at) ? $inq->created_at : $inq->created_at->format('M d, Y, h:i A')) : 'N/A' }}
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="tel:{{ $inq->phone }}">
                                            <i class="fa-solid fa-phone text-primary me-2"></i> Call Customer ({{ $inq->phone }})
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this inquiry message?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Inquiry
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
                            <i class="fa-solid fa-envelope-open" style="font-size:28px; color:#d9d9d9; display:block; margin-bottom:8px;"></i>
                            No inquiries found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:12px 16px; border-top:1px solid #f0f0f0; font-size:12px; color:#8c8c8c;">
            @if(method_exists($inquiries, 'links'))
                {{ $inquiries->links() }}
            @else
                Showing all received messages
            @endif
        </div>
    </div>

</div>
@endsection

