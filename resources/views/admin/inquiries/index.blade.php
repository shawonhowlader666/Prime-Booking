@extends('layouts.admin')
@section('title', 'Guest Inquiries & Messages — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Customer Inquiries &amp; Support Messages</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('inquiriesTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('inquiriesTable', 'inquiries')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('inquiriesTable', 'inquiries')" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('inquiriesTable', 'inquiries')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('inquiriesTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Support</span>
        <span class="sep">-</span><strong style="color:#333;">Inquiries &amp; Messages</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="page-filters-bar mb-3">
        <form method="GET" action="{{ route('admin.inquiries.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Service Category</label>
                    <select name="service_type" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ request('service_type') == 'all' ? 'selected' : '' }}>All Services</option>
                        <option value="Helicopter Charter" {{ request('service_type') == 'Helicopter Charter' ? 'selected' : '' }}>Helicopter Charter</option>
                        <option value="Air Ambulance" {{ request('service_type') == 'Air Ambulance' ? 'selected' : '' }}>Air Ambulance</option>
                        <option value="Hotel Booking" {{ request('service_type') == 'Hotel Booking' ? 'selected' : '' }}>Hotel Booking</option>
                        <option value="General Inquiry" {{ request('service_type') == 'General Inquiry' ? 'selected' : '' }}>General Support</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Status Filter</label>
                    <select name="status" class="form-select form-select-sm" style="height:32px; font-size:12px;" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Response</option>
                        <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded / Contacted</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label" style="font-size:11px; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase;">Search Guest or Message</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by guest name, phone, email, or destination..." style="height:32px; font-size:12px;">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn-add-primary flex-grow-1" style="height:32px; font-size:12px; justify-content:center;" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    <a href="{{ route('admin.inquiries.index') }}" class="btn-tbl-copy" style="height:32px; font-size:12px; display:inline-flex; align-items:center; justify-content:center; padding:0 10px;" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI SUMMARY ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL INQUIRIES</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] ?? count($inquiries) }} Received</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">PENDING ACTION</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['pending'] ?? 0 }} Pending</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">RESPONDED SUPPORT</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['responded'] ?? 0 }} Answered</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">CHARTER / AMBULANCE</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['emergency'] ?? 0 }} Urgent</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff5f5; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-truck-medical"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-comments me-1 text-primary"></i> Inbox &amp; Guest Booking Requests ({{ count($inquiries) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search inquiries..." onkeyup="filterTableSearch('inquiriesTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="inquiriesTable">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('inquiriesTable', this)" title="Select All Rows"></th>
                        <th>Guest Name &amp; Contact</th>
                        <th>Requested Service</th>
                        <th>Destination</th>
                        <th>Date &amp; Pax</th>
                        <th>Message Note</th>
                        <th>Submitted At</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($inquiries as $inq)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $inq->name }}</strong>
                            <span style="font-size:11px; color:#64748b;">
                                <i class="fa-solid fa-phone me-1"></i>{{ $inq->phone }} @if($inq->email)| <i class="fa-solid fa-envelope me-1"></i>{{ $inq->email }}@endif
                            </span>
                        </td>
                        <td>
                            @if(!empty($inq->property))
                                <span class="badge bg-light text-success border d-block mb-1" style="font-weight:700; font-size:11px; text-align:left;">
                                    <i class="fa-solid fa-hotel me-1"></i>{{ $inq->property->name }}
                                </span>
                            @endif
                            <span class="badge bg-light text-primary border" style="font-weight:700; font-size:11px;">
                                <i class="fa-solid fa-tag me-1"></i>{{ $inq->service_type ?? 'General' }}
                            </span>
                        </td>
                        <td style="font-size:12.5px; color:#334155; font-weight:600;">
                            {{ $inq->destination ?? 'General Inquiry' }}
                        </td>
                        <td style="font-size:12px; color:#595959;">
                            <div><i class="fa-solid fa-calendar me-1 text-secondary"></i>{{ $inq->travel_date ?? 'Flexible' }}</div>
                            <span class="badge bg-light text-secondary border mt-1" style="font-size:10px;">{{ $inq->passengers ?? 1 }} Pax</span>
                        </td>
                        <td style="font-size:12px; color:#475569; max-width:240px; white-space:normal;">
                            {{ Str::limit($inq->message ?? 'No details provided.', 85) }}
                        </td>
                        <td style="font-size:11.5px; color:#8c8c8c;">
                            {{ $inq->created_at ? (is_string($inq->created_at) ? $inq->created_at : $inq->created_at->format('M d, Y, h:i A')) : 'N/A' }}
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="tel:{{ $inq->phone }}">
                                            <i class="fa-solid fa-phone text-success me-2"></i> Call Guest ({{ $inq->phone }})
                                        </a>
                                    </li>
                                    @if($inq->email)
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="mailto:{{ $inq->email }}">
                                            <i class="fa-solid fa-envelope text-primary me-2"></i> Send Email
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="https://wa.me/88{{ preg_replace('/[^0-9]/', '', $inq->phone) }}" target="_blank">
                                            <i class="fa-brands fa-whatsapp text-success me-2"></i> WhatsApp Message
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
                        <td colspan="8" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-envelope-open"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Inquiries Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no guest messages matching your criteria in the database.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$inquiries" :perPage="20" />
    </div>

</div>
@endsection

