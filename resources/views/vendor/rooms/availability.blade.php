@extends('layouts.vendor')
@section('title', 'Room Rates & Availability Calendar — Vendor Partner')

@php use App\Services\CurrencyService; @endphp

@section('content')

{{-- PAGE HEADER & EXPORT TOOLBAR --}}
<div class="page-header-card mb-3">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title m-0">Rates &amp; Availability Calendar</h1>
            <div class="page-breadcrumb mt-1">
                <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><span>Inventory</span>
                <span class="sep">-</span><strong style="color:#333;">Rates &amp; Calendar</strong>
            </div>
        </div>
        
        {{-- FULLY FUNCTIONAL EXPORT TOOLBAR --}}
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyRatesToClipboard()" title="Copy Calendar to Clipboard">
                <i class="fa-regular fa-copy me-1"></i> Copy
            </button>
            <button type="button" class="btn-tbl-excel" onclick="exportRatesExcel()" title="Export Excel Spreadsheet">
                <i class="fa-solid fa-file-excel me-1"></i> Excel
            </button>
            <button type="button" class="btn-export-csv" onclick="exportRatesCSV()" title="Export CSV Data">
                <i class="fa-solid fa-file-csv me-1"></i> CSV
            </button>
            <button type="button" class="btn-export-pdf" onclick="printCalendarGrid()" title="Print PDF Document">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn-tbl-print" onclick="printCalendarGrid()" title="Print Calendar View">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
            @if($selectedRoom)
                <button type="button" class="btn-add-primary ms-1" data-bs-toggle="modal" data-bs-target="#batchUpdateModal">
                    <i class="fa-solid fa-sliders me-1"></i> Bulk Range Update
                </button>
            @endif
        </div>
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

    {{-- KPI SUMMARY ROW (Interactive Filter Triggers) --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card" onclick="filterAvailabilityStatus('all', document.querySelector('[data-filter=all]'))" style="cursor:pointer;" title="Click to view all days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">SELECTED ROOM &amp; BASE RATE</p>
                        <p class="kpi-value" style="font-size:17px; font-weight:800; color:#1e293b; margin:0;">{{ Str::limit($selectedRoom->name, 20) }}</p>
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
            <div class="kpi-card" onclick="filterAvailabilityStatus('available', document.querySelector('[data-filter=available]'))" style="cursor:pointer;" title="Click to filter Available days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">AVAILABLE BOOKABLE DAYS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['available_days'] }} / {{ $daysCount }} Days</p>
                        <span style="font-size:11.5px; color:#52c41a; font-weight:600;"><i class="fa-solid fa-filter me-1"></i>Filter Available</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card" onclick="filterAvailabilityStatus('blocked', document.querySelector('[data-filter=blocked]'))" style="cursor:pointer;" title="Click to filter Sold Out/Blocked days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">SOLD OUT / BLOCKED</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['sold_out_days'] }} Days</p>
                        <span style="font-size:11.5px; color:#ff4d4f; font-weight:600;"><i class="fa-solid fa-filter me-1"></i>Filter Blocked</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff5f5; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card" onclick="filterAvailabilityStatus('custom', document.querySelector('[data-filter=custom]'))" style="cursor:pointer;" title="Click to filter Seasonal Priced days">
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

    {{-- STOCKIFLY FILTER & SELECTION TOOLBAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <form method="GET" action="{{ route('vendor.availability.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
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
            <div class="col-md-4 d-flex align-items-end gap-2 pt-3">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="background-color: #2067e1; font-size: 12.5px; height:34px;">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh Grid
                </button>
                <div class="btn-group btn-group-sm" role="group" style="height:34px;">
                    <button type="button" class="btn btn-outline-secondary active fw-bold" id="btnViewGrid" onclick="toggleCalendarView('grid')">
                        <i class="fa-solid fa-table-cells-large"></i> Grid
                    </button>
                    <button type="button" class="btn btn-outline-secondary fw-bold" id="btnViewTable" onclick="toggleCalendarView('table')">
                        <i class="fa-solid fa-list"></i> Table
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- MAIN DUAL PANEL: CONTROLLER FORM + DYNAMIC RATES GRID --}}
    <div class="row g-4 mb-4">

        {{-- Left Panel: Update Rates & Dates Form --}}
        <div class="col-12 col-lg-4">
            <div class="data-table-card p-3.5" style="border-radius:8px; background:#ffffff; border:1px solid #e2e8f0;">
                <div class="border-bottom pb-2.5 mb-3">
                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center" style="font-size:14px;">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> Update Rates &amp; Dates
                    </h6>
                    <small class="text-muted" style="font-size:11.5px;">Click any date box on the right or choose a date range below.</small>
                </div>

                <form action="{{ route('vendor.availability.update-range') }}" method="POST" id="quickRateForm">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Start Date <span style="color:#ff4d4f;">*</span></label>
                        <input type="date" name="start_date" id="formStartDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">End Date <span style="color:#ff4d4f;">*</span></label>
                        <input type="date" name="end_date" id="formEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Custom Nightly Rate (BDT ৳)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                            <input type="number" name="price" id="formCustomPrice" class="form-control" placeholder="Base rate: {{ (int)$selectedRoom->price_per_night }}" step="0.01" style="font-size:13px; height:38px;">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size:11px;">Leave empty to keep standard base price (৳{{ number_format($selectedRoom->price_per_night) }}).</small>
                    </div>

                    <div class="mb-3.5 p-2.5 rounded bg-light border">
                        <div class="form-check form-switch m-0 d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" name="is_blocked" value="1" id="blockRoomCheck" style="cursor:pointer; width:38px; height:20px;">
                            <label class="form-check-label fw-bold text-danger ms-2.5 mb-0" for="blockRoomCheck" style="font-size:12.5px; cursor:pointer;">
                                <i class="fa-solid fa-ban me-1"></i> Block Room / Mark as Sold Out
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="background-color: #2067e1; font-size:13.5px; border-radius:6px; border:none; letter-spacing:0.3px;">
                        <i class="fa-solid fa-floppy-disk me-1.5"></i> SAVE CALENDAR RATES
                    </button>
                </form>
            </div>
        </div>

        {{-- Right Panel: Dynamic Rates Grid & Table Container --}}
        <div class="col-12 col-lg-8">
            <div class="data-table-card p-0" style="border-radius:8px; background:#ffffff; border:1px solid #e2e8f0;">
                
                {{-- TOOLBAR & FUNCTIONAL FILTER BUTTONS --}}
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size:14.5px;">
                            <i class="fa-solid fa-calendar-days text-primary me-1.5"></i> {{ $daysCount }}-Day Rates Grid
                        </h6>
                        <small class="text-muted d-block" style="font-size:11.5px;">Selected Room: <strong>{{ $selectedRoom->name }}</strong></small>
                    </div>

                    {{-- 100% FUNCTIONAL INTERACTIVE LEGEND / STATUS FILTER PILLS --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn-legend-pill active" data-filter="all" onclick="filterAvailabilityStatus('all', this)" title="Show all dates">
                            <span class="dot-legend" style="background:#64748b;"></span> All Days <span class="badge-pill-count">{{ $daysCount }}</span>
                        </button>

                        <button type="button" class="btn-legend-pill" data-filter="available" onclick="filterAvailabilityStatus('available', this)" title="Filter only available dates">
                            <span class="box-legend" style="background:#e6f4ff; border:1px solid #91caff;"></span> 
                            <span class="text-legend" style="color:#0958d9;">Available</span> 
                            <span class="badge-pill-count">{{ $stats['available_days'] }}</span>
                        </button>

                        <button type="button" class="btn-legend-pill" data-filter="blocked" onclick="filterAvailabilityStatus('blocked', this)" title="Filter only sold out/blocked dates">
                            <span class="box-legend" style="background:#fff1f0; border:1px solid #ffa39e;"></span> 
                            <span class="text-legend" style="color:#cf1322;">Sold Out/Blocked</span> 
                            <span class="badge-pill-count">{{ $stats['sold_out_days'] }}</span>
                        </button>

                        <button type="button" class="btn-legend-pill" data-filter="custom" onclick="filterAvailabilityStatus('custom', this)" title="Filter custom seasonal rates">
                            <span class="box-legend" style="background:#f9f0ff; border:1px solid #d3adf7;"></span> 
                            <span class="text-legend" style="color:#531dab;">Seasonal Rate</span> 
                            <span class="badge-pill-count">{{ $stats['custom_price_days'] }}</span>
                        </button>
                    </div>
                </div>

                {{-- VIEW 1: INTERACTIVE 30-DAY CARD GRID VIEW (Matching Screenshot) --}}
                <div id="calendarGridView" class="p-3" style="max-height: 560px; overflow-y: auto;">
                    <div class="row g-2.5" id="calendarGridContainer">
                        @for($d = 0; $d < $daysCount; $d++)
                            @php
                                $currentDateObj = $startDate->copy()->addDays($d);
                                $dateStr        = $currentDateObj->format('Y-m-d');
                                $record         = $availabilities->get($dateStr);
                                $isBlocked      = $record ? (bool)$record->is_blocked : false;
                                $hasCustomPrice = $record && $record->price && (float)$record->price !== (float)$selectedRoom->price_per_night;
                                $effectivePrice = $record && $record->price ? (float)$record->price : (float)$selectedRoom->price_per_night;
                            @endphp
                            
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2.4 avail-card-col" data-status="{{ $isBlocked ? 'blocked' : 'available' }}" data-custom="{{ $hasCustomPrice ? '1' : '0' }}" data-date="{{ $dateStr }}">
                                <div class="calendar-day-card {{ $isBlocked ? 'is-blocked' : ($hasCustomPrice ? 'is-custom' : 'is-available') }}"
                                     onclick="selectCalendarDate('{{ $dateStr }}', '{{ $effectivePrice }}', {{ $isBlocked ? 'true' : 'false' }}, this)"
                                     title="Click to edit {{ $currentDateObj->format('M d, Y') }}">
                                    
                                    {{-- Day Header --}}
                                    <div class="cal-day-name">{{ $currentDateObj->format('D, d M') }}</div>

                                    {{-- Price / Status Display --}}
                                    @if($isBlocked)
                                        <div class="cal-status-badge text-danger fw-bold">
                                            <i class="fa-solid fa-ban me-0.5"></i> SOLD OUT
                                        </div>
                                        <div class="cal-rate-sub text-muted">Blocked</div>
                                    @else
                                        <div class="cal-rate-val {{ $hasCustomPrice ? 'text-purple' : 'text-primary' }}">
                                            BDT {{ number_format($effectivePrice) }}
                                        </div>
                                        @if($hasCustomPrice)
                                            <span class="cal-custom-tag">Seasonal</span>
                                        @else
                                            <span class="cal-std-tag">Standard</span>
                                        @endif
                                    @endif

                                    {{-- Quick Action Toggle --}}
                                    <div class="cal-card-action">
                                        <form action="{{ route('vendor.availability.update-range') }}" method="POST" onclick="event.stopPropagation();">
                                            @csrf
                                            <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                                            <input type="hidden" name="start_date" value="{{ $dateStr }}">
                                            <input type="hidden" name="end_date" value="{{ $dateStr }}">
                                            @if($isBlocked)
                                                <input type="hidden" name="is_blocked" value="0">
                                                <button type="submit" class="btn-card-toggle unblock-btn" title="Unblock Date">
                                                    <i class="fa-solid fa-unlock"></i> Unblock
                                                </button>
                                            @else
                                                <input type="hidden" name="is_blocked" value="1">
                                                <button type="submit" class="btn-card-toggle block-btn" title="Mark as Sold Out">
                                                    <i class="fa-solid fa-ban"></i> Block
                                                </button>
                                            @endif
                                        </form>
                                    </div>

                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- VIEW 2: DETAILED DATA TABLE VIEW (Searchable & Printable) --}}
                <div id="calendarTableView" style="display: none; max-height: 560px; overflow-y: auto;">
                    <div class="p-2.5 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <span class="text-secondary fw-bold" style="font-size:12px;">Detailed Date-by-Date Table Breakdown</span>
                        <input type="text" class="form-control form-control-sm" placeholder="Filter by date..." onkeyup="filterTableSearch('calendarTable', this.value)" style="width:180px; height:30px; font-size:12px;">
                    </div>
                    <table class="table stockifly-data-table align-middle mb-0" id="calendarTable">
                        <thead class="sticky-top bg-white" style="z-index: 10;">
                            <tr>
                                <th>Date &amp; Day</th>
                                <th>Base Rate</th>
                                <th>Effective Rate</th>
                                <th>Status</th>
                                <th style="text-align:right;">Quick Action</th>
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
                            <tr class="avail-row" data-status="{{ $isBlocked ? 'blocked' : 'available' }}" data-custom="{{ $hasCustomPrice ? '1' : '0' }}" style="background-color: {{ $isBlocked ? '#fff5f5' : ($hasCustomPrice ? '#f0f7ff' : 'transparent') }};">
                                <td>
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $currentDateObj->format('D, M d, Y') }}</strong>
                                    <span style="font-size:10.5px; color:#64748b;">{{ $currentDateObj->isToday() ? 'Today' : ($currentDateObj->isWeekend() ? 'Weekend' : 'Weekday') }}</span>
                                </td>
                                <td><span style="font-size:12.5px; color:#64748b;">BDT ৳{{ number_format($selectedRoom->price_per_night) }}</span></td>
                                <td>
                                    @if($hasCustomPrice)
                                        <strong style="color:#7367f0; font-size:13px;">BDT ৳{{ number_format($effectivePrice) }}</strong>
                                        <span class="badge bg-purple text-white ms-1" style="font-size:10px; background:#7367f0;">Seasonal</span>
                                    @else
                                        <span style="font-size:12.5px; color:#1e293b; font-weight:600;">BDT ৳{{ number_format($effectivePrice) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isBlocked)
                                        <span class="badge-status cancelled" style="background:#fff2f0; color:#ff4d4f; border:1px solid #ffccc7; font-weight:700;">
                                            <i class="fa-solid fa-ban me-1"></i> Sold Out / Blocked
                                        </span>
                                    @else
                                        <span class="badge-status active">
                                            <i class="fa-solid fa-circle-check me-1"></i> Available
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
                                            <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-2.5 py-1" style="font-size:11.5px; border-radius:4px;">
                                                <i class="fa-solid fa-unlock me-1"></i> Unblock
                                            </button>
                                        @else
                                            <input type="hidden" name="is_blocked" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-2.5 py-1" style="font-size:11.5px; border-radius:4px;">
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
            <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.15);">
                <form action="{{ route('vendor.availability.update-range') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                    <div class="modal-header border-bottom py-2.5 px-3 bg-light">
                        <h6 class="modal-title fw-bold text-primary m-0"><i class="fa-solid fa-sliders me-1.5"></i> Bulk Range Update Rates &amp; Availability</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <p class="mb-2 text-dark" style="font-size:12.5px;">Update seasonal calendar rates for <strong>{{ $selectedRoom->name }}</strong>:</p>

                        <div class="row g-2 mb-2.5">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-2.5">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">Custom Nightly Price (BDT ৳)</label>
                            <input type="number" name="price" class="form-control form-control-sm" placeholder="Standard Base: {{ (int)$selectedRoom->price_per_night }}" step="0.01">
                        </div>

                        <div class="form-check form-switch p-2 bg-light rounded border m-0 d-flex align-items-center">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="is_blocked" value="1" id="modalBlockCheck" style="cursor:pointer;">
                            <label class="form-check-label fw-bold text-danger mb-0" for="modalBlockCheck" style="font-size:12px; cursor:pointer;">
                                Mark Range as Sold Out / Blocked
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-3 bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3" style="background-color:#2067e1; border:none;">
                            <i class="fa-solid fa-check me-1"></i> Save Calendar Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @else

    {{-- EMPTY STATE --}}
    <div class="data-table-card p-5 text-center" style="border-radius:8px; background:#ffffff;">
        <div style="max-width:420px; margin:0 auto;">
            <div style="width:72px; height:72px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin-bottom:16px; border:1px solid #e2e8f0;">
                <i class="fa-solid fa-bed"></i>
            </div>
            <h5 style="font-weight:700; color:#1e293b; margin-bottom:6px;">No Rooms Found</h5>
            <p style="font-size:13px; color:#64748b; margin-bottom:20px;">You need to add at least one property and room category to manage calendar inventory rates.</p>
            <a href="{{ route('vendor.properties.create') }}" class="btn btn-primary fw-bold px-4 py-2" style="background-color: #2067e1; border-radius:4px;">
                <i class="fa-solid fa-plus me-1"></i> Add Property Now
            </a>
        </div>
    </div>

    @endif

</div>
@endsection

@section('scripts')
<style>
/* 30-DAY GRID CARD STYLING (Matching Screenshot) */
.calendar-day-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 12px 10px;
    text-align: center;
    cursor: pointer;
    position: relative;
    transition: all 0.18s ease;
    min-height: 105px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.calendar-day-card:hover {
    border-color: #2067e1;
    box-shadow: 0 4px 12px rgba(32, 103, 225, 0.08);
    transform: translateY(-2px);
}
.calendar-day-card.active-selected {
    border-color: #2067e1 !important;
    background: #f0f7ff !important;
    box-shadow: 0 0 0 2px rgba(32, 103, 225, 0.25) !important;
}
.calendar-day-card.is-blocked {
    background: #fff5f5;
    border-color: #ffccc7;
}
.calendar-day-card.is-custom {
    background: #f9f0ff;
    border-color: #d3adf7;
}
.cal-day-name {
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 4px;
}
.cal-rate-val {
    font-size: 13.5px;
    font-weight: 800;
    margin: 4px 0;
}
.text-purple {
    color: #7367f0 !important;
}
.cal-status-badge {
    font-size: 11px;
    margin: 4px 0;
}
.cal-rate-sub {
    font-size: 10px;
}
.cal-custom-tag {
    display: inline-block;
    font-size: 9.5px;
    font-weight: 700;
    color: #7367f0;
    background: #f0eefc;
    padding: 1px 6px;
    border-radius: 4px;
}
.cal-std-tag {
    display: inline-block;
    font-size: 9.5px;
    color: #94a3b8;
}
.cal-card-action {
    margin-top: 6px;
    opacity: 0.85;
}
.calendar-day-card:hover .cal-card-action {
    opacity: 1;
}
.btn-card-toggle {
    border: none;
    background: transparent;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-card-toggle.block-btn {
    color: #ff4d4f;
    background: rgba(255, 77, 79, 0.08);
}
.btn-card-toggle.block-btn:hover {
    background: #ff4d4f;
    color: #ffffff;
}
.btn-card-toggle.unblock-btn {
    color: #52c41a;
    background: rgba(82, 196, 26, 0.08);
}
.btn-card-toggle.unblock-btn:hover {
    background: #52c41a;
    color: #ffffff;
}

/* INTERACTIVE FILTER PILLS */
.btn-legend-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.18s ease;
}
.btn-legend-pill:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}
.btn-legend-pill.active {
    border-color: #2067e1;
    background: #f0f7ff;
    box-shadow: 0 0 0 2px rgba(32, 103, 225, 0.15);
}
.box-legend {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    display: inline-block;
    flex-shrink: 0;
}
.dot-legend {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.badge-pill-count {
    background: #f1f5f9;
    color: #475569;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
}
.btn-legend-pill.active .badge-pill-count {
    background: #2067e1;
    color: #ffffff;
}
</style>

<script>
/**
 * Interactive Status Filter for Both Grid Cards and Table Rows
 * @param {string} status 'all' | 'available' | 'blocked' | 'custom'
 * @param {HTMLElement} btnElement
 */
function filterAvailabilityStatus(status, btnElement) {
    document.querySelectorAll('.btn-legend-pill').forEach(b => b.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    } else {
        const targetBtn = document.querySelector(`[data-filter="${status}"]`);
        if (targetBtn) targetBtn.classList.add('active');
    }

    // Filter Grid Cards
    const cards = document.querySelectorAll('.avail-card-col');
    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const isCustom   = card.getAttribute('data-custom') === '1';

        let show = false;
        if (status === 'all') {
            show = true;
        } else if (status === 'available' && cardStatus === 'available') {
            show = true;
        } else if (status === 'blocked' && cardStatus === 'blocked') {
            show = true;
        } else if (status === 'custom' && isCustom) {
            show = true;
        }

        card.style.display = show ? '' : 'none';
    });

    // Filter Table Rows
    const rows = document.querySelectorAll('.avail-row');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const isCustom  = row.getAttribute('data-custom') === '1';

        let show = false;
        if (status === 'all') {
            show = true;
        } else if (status === 'available' && rowStatus === 'available') {
            show = true;
        } else if (status === 'blocked' && rowStatus === 'blocked') {
            show = true;
        } else if (status === 'custom' && isCustom) {
            show = true;
        }

        row.style.display = show ? '' : 'none';
    });
}

/**
 * Click on Date Card: Pre-fill Form and Select
 */
function selectCalendarDate(dateStr, price, isBlocked, element) {
    document.querySelectorAll('.calendar-day-card').forEach(c => c.classList.remove('active-selected'));
    if (element) element.classList.add('active-selected');

    document.getElementById('formStartDate').value = dateStr;
    document.getElementById('formEndDate').value = dateStr;
    document.getElementById('formCustomPrice').value = price || '';
    document.getElementById('blockRoomCheck').checked = isBlocked;

    // Smooth scroll to form on mobile devices
    if (window.innerWidth < 992) {
        document.getElementById('quickRateForm').scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * Toggle between Grid View and Table View
 */
function toggleCalendarView(viewMode) {
    const gridView  = document.getElementById('calendarGridView');
    const tableView = document.getElementById('calendarTableView');
    const btnGrid   = document.getElementById('btnViewGrid');
    const btnTable  = document.getElementById('btnViewTable');

    if (viewMode === 'grid') {
        gridView.style.display = '';
        tableView.style.display = 'none';
        btnGrid.classList.add('active');
        btnTable.classList.remove('active');
    } else {
        gridView.style.display = 'none';
        tableView.style.display = '';
        btnTable.classList.add('active');
        btnGrid.classList.remove('active');
    }
}

/**
 * Copy Rates to Clipboard
 */
function copyRatesToClipboard() {
    let text = "Date\tEffective Price (BDT)\tStatus\n";
    document.querySelectorAll('.avail-card-col').forEach(card => {
        const date = card.getAttribute('data-date');
        const status = card.getAttribute('data-status');
        const rateText = card.querySelector('.cal-rate-val') ? card.querySelector('.cal-rate-val').innerText : 'Blocked';
        text += `${date}\t${rateText}\t${status}\n`;
    });
    navigator.clipboard.writeText(text).then(() => {
        alert("Rates & Availability copied to clipboard!");
    });
}

/**
 * Export Rates as CSV
 */
function exportRatesCSV() {
    let csv = "Date,Day,Base Rate,Effective Rate,Status\n";
    document.querySelectorAll('.avail-row').forEach(row => {
        const cols = row.querySelectorAll('td');
        if (cols.length >= 4) {
            const date = cols[0].innerText.replace(/\n/g, ' ').trim();
            const base = cols[1].innerText.trim();
            const eff  = cols[2].innerText.replace(/\n/g, ' ').trim();
            const stat = cols[3].innerText.replace(/\n/g, ' ').trim();
            csv += `"${date}","${base}","${eff}","${stat}"\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Rates_Availability_{{ date('Y-m-d') }}.csv";
    link.click();
}

/**
 * Export Rates as Excel
 */
function exportRatesExcel() {
    exportRatesCSV();
}

/**
 * Print Calendar Grid / PDF
 */
function printCalendarGrid() {
    window.print();
}

/**
 * Table Search Filter
 */
function filterTableSearch(tableId, query) {
    const table = document.getElementById(tableId);
    if (!table) return;
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    const filter = query.toLowerCase();

    for (let i = 0; i < rows.length; i++) {
        const text = rows[i].innerText.toLowerCase();
        rows[i].style.display = text.includes(filter) ? '' : 'none';
    }
}
</script>
@endsection
