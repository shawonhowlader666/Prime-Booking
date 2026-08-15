@extends('layouts.vendor')
@section('title', 'Room Rates & Availability Calendar — Vendor Partner')

@php use App\Services\CurrencyService; @endphp

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Rates &amp; Availability Calendar</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('calendarTable')" title="Copy Table"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('calendarTable', 'Room_Availability')" title="Export Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('calendarTable', 'Room_Availability')" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="printTable('calendarTable')" title="Print PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('calendarTable')" title="Print Grid"><i class="fa-solid fa-print"></i> Print</button>
            @if($selectedRoom)
                <button type="button" class="btn-add-primary ms-1" data-bs-toggle="modal" data-bs-target="#batchUpdateModal">
                    <i class="fa-solid fa-sliders me-1"></i> Update Date Range Rates
                </button>
            @endif
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Inventory</span>
        <span class="sep">-</span><strong style="color:#333;">Rates &amp; Calendar</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if($selectedRoom)

    {{-- KPI SUMMARY ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">SELECTED ROOM &amp; BASE RATE</p>
                        <p class="kpi-value" style="font-size:18px; font-weight:800; color:#1e293b; margin:0;">{{ Str::limit($selectedRoom->name, 22) }}</p>
                        <span style="font-size:12px; color:#2067e1; font-weight:700;">৳ {{ number_format($selectedRoom->price_per_night) }} / night</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-bed"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">AVAILABLE BOOKABLE DAYS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['available_days'] }} / {{ $daysCount }} Days</p>
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
                        <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">SOLD OUT / BLOCKED</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['sold_out_days'] }} Days</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff5f5; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">SEASONAL PRICED DAYS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $stats['custom_price_days'] }} Days</p>
                        <span style="font-size:11.5px; color:#64748b;">Avg: ৳ {{ number_format($stats['avg_price']) }}/night</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f0eefc; color:#7367f0; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- STOCKIFLY FILTER BAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <form method="GET" action="{{ route('vendor.availability.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <label class="form-label mb-1 fw-bold text-secondary" style="font-size:11.5px;">Select Property &amp; Room</label>
                <select name="room_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 13px; font-weight:600;">
                    @foreach($properties as $p)
                        <optgroup label="🏢 {{ $p->name }} ({{ $p->city }})">
                            @foreach($p->rooms as $r)
                                <option value="{{ $r->id }}" {{ $selectedRoom->id === $r->id ? 'selected' : '' }}>
                                    🛏️ {{ $r->name }} — Base Rate: BDT ৳{{ number_format($r->price_per_night) }}/night
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 fw-bold text-secondary" style="font-size:11.5px;">View Forecast Range</label>
                <select name="days" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 13px;">
                    <option value="14" {{ $daysCount == 14 ? 'selected' : '' }}>Next 14 Days (2 Weeks)</option>
                    <option value="30" {{ $daysCount == 30 ? 'selected' : '' }}>Next 30 Days (1 Month)</option>
                    <option value="60" {{ $daysCount == 60 ? 'selected' : '' }}>Next 60 Days (2 Months)</option>
                    <option value="90" {{ $daysCount == 90 ? 'selected' : '' }}>Next 90 Days (3 Months)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2 pt-3">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="background-color: #2067e1; font-size: 12.5px; height:32px;">
                    <i class="fa-solid fa-filter me-1"></i> Apply Filter
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#batchUpdateModal" style="height:32px; font-size:12px; white-space:nowrap;">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Rates
                </button>
            </div>
        </form>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="row g-4 mb-4">

        {{-- Left: Batch Update Controller Card --}}
        <div class="col-12 col-lg-4">
            <div class="data-table-card p-3" style="border-radius:8px;">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark m-0"><i class="fa-solid fa-sliders me-1.5 text-primary"></i> Quick Rate &amp; Availability Update</h6>
                    <small class="text-muted" style="font-size:11.5px;">Update seasonal rates for <strong>{{ $selectedRoom->name }}</strong></small>
                </div>

                <form action="{{ route('vendor.availability.update-range') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                    <div class="mb-2.5">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Start Date <span style="color:#ff4d4f;">*</span></label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}" style="font-size:12.5px;">
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">End Date <span style="color:#ff4d4f;">*</span></label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required min="{{ date('Y-m-d') }}" style="font-size:12.5px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Custom Nightly Rate (BDT ৳)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="price" class="form-control" placeholder="Base: {{ (int)$selectedRoom->price_per_night }}" step="0.01" style="font-size:12.5px;">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:11px;">Leave empty to keep standard base price (৳{{ number_format($selectedRoom->price_per_night) }}).</small>
                    </div>

                    <div class="mb-3.5 p-2.5 rounded bg-light border">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="is_blocked" value="1" id="blockRoomCheck">
                            <label class="form-check-label fw-bold text-danger ms-2" for="blockRoomCheck" style="font-size:12px;">
                                <i class="fa-solid fa-ban me-1"></i> Mark as Sold Out / Block Room
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold py-2" style="background-color: #2067e1; font-size:13px; border-radius:6px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Calendar Rates
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: 30-90 Day Data Table Grid --}}
        <div class="col-12 col-lg-8">
            <div class="data-table-card p-0">
                <div class="saas-table-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-calendar-days me-1 text-primary"></i> Daily Rates &amp; Inventory Forecast ({{ $daysCount }} Days)</h6>
                    </div>
                    <div style="width:200px;">
                        <input type="text" class="form-control form-control-sm" placeholder="Search date..." onkeyup="filterTableSearch('calendarTable', this.value)" style="font-size:12px;">
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                    <table class="table stockifly-data-table align-middle mb-0" id="calendarTable">
                        <thead class="sticky-top bg-white" style="z-index: 10;">
                            <tr>
                                <th>Date &amp; Day</th>
                                <th>Base Rate</th>
                                <th>Custom Seasonal Rate</th>
                                <th>Status</th>
                                <th style="text-align:right;">Quick Update</th>
                            </tr>
                        </thead>
                        <tbody>
                        @for($d = 0; $d < $daysCount; $d++)
                            @php
                                $currentDateObj = $startDate->copy()->addDays($d);
                                $dateStr        = $currentDateObj->format('Y-m-d');
                                $record         = $availabilities->get($dateStr);
                                $isBlocked      = $record ? (bool)$record->is_blocked : false;
                                $hasCustomPrice = $record && $record->price && (float)$record->price !== (float)$selectedRoom->price_per_night;
                                $effectivePrice = $record && $record->price ? (float)$record->price : (float)$selectedRoom->price_per_night;
                            @endphp
                            <tr style="background-color: {{ $isBlocked ? '#fff5f5' : ($hasCustomPrice ? '#f0f7ff' : 'transparent') }};">
                                <td>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $currentDateObj->format('D, M d, Y') }}</strong>
                                    <span style="font-size:10.5px; color:#64748b;">{{ $currentDateObj->isToday() ? 'Today' : ($currentDateObj->isWeekend() ? 'Weekend' : 'Weekday') }}</span>
                                </td>
                                <td><span style="font-size:12.5px; color:#64748b;">BDT ৳{{ number_format($selectedRoom->price_per_night) }}</span></td>
                                <td>
                                    @if($hasCustomPrice)
                                        <strong style="color:#7367f0; font-size:13px;">BDT ৳{{ number_format($effectivePrice) }}</strong>
                                        <span class="badge bg-purple text-white ms-1" style="font-size:10px; background:#7367f0;">Custom</span>
                                    @else
                                        <span style="font-size:12px; color:#94a3b8;">Standard Rate</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isBlocked)
                                        <span class="badge-status cancelled" style="background:#fff2f0; color:#ff4d4f; border:1px solid #ffccc7; font-weight:700;">
                                            <i class="fa-solid fa-ban me-1"></i> Sold Out / Blocked
                                        </span>
                                    @else
                                        <span class="badge-status active">
                                            <i class="fa-solid fa-circle-check me-1"></i> Available (৳{{ number_format($effectivePrice) }})
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('vendor.availability.update-range') }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                                        <input type="hidden" name="start_date" value="{{ $dateStr }}">
                                        <input type="hidden" name="end_date" value="{{ $dateStr }}">
                                        @if($isBlocked)
                                            <input type="hidden" name="is_blocked" value="0">
                                            <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-2 py-1" style="font-size:11px; border-radius:4px;" title="Unblock Date">
                                                <i class="fa-solid fa-unlock me-1"></i> Unblock
                                            </button>
                                        @else
                                            <input type="hidden" name="is_blocked" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-2 py-1" style="font-size:11px; border-radius:4px;" title="Mark Sold Out">
                                                <i class="fa-solid fa-ban me-1"></i> Block
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- BATCH UPDATE MODAL --}}
    <div class="modal fade text-start" id="batchUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:8px;">
                <form action="{{ route('vendor.availability.update-range') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                    <div class="modal-header border-bottom py-2.5 px-3">
                        <h6 class="modal-title fw-bold text-primary"><i class="fa-solid fa-sliders me-1"></i> Bulk Update Rates &amp; Availability</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <p class="mb-2 text-dark font-size-13">Update availability calendar for <strong>{{ $selectedRoom->name }}</strong>:</p>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-2.5">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Custom Price (BDT ৳)</label>
                            <input type="number" name="price" class="form-control form-control-sm" placeholder="Base rate: {{ (int)$selectedRoom->price_per_night }}" step="0.01">
                        </div>

                        <div class="form-check form-switch p-2 bg-light rounded border m-0">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="is_blocked" value="1" id="modalBlockCheck">
                            <label class="form-check-label fw-bold text-danger" for="modalBlockCheck" style="font-size:12px;">
                                Mark as Sold Out / Block Room
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-3">
                        <button type="button" class="btn btn-light btn-sm text-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-check me-1"></i> Apply Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @else

    {{-- EMPTY STATE CARD (Admin Style) --}}
    <div class="data-table-card p-5 text-center" style="border-radius:8px; background:#ffffff;">
        <div style="max-width:420px; margin:0 auto;">
            <div style="width:72px; height:72px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin-bottom:16px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                <i class="fa-solid fa-bed"></i>
            </div>
            <h5 style="font-weight:700; color:#1e293b; margin-bottom:6px;">No Rooms Registered Yet</h5>
            <p style="font-size:13px; color:#64748b; margin-bottom:20px;">You need to add at least one property and room to manage nightly availability rates and calendar forecasts.</p>
            <a href="{{ route('vendor.properties.create') }}" class="btn btn-primary fw-bold px-4 py-2" style="background-color: #2067e1; border-radius:4px;">
                <i class="fa-solid fa-plus me-1"></i> Add Property &amp; Rooms Now
            </a>
        </div>
    </div>

    @endif

</div>
@endsection
