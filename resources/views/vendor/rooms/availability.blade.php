@extends('layouts.vendor')
@section('title', 'Room Rates & Availability Calendar — Vendor Partner')

@php use App\Services\CurrencyService; @endphp

@section('content')

{{-- PAGE HEADER & EXPORT TOOLBAR (Exact Stockifly Admin Attached Header) --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title m-0">Rates &amp; Availability</h1>
            <div class="page-breadcrumb mt-1.5">
                <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><span>Inventory</span>
                <span class="sep">-</span><strong style="color:#333;">Rates &amp; Availability</strong>
            </div>
        </div>
        
        {{-- FULLY FUNCTIONAL EXPORT TOOLBAR --}}
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn-tbl-copy" onclick="copyRatesToClipboard()" title="Copy to Clipboard">
                <i class="fa-regular fa-copy me-1"></i> Copy
            </button>
            <button type="button" class="btn-tbl-excel" onclick="exportRatesExcel()" title="Export Excel">
                <i class="fa-solid fa-file-excel me-1"></i> Excel
            </button>
            <button type="button" class="btn-export-csv" onclick="exportRatesCSV()" title="Export CSV">
                <i class="fa-solid fa-file-csv me-1"></i> CSV
            </button>
            <button type="button" class="btn-export-pdf" onclick="printCalendarGrid()" title="Print PDF">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn-tbl-print" onclick="printCalendarGrid()" title="Print View">
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
    <div class="row g-3" style="margin-bottom: 24px !important;">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card" onclick="filterAvailabilityStatus('all', document.querySelector('[data-filter=all]'))" style="cursor:pointer; border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);" title="Click to view all days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">SELECTED ROOM</p>
                        <p class="kpi-value" style="font-size:16px; font-weight:800; color:#1e293b; margin:0;">{{ Str::limit($selectedRoom->name, 22) }}</p>
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
            <div class="kpi-card" onclick="filterAvailabilityStatus('available', document.querySelector('[data-filter=available]'))" style="cursor:pointer; border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);" title="Click to filter Available days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">AVAILABLE DAYS</p>
                        <p class="kpi-value" style="font-size:19px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['available_days'] }} / {{ $daysCount }} Days</p>
                        <span style="font-size:11.5px; color:#52c41a; font-weight:600;">Bookable Inventory</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card" onclick="filterAvailabilityStatus('blocked', document.querySelector('[data-filter=blocked]'))" style="cursor:pointer; border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);" title="Click to filter Sold Out/Blocked days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">BLOCKED DAYS</p>
                        <p class="kpi-value" style="font-size:19px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['sold_out_days'] }} Days</p>
                        <span style="font-size:11.5px; color:#ff4d4f; font-weight:600;">Sold Out / Locked</span>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff5f5; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card" onclick="filterAvailabilityStatus('custom', document.querySelector('[data-filter=custom]'))" style="cursor:pointer; border-radius:6px; border:1px solid #e8e8e8; box-shadow:0 1px 3px rgba(0,0,0,0.03);" title="Click to filter Seasonal Priced days">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">SEASONAL RATES</p>
                        <p class="kpi-value" style="font-size:19px; font-weight:800; color:#7367f0; margin:0;">{{ $stats['custom_price_days'] }} Days</p>
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

    {{-- 🏢 1-LINE SLEEK SELECTION & VIEW TOOLBAR (Exact Stockifly SaaS Standard) --}}
    <div class="data-table-card" style="border-radius: 6px !important; background:#ffffff; border: 1px solid #e8e8e8 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.03); margin-bottom: 24px !important; padding: 12px 20px;">
        <form method="GET" action="{{ route('vendor.availability.index') }}" id="roomSelectForm" class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100 m-0">
            
            {{-- Filter Controls in 1 Line --}}
            <div class="d-flex align-items-center gap-3 flex-grow-1 flex-wrap">
                {{-- Room Selector --}}
                <div class="d-flex align-items-center gap-2" style="min-width: 280px; flex: 1;">
                    <label class="text-secondary fw-bold text-nowrap m-0 d-flex align-items-center" style="font-size:11.5px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fa-solid fa-bed text-primary me-1.5"></i> Room:
                    </label>
                    <select name="room_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 13px; font-weight:600; color:#1e293b; height:36px; border:1px solid #d9d9d9; border-radius:4px; background-color:#ffffff;">
                        @foreach($properties as $p)
                            <optgroup label="🏢 {{ $p->name }} ({{ $p->star_rating }}★)">
                                @foreach($p->rooms as $r)
                                    <option value="{{ $r->id }}" {{ $selectedRoom->id === $r->id ? 'selected' : '' }}>
                                        {{ $r->name }} — ৳{{ number_format($r->price_per_night) }}/night
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Timeline Selector --}}
                <div class="d-flex align-items-center gap-2" style="min-width: 170px;">
                    <label class="text-secondary fw-bold text-nowrap m-0 d-flex align-items-center" style="font-size:11.5px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fa-solid fa-calendar-week text-primary me-1.5"></i> Timeline:
                    </label>
                    <select name="days" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 13px; font-weight:600; color:#1e293b; height:36px; border:1px solid #d9d9d9; border-radius:4px;">
                        <option value="14" {{ $daysCount == 14 ? 'selected' : '' }}>14 Days</option>
                        <option value="30" {{ $daysCount == 30 ? 'selected' : '' }}>30 Days</option>
                        <option value="60" {{ $daysCount == 60 ? 'selected' : '' }}>60 Days</option>
                        <option value="90" {{ $daysCount == 90 ? 'selected' : '' }}>90 Days</option>
                    </select>
                </div>
            </div>

            {{-- Right: View Switcher (Grid / Table) --}}
            <div class="btn-group btn-group-sm" role="group" style="height:34px;">
                <button type="button" class="btn btn-outline-secondary active fw-bold px-3" id="btnViewGrid" onclick="toggleCalendarView('grid')" style="font-size:12px;">
                    <i class="fa-solid fa-table-cells-large me-1"></i> Grid
                </button>
                <button type="button" class="btn btn-outline-secondary fw-bold px-3" id="btnViewTable" onclick="toggleCalendarView('table')" style="font-size:12px;">
                    <i class="fa-solid fa-list me-1"></i> Table
                </button>
            </div>
        </form>
    </div>

    {{-- MAIN DUAL PANEL: CONTROLLER FORM + DYNAMIC RATES GRID --}}
    <div class="row g-3.5 mb-4">

        {{-- Left Panel: Update Rates & Dates Form --}}
        <div class="col-12 col-lg-4">
            <div class="data-table-card" style="border-radius:6px !important; background:#ffffff; border:1px solid #e8e8e8 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div class="data-table-card-header" style="padding: 14px 20px; border-bottom: 1px solid #f0f0f0; background:#ffffff; border-radius:6px 6px 0 0;">
                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center" style="font-size:13.5px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> Set Rate &amp; Status
                    </h6>
                </div>

                <div style="padding: 20px 22px;">
                    <form action="{{ route('vendor.availability.update-range') }}" method="POST" id="quickRateForm">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                        <div class="mb-3.5">
                            <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                                Start Date <span style="color:#ff4d4f;">*</span>
                            </label>
                            <input type="date" name="start_date" id="formStartDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:4px; padding:6px 12px;">
                        </div>

                        <div class="mb-3.5">
                            <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                                End Date <span style="color:#ff4d4f;">*</span>
                            </label>
                            <input type="date" name="end_date" id="formEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:4px; padding:6px 12px;">
                        </div>

                        <div class="mb-3.5">
                            <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                                Nightly Rate (BDT ৳)
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light" style="border:1px solid #d9d9d9; border-radius:4px 0 0 4px; font-size:12px; color:#595959;">৳ BDT</span>
                                <input type="number" name="price" id="formCustomPrice" class="form-control" placeholder="Base: {{ (int)$selectedRoom->price_per_night }}" step="0.01" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:0 4px 4px 0;">
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2">
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="applyPricePreset(1.15)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-bolt text-warning me-0.5"></i> +15% Weekend
                                </button>
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="applyPricePreset(1.25)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-bolt text-warning me-0.5"></i> +25% Peak
                                </button>
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="applyPricePreset(1.0)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-rotate-left me-0.5"></i> Base
                                </button>
                            </div>
                        </div>

                        <div class="mb-3.5 p-2.5 rounded bg-light border" style="border-radius:4px; border-color:#e8e8e8 !important;">
                            <div class="form-check form-switch m-0 d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" name="is_blocked" value="1" id="blockRoomCheck" style="cursor:pointer; width:38px; height:20px;">
                                <label class="form-check-label fw-bold text-danger ms-2.5 mb-0" for="blockRoomCheck" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-ban me-1"></i> Block / Sold Out
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="background-color: #1890ff; font-size:13px; border-radius:4px; border:none; letter-spacing:0.3px; height:38px; box-shadow: 0 2px 6px rgba(24,144,255,0.25);">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> SAVE CHANGES
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Panel: Dynamic Rates Grid & Table Container --}}
        <div class="col-12 col-lg-8">
            <div class="data-table-card p-0" style="border-radius:6px !important; background:#ffffff; border:1px solid #e8e8e8 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                
                {{-- TOOLBAR & FUNCTIONAL FILTER BUTTONS --}}
                <div class="data-table-card-header" style="padding: 14px 20px; border-bottom: 1px solid #f0f0f0; background:#ffffff; border-radius:6px 6px 0 0;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                        <h6 class="mb-0 fw-bold text-dark" style="font-size:13.5px; text-transform:uppercase; letter-spacing:0.4px;">
                            <i class="fa-solid fa-calendar-days text-primary me-1.5"></i> {{ $daysCount }}-Day Calendar
                        </h6>

                        {{-- 100% FUNCTIONAL INTERACTIVE TICK CHECKBOX FILTERS --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-secondary fw-bold me-1" style="font-size:11px; text-transform:uppercase;">Filter:</span>

                            {{-- Available Tick Filter --}}
                            <label class="filter-tick-badge is-available checked" id="lblChkAvailable" title="Toggle Available Dates" style="cursor:pointer;">
                                <input type="checkbox" id="chkAvailable" checked onchange="applyTickFilters()" style="display:none;">
                                <span class="tick-box"><i class="fa-solid fa-check tick-icon"></i></span>
                                <span class="tick-label" style="color:#0958d9;">Available</span>
                                <span class="badge-pill-count">{{ $stats['available_days'] }}</span>
                            </label>

                            {{-- Sold Out / Blocked Tick Filter --}}
                            <label class="filter-tick-badge is-blocked checked" id="lblChkBlocked" title="Toggle Sold Out Dates" style="cursor:pointer;">
                                <input type="checkbox" id="chkBlocked" checked onchange="applyTickFilters()" style="display:none;">
                                <span class="tick-box"><i class="fa-solid fa-check tick-icon"></i></span>
                                <span class="tick-label" style="color:#cf1322;">Blocked</span>
                                <span class="badge-pill-count">{{ $stats['sold_out_days'] }}</span>
                            </label>

                            {{-- Seasonal Price Filter --}}
                            <label class="filter-tick-badge is-custom checked" id="lblChkCustom" title="Toggle Seasonal Rates" style="cursor:pointer;">
                                <input type="checkbox" id="chkCustom" checked onchange="applyTickFilters()" style="display:none;">
                                <span class="tick-box"><i class="fa-solid fa-check tick-icon"></i></span>
                                <span class="tick-label" style="color:#531dab;">Seasonal</span>
                                <span class="badge-pill-count">{{ $stats['custom_price_days'] }}</span>
                            </label>

                            <button type="button" class="btn btn-link btn-sm text-secondary p-0 ms-1 fw-bold" onclick="resetAllTickFilters()" style="font-size:11.5px; text-decoration:none;">
                                <i class="fa-solid fa-rotate-left"></i> Reset
                            </button>
                        </div>
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
                    <div class="p-3 px-3.5 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#fafafa !important; border-color:#e8e8e8 !important;">
                        <span class="text-secondary fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.3px;">
                            <i class="fa-solid fa-list-check text-primary me-1"></i> Detailed Date-by-Date Breakdown
                        </span>
                        <input type="text" class="form-control form-control-sm" placeholder="Filter by date..." onkeyup="filterTableSearch('calendarTable', this.value)" style="width:180px; height:32px; font-size:12px; border:1px solid #d9d9d9; border-radius:4px; padding:4px 10px;">
                    </div>
                    <table class="table table-stockifly align-middle mb-0" id="calendarTable">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="padding-left: 20px !important;">Date &amp; Day</th>
                                <th>Base Rate</th>
                                <th>Effective Rate</th>
                                <th>Status</th>
                                <th style="text-align:right; padding-right: 20px !important;">Quick Action</th>
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
                                <td style="padding-left: 20px !important;">
                                    <strong style="font-size:13px; color:#1e293b; display:block;">{{ $currentDateObj->format('D, M d, Y') }}</strong>
                                    <span style="font-size:10.5px; color:#64748b;">{{ $currentDateObj->isToday() ? 'Today' : ($currentDateObj->isWeekend() ? 'Weekend' : 'Weekday') }}</span>
                                </td>
                                <td><span style="font-size:12.5px; color:#64748b; font-weight:600;">৳{{ number_format($selectedRoom->price_per_night) }}</span></td>
                                <td>
                                    @if($hasCustomPrice)
                                        <strong style="color:#7367f0; font-size:13px;">৳{{ number_format($effectivePrice) }}</strong>
                                        <span class="badge text-white ms-1" style="font-size:10px; background:#7367f0; border-radius:3px;">Seasonal</span>
                                    @else
                                        <span style="font-size:12.5px; color:#1e293b; font-weight:600;">৳{{ number_format($effectivePrice) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isBlocked)
                                        <span class="badge-status cancelled" style="background:#fff2f0; color:#ff4d4f; border:1px solid #ffccc7; font-weight:700; padding:3px 8px; border-radius:4px; font-size:11px;">
                                            <i class="fa-solid fa-ban me-1"></i> Sold Out / Blocked
                                        </span>
                                    @else
                                        <span class="badge-status active" style="background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f; font-weight:700; padding:3px 8px; border-radius:4px; font-size:11px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Available
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right; padding-right: 20px !important;">
                                    <form action="{{ route('vendor.availability.update-range') }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                                        <input type="hidden" name="start_date" value="{{ $dateStr }}">
                                        <input type="hidden" name="end_date" value="{{ $dateStr }}">
                                        @if($isBlocked)
                                            <input type="hidden" name="is_blocked" value="0">
                                            <button type="submit" class="btn btn-sm btn-outline-success fw-bold px-2.5 py-1" style="font-size:11px; border-radius:4px; height:28px;">
                                                <i class="fa-solid fa-unlock me-1"></i> Unblock
                                            </button>
                                        @else
                                            <input type="hidden" name="is_blocked" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-2.5 py-1" style="font-size:11px; border-radius:4px; height:28px;">
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

    {{-- BATCH UPDATE MODAL (Stockifly Enterprise Modal) --}}
    <div class="modal fade text-start" id="batchUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:6px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
                <form action="{{ route('vendor.availability.update-range') }}" method="POST" id="batchUpdateModalForm">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                    <div class="modal-header py-3 px-3.5" style="background:#002140; color:#fff; border-radius:6px 6px 0 0;">
                        <h6 class="modal-title fw-bold text-white m-0 d-flex align-items-center" style="font-size:14px; letter-spacing:0.3px;">
                            <i class="fa-solid fa-sliders me-2 text-info"></i> Bulk Range Update Rates &amp; Availability
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="p-2.5 mb-3 rounded d-flex align-items-center gap-2" style="background:#f0f7ff; border:1px solid #bae0ff;">
                            <i class="fa-solid fa-bed text-primary"></i>
                            <div style="font-size:12px; color:#1e293b;">
                                Updating: <strong class="text-primary">{{ $selectedRoom->name }}</strong> (Base: ৳{{ number_format($selectedRoom->price_per_night) }}/night)
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.3px;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="modalStartDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:4px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.3px;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="modalEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:4px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label mb-1" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.3px;">Custom Nightly Price (BDT ৳)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light" style="border:1px solid #d9d9d9; border-radius:4px 0 0 4px;">৳ BDT</span>
                                <input type="number" name="price" id="modalPriceInput" class="form-control" placeholder="Base rate: {{ (int)$selectedRoom->price_per_night }}" step="0.01" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:0 4px 4px 0;">
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2">
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="document.getElementById('modalPriceInput').value = Math.round({{ (float)$selectedRoom->price_per_night }} * 1.15)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-bolt text-warning me-0.5"></i> +15% Weekend
                                </button>
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="document.getElementById('modalPriceInput').value = Math.round({{ (float)$selectedRoom->price_per_night }} * 1.25)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-bolt text-warning me-0.5"></i> +25% Peak
                                </button>
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="document.getElementById('modalPriceInput').value = ''" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-rotate-left me-0.5"></i> Base
                                </button>
                            </div>
                        </div>

                        <div class="form-check form-switch p-2.5 bg-light rounded border m-0 d-flex align-items-center" style="border-color:#e8e8e8 !important; border-radius:4px;">
                            <input class="form-check-input ms-0 me-2.5" type="checkbox" name="is_blocked" value="1" id="modalBlockCheck" style="cursor:pointer; width:36px; height:18px;">
                            <label class="form-check-label fw-bold text-danger mb-0" for="modalBlockCheck" style="font-size:12px; cursor:pointer;">
                                <i class="fa-solid fa-ban me-1"></i> Mark entire date range as Sold Out / Blocked
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer py-2.5 px-4 bg-light border-top" style="border-color:#e8e8e8 !important;">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius:4px; height:36px;">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3" style="background-color:#1890ff; border:none; border-radius:4px; height:36px; box-shadow:0 2px 6px rgba(24,144,255,0.25);">
                            <i class="fa-solid fa-check me-1.5"></i> Apply Calendar Changes
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

/* 🏷️ INTERACTIVE TICK CHECKBOX FILTER BADGES (Matching User Screenshot) */
.filter-tick-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    user-select: none;
    transition: all 0.18s ease;
    border: 1px solid #e2e8f0;
    background: #ffffff;
}
.filter-tick-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.filter-tick-badge .tick-box {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    transition: all 0.18s ease;
}

/* Checked States */
.filter-tick-badge.is-available.checked {
    border-color: #91caff;
    background: #f0f7ff;
}
.filter-tick-badge.is-available.checked .tick-box {
    background: #1890ff;
    color: #ffffff;
}
.filter-tick-badge.is-available:not(.checked) {
    opacity: 0.55;
    background: #fafafa;
    border-color: #e2e8f0;
}
.filter-tick-badge.is-available:not(.checked) .tick-box {
    border: 1px solid #cbd5e1;
    background: #ffffff;
}
.filter-tick-badge.is-available:not(.checked) .tick-icon {
    display: none;
}

.filter-tick-badge.is-blocked.checked {
    border-color: #ffa39e;
    background: #fff1f0;
}
.filter-tick-badge.is-blocked.checked .tick-box {
    background: #ff4d4f;
    color: #ffffff;
}
.filter-tick-badge.is-blocked:not(.checked) {
    opacity: 0.55;
    background: #fafafa;
    border-color: #e2e8f0;
}
.filter-tick-badge.is-blocked:not(.checked) .tick-box {
    border: 1px solid #cbd5e1;
    background: #ffffff;
}
.filter-tick-badge.is-blocked:not(.checked) .tick-icon {
    display: none;
}

.filter-tick-badge.is-custom.checked {
    border-color: #d3adf7;
    background: #f9f0ff;
}
.filter-tick-badge.is-custom.checked .tick-box {
    background: #7367f0;
    color: #ffffff;
}
.filter-tick-badge.is-custom:not(.checked) {
    opacity: 0.55;
    background: #fafafa;
    border-color: #e2e8f0;
}
.filter-tick-badge.is-custom:not(.checked) .tick-box {
    border: 1px solid #cbd5e1;
    background: #ffffff;
}
.filter-tick-badge.is-custom:not(.checked) .tick-icon {
    display: none;
}

/* 📱 ADVANCED MOBILE RESPONSIVENESS & BREAKPOINTS */
@media (min-width: 1200px) {
    .avail-card-col {
        flex: 0 0 20%;
        max-width: 20%;
    }
}
@media (min-width: 768px) and (max-width: 1199px) {
    .avail-card-col {
        flex: 0 0 25%;
        max-width: 25%;
    }
}
@media (min-width: 480px) and (max-width: 767px) {
    .avail-card-col {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
}
@media (max-width: 479px) {
    .avail-card-col {
        flex: 0 0 50%;
        max-width: 50%;
    }
    .calendar-day-card {
        padding: 8px 6px;
        min-height: 95px;
    }
    .cal-day-name {
        font-size: 11px;
    }
    .cal-rate-val {
        font-size: 12px;
    }
    .btn-legend-pill {
        padding: 3px 8px;
        font-size: 11px;
    }
}

/* 🖨️ PRINT & PDF STYLING */
@media print {
    .page-header-card, .btn-tbl-copy, .btn-tbl-excel, .btn-export-csv, .btn-export-pdf, .btn-tbl-print,
    .navbar-vendor, .vendor-sidebar, .card.border, #quickRateForm, .cal-card-action, .btn-legend-pill {
        display: none !important;
    }
    .page-content-area {
        padding: 0 !important;
        margin: 0 !important;
    }
    .col-lg-4 {
        display: none !important;
    }
    .col-lg-8 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    #calendarGridView {
        max-height: none !important;
        overflow: visible !important;
    }
}
</style>

<script>
/**
 * Interactive Multi-Select Tick Filter for Checkboxes
 */
function applyTickFilters() {
    const chkAvail  = document.getElementById('chkAvailable');
    const chkBlock  = document.getElementById('chkBlocked');
    const chkCust   = document.getElementById('chkCustom');

    const showAvailable = chkAvail ? chkAvail.checked : true;
    const showBlocked   = chkBlock ? chkBlock.checked : true;
    const showCustom    = chkCust  ? chkCust.checked  : true;

    // Toggle checked visual state on badges
    if (document.getElementById('lblChkAvailable')) {
        document.getElementById('lblChkAvailable').classList.toggle('checked', showAvailable);
    }
    if (document.getElementById('lblChkBlocked')) {
        document.getElementById('lblChkBlocked').classList.toggle('checked', showBlocked);
    }
    if (document.getElementById('lblChkCustom')) {
        document.getElementById('lblChkCustom').classList.toggle('checked', showCustom);
    }

    let activeCount = 0;

    // Filter Grid Cards
    document.querySelectorAll('.avail-card-col').forEach(card => {
        const isBlocked = card.getAttribute('data-status') === 'blocked';
        const isCustom  = card.getAttribute('data-custom') === '1';

        let show = false;
        if (isBlocked && showBlocked) {
            show = true;
        } else if (!isBlocked && showAvailable) {
            if (!isCustom || showCustom) {
                show = true;
            }
        }

        card.style.display = show ? '' : 'none';
        if (show) activeCount++;
    });

    // Filter Table Rows
    document.querySelectorAll('.avail-row').forEach(row => {
        const isBlocked = row.getAttribute('data-status') === 'blocked';
        const isCustom  = row.getAttribute('data-custom') === '1';

        let show = false;
        if (isBlocked && showBlocked) {
            show = true;
        } else if (!isBlocked && showAvailable) {
            if (!isCustom || showCustom) {
                show = true;
            }
        }

        row.style.display = show ? '' : 'none';
    });
}

/**
 * Reset All Tick Filters to Checked
 */
function resetAllTickFilters() {
    if (document.getElementById('chkAvailable')) document.getElementById('chkAvailable').checked = true;
    if (document.getElementById('chkBlocked'))   document.getElementById('chkBlocked').checked   = true;
    if (document.getElementById('chkCustom'))    document.getElementById('chkCustom').checked    = true;
    applyTickFilters();
}

/**
 * Legacy Filter function for KPI Cards
 */
function filterAvailabilityStatus(status) {
    if (status === 'available') {
        if (document.getElementById('chkAvailable')) document.getElementById('chkAvailable').checked = true;
        if (document.getElementById('chkBlocked'))   document.getElementById('chkBlocked').checked   = false;
        if (document.getElementById('chkCustom'))    document.getElementById('chkCustom').checked    = true;
    } else if (status === 'blocked') {
        if (document.getElementById('chkAvailable')) document.getElementById('chkAvailable').checked = false;
        if (document.getElementById('chkBlocked'))   document.getElementById('chkBlocked').checked   = true;
        if (document.getElementById('chkCustom'))    document.getElementById('chkCustom').checked    = false;
    } else if (status === 'custom') {
        if (document.getElementById('chkAvailable')) document.getElementById('chkAvailable').checked = true;
        if (document.getElementById('chkBlocked'))   document.getElementById('chkBlocked').checked   = false;
        if (document.getElementById('chkCustom'))    document.getElementById('chkCustom').checked    = true;
    } else {
        resetAllTickFilters();
        return;
    }
    applyTickFilters();
}

/**
 * Apply Price Percentage Preset (+15% Weekend, +25% Peak Season, Base Rate)
 */
function applyPricePreset(multiplier) {
    const baseRate = {{ (float)($selectedRoom->price_per_night ?? 0) }};
    const priceInput = document.getElementById('formCustomPrice');
    if (!priceInput) return;

    if (multiplier === 1.0) {
        priceInput.value = '';
    } else {
        const calculated = Math.round(baseRate * multiplier);
        priceInput.value = calculated;
    }
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

/**
 * ⚡ Full Suite of 100% Dynamic AJAX Form Handlers (Zero Page Reload)
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Quick Rate Left Form AJAX Handler
    const rateForm = document.getElementById('quickRateForm');
    if (rateForm) {
        rateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = rateForm.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1.5"></i> SAVING...';

            const formData = new FormData(rateForm);

            fetch(rateForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-check me-1.5"></i> SAVED INSTANTLY!';
                setTimeout(() => { submitBtn.innerHTML = originalBtnHtml; }, 1800);

                if (data.status === 'success' && data.records) {
                    updateCalendarDOM(data.records, data.base_price);
                    showLiveToast('⚡ Rates & availability updated instantly!');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                rateForm.submit();
            });
        });
    }

    // 2. Batch Update Modal Form AJAX Handler
    const modalForm = document.getElementById('batchUpdateModalForm');
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = modalForm.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1.5"></i> APPLYING...';

            const formData = new FormData(modalForm);

            fetch(modalForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                // Close Bootstrap modal
                const modalEl = document.getElementById('batchUpdateModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    bsModal.hide();
                }

                if (data.status === 'success' && data.records) {
                    updateCalendarDOM(data.records, data.base_price);
                    showLiveToast('🎉 Bulk calendar rates applied successfully!');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                modalForm.submit();
            });
        });
    }

    // 3. Table Inline Block/Unblock AJAX Handler
    document.addEventListener('submit', function(e) {
        const targetForm = e.target;
        if (targetForm && targetForm.closest('#calendarTableView')) {
            e.preventDefault();
            const btn = targetForm.querySelector('button');
            if (btn) btn.disabled = true;

            const formData = new FormData(targetForm);

            fetch(targetForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (btn) btn.disabled = false;
                if (data.status === 'success' && data.records) {
                    updateCalendarDOM(data.records, data.base_price);
                    showLiveToast('⚡ Date status toggled instantly!');
                }
            })
            .catch(() => {
                targetForm.submit();
            });
        }
    });
});

/**
 * Update Calendar Grid and Table DOM dynamically
 */
function updateCalendarDOM(records, basePrice) {
    records.forEach(rec => {
        const isBlocked   = rec.is_blocked;
        const customPrice = rec.price;
        const hasCustom   = customPrice !== null && parseFloat(customPrice) !== parseFloat(basePrice);
        const effPrice    = customPrice !== null ? parseFloat(customPrice) : basePrice;

        // 1. Update Grid Card
        const cardCol = document.querySelector(`.avail-card-col[data-date="${rec.date}"]`);
        if (cardCol) {
            cardCol.setAttribute('data-status', isBlocked ? 'blocked' : 'available');
            cardCol.setAttribute('data-custom', hasCustom ? '1' : '0');

            const dayCard = cardCol.querySelector('.calendar-day-card');
            if (dayCard) {
                dayCard.className = `calendar-day-card ${isBlocked ? 'is-blocked' : (hasCustom ? 'is-custom' : 'is-available')}`;
                const dayName = dayCard.querySelector('.cal-day-name') ? dayCard.querySelector('.cal-day-name').innerHTML : '';
                if (isBlocked) {
                    dayCard.innerHTML = `
                        <div class="cal-day-name">${dayName}</div>
                        <div class="cal-status-badge text-danger fw-bold"><i class="fa-solid fa-ban me-0.5"></i> SOLD OUT</div>
                        <div class="cal-rate-sub text-muted">Blocked</div>
                        <div class="cal-click-hint"><i class="fa-solid fa-pen-to-square"></i> Edit</div>
                    `;
                } else {
                    dayCard.innerHTML = `
                        <div class="cal-day-name">${dayName}</div>
                        <div class="cal-rate-val ${hasCustom ? 'text-purple' : 'text-primary'}">৳ ${Number(effPrice).toLocaleString()}</div>
                        ${hasCustom ? '<span class="cal-custom-tag">Seasonal</span>' : '<span class="cal-std-tag">Standard</span>'}
                        <div class="cal-click-hint"><i class="fa-solid fa-pen-to-square"></i> Edit</div>
                    `;
                }
            }
        }

        // 2. Update Table Row
        const tableRow = document.querySelector(`.avail-row[data-date="${rec.date}"]`);
        if (tableRow) {
            tableRow.setAttribute('data-status', isBlocked ? 'blocked' : 'available');
            tableRow.setAttribute('data-custom', hasCustom ? '1' : '0');
            tableRow.style.backgroundColor = isBlocked ? '#fff5f5' : (hasCustom ? '#f0f7ff' : 'transparent');

            const cols = tableRow.querySelectorAll('td');
            if (cols.length >= 5) {
                // Effective Rate Col
                cols[2].innerHTML = hasCustom 
                    ? `<strong style="color:#7367f0; font-size:13px;">৳${Number(effPrice).toLocaleString()}</strong><span class="badge text-white ms-1" style="font-size:10px; background:#7367f0; border-radius:3px;">Seasonal</span>`
                    : `<span style="font-size:12.5px; color:#1e293b; font-weight:600;">৳${Number(effPrice).toLocaleString()}</span>`;

                // Status Col
                cols[3].innerHTML = isBlocked 
                    ? `<span class="badge-status cancelled" style="background:#fff2f0; color:#ff4d4f; border:1px solid #ffccc7; font-weight:700; padding:3px 8px; border-radius:4px; font-size:11px;"><i class="fa-solid fa-ban me-1"></i> Sold Out / Blocked</span>`
                    : `<span class="badge-status active" style="background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f; font-weight:700; padding:3px 8px; border-radius:4px; font-size:11px;"><i class="fa-solid fa-circle-check me-1"></i> Available</span>`;

                // Action Form Col
                cols[4].innerHTML = `
                    <form action="{{ route('vendor.availability.update-range') }}" method="POST" class="d-inline-block">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                        <input type="hidden" name="start_date" value="${rec.date}">
                        <input type="hidden" name="end_date" value="${rec.date}">
                        <input type="hidden" name="is_blocked" value="${isBlocked ? '0' : '1'}">
                        <button type="submit" class="btn btn-sm ${isBlocked ? 'btn-outline-success' : 'btn-outline-danger'} fw-bold px-2.5 py-1" style="font-size:11px; border-radius:4px; height:28px;">
                            <i class="fa-solid ${isBlocked ? 'fa-unlock' : 'fa-ban'} me-1"></i> ${isBlocked ? 'Unblock' : 'Block'}
                        </button>
                    </form>
                `;
            }
        }
    });

    recountFilters();
}

/**
 * Dynamic Filter Counts and KPI Recalculation
 */
function recountFilters() {
    let availCount = 0, blockCount = 0, custCount = 0;
    document.querySelectorAll('.avail-card-col').forEach(c => {
        const isB = c.getAttribute('data-status') === 'blocked';
        const isC = c.getAttribute('data-custom') === '1';
        if (isB) blockCount++;
        else {
            availCount++;
            if (isC) custCount++;
        }
    });

    const lblAvail = document.querySelector('#lblChkAvailable .badge-pill-count');
    const lblBlock = document.querySelector('#lblChkBlocked .badge-pill-count');
    const lblCust  = document.querySelector('#lblChkCustom .badge-pill-count');
    if (lblAvail) lblAvail.innerText = availCount;
    if (lblBlock) lblBlock.innerText = blockCount;
    if (lblCust)  lblCust.innerText  = custCount;
}

/**
 * Toast Notification Popup
 */
function showLiveToast(message) {
    let toast = document.getElementById('liveInstantToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'liveInstantToast';
        toast.style.cssText = 'position:fixed; bottom:24px; right:24px; z-index:99999; background:#002140; color:#fff; padding:12px 20px; border-radius:6px; font-size:13px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.25); display:flex; align-items:center; gap:8px; transition:all 0.3s ease;';
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#52c41a; font-size:16px;"></i> ${message}`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
    }, 3500);
}
</script>
@endsection
