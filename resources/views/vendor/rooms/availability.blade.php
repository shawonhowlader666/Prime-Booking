@extends('layouts.vendor')
@section('title', 'Room Rates & Availability Calendar — Vendor Partner')

@php use App\Services\CurrencyService; @endphp

@section('content')

{{-- PAGE HEADER & EXPORT TOOLBAR (Exact Stockifly Admin Attached Header) --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title m-0">Rates &amp; Availability Calendar</h1>
            <div class="page-breadcrumb mt-1.5">
                <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <span class="sep">-</span><span>Inventory</span>
                <span class="sep">-</span><strong style="color:#333;">Rates &amp; Calendar</strong>
            </div>
        </div>
        
        {{-- FULLY FUNCTIONAL EXPORT TOOLBAR --}}
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
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
    <div class="row g-3 mb-3.5">
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

    {{-- 🏢 SMART PROPERTY & ROOM CATEGORY SELECTION CARD (Stockifly Enterprise UI) --}}
    <div class="data-table-card mb-3.5" style="border-radius: 6px !important; background:#ffffff; border: 1px solid #e8e8e8 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <div class="data-table-card-header" style="padding: 14px 24px; border-bottom: 1px solid #f0f0f0; background:#ffffff; border-radius: 6px 6px 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                <div>
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center" style="font-size:14.5px; color:#0f172a;">
                        <i class="fa-solid fa-hotel text-primary me-2" style="font-size:15px;"></i>
                        <span>Select Property &amp; Room Category</span>
                    </h5>
                    <small class="text-secondary mt-0.5 d-block" style="font-size:12px;">Choose which hotel and specific room category you want to view, forecast, and manage calendar rates for.</small>
                </div>
                <div class="btn-group btn-group-sm" role="group" style="height:34px;">
                    <button type="button" class="btn btn-outline-secondary active fw-bold px-3" id="btnViewGrid" onclick="toggleCalendarView('grid')">
                        <i class="fa-solid fa-table-cells-large me-1"></i> Grid View
                    </button>
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" id="btnViewTable" onclick="toggleCalendarView('table')">
                        <i class="fa-solid fa-list me-1"></i> Table View
                    </button>
                </div>
            </div>
        </div>

        <div style="padding: 20px 24px;">
            <form method="GET" action="{{ route('vendor.availability.index') }}" id="roomSelectForm" class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-lg-5">
                    <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fa-solid fa-bed text-primary me-1"></i> Choose Hotel &amp; Room Category <span style="color:#ff4d4f;">*</span>
                    </label>
                    <select name="room_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 13px; font-weight:600; color:#1e293b; height:38px; border:1px solid #d9d9d9; border-radius:4px; background-color:#ffffff;">
                        @foreach($properties as $p)
                            <optgroup label="🏢 {{ $p->name }} — {{ $p->city }} ({{ $p->star_rating }}★)">
                                @foreach($p->rooms as $r)
                                    <option value="{{ $r->id }}" {{ $selectedRoom->id === $r->id ? 'selected' : '' }}>
                                        🛏️ {{ $r->name }}  ➔  BDT ৳{{ number_format($r->price_per_night) }} / night
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3 col-lg-3">
                    <label class="form-label mb-1.5" style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fa-solid fa-calendar-week text-primary me-1"></i> Forecast Timeline
                    </label>
                    <select name="days" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 13px; font-weight:600; color:#1e293b; height:38px; border:1px solid #d9d9d9; border-radius:4px;">
                        <option value="14" {{ $daysCount == 14 ? 'selected' : '' }}>📅 Next 14 Days (2 Weeks)</option>
                        <option value="30" {{ $daysCount == 30 ? 'selected' : '' }}>📅 Next 30 Days (1 Month)</option>
                        <option value="60" {{ $daysCount == 60 ? 'selected' : '' }}>📅 Next 60 Days (2 Months)</option>
                        <option value="90" {{ $daysCount == 90 ? 'selected' : '' }}>📅 Next 90 Days (3 Months)</option>
                    </select>
                </div>

                <div class="col-12 col-md-3 col-lg-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-add-primary w-100 justify-content-center" style="height:38px; font-size:13px; border-radius:4px; font-weight:600;">
                        <i class="fa-solid fa-rotate me-1.5"></i> Load Room Calendar
                    </button>
                </div>
            </form>

            {{-- 🎯 LIVE ACTIVE SELECTION STATUS BANNER --}}
            <div class="mt-3 p-2.5 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#f0f7ff; border: 1px solid #bae0ff; border-radius:4px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:26px; height:26px; border-radius:50%; background:#1890ff; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0;">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div style="font-size:12.5px; color:#1e293b;">
                        <span class="text-secondary">Currently Managing:</span> 
                        <strong class="text-primary" style="font-size:13px;">{{ $selectedRoom->name }}</strong>
                        <span class="text-muted mx-1.5">•</span>
                        <span>Base Rate: <strong class="text-dark">৳{{ number_format($selectedRoom->price_per_night) }} / night</strong></span>
                        <span class="text-muted mx-1.5">•</span>
                        <span>Hotel: <strong class="text-secondary">{{ $selectedRoom->property->name ?? 'Your Hotel' }}</strong></span>
                    </div>
                </div>
                <div>
                    <span class="badge bg-primary text-white px-2.5 py-1" style="font-size:11px; border-radius:3px; font-weight:600;">
                        <i class="fa-solid fa-circle-check me-1"></i> Active Inventory
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN DUAL PANEL: CONTROLLER FORM + DYNAMIC RATES GRID --}}
    <div class="row g-3.5 mb-4">

        {{-- Left Panel: Update Rates & Dates Form --}}
        <div class="col-12 col-lg-4">
            <div class="data-table-card" style="border-radius:6px !important; background:#ffffff; border:1px solid #e8e8e8 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div class="data-table-card-header" style="padding: 14px 20px; border-bottom: 1px solid #f0f0f0; background:#ffffff; border-radius:6px 6px 0 0;">
                    <div>
                        <h6 class="fw-bold text-dark m-0 d-flex align-items-center" style="font-size:13.5px; text-transform:uppercase; letter-spacing:0.4px;">
                            <i class="fa-solid fa-sliders text-primary me-2"></i> Update Rates &amp; Dates
                        </h6>
                        <small class="text-muted mt-0.5 d-block" style="font-size:11px;">Click any date box on the right or choose a date range below.</small>
                    </div>
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
                                Custom Nightly Rate (BDT ৳)
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light" style="border:1px solid #d9d9d9; border-radius:4px 0 0 4px; font-size:12px; color:#595959;">৳ BDT</span>
                                <input type="number" name="price" id="formCustomPrice" class="form-control" placeholder="Base rate: {{ (int)$selectedRoom->price_per_night }}" step="0.01" style="font-size:13px; height:38px; border:1px solid #d9d9d9; border-radius:0 4px 4px 0;">
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2">
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="applyPricePreset(1.15)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-bolt text-warning me-0.5"></i> +15% Weekend
                                </button>
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="applyPricePreset(1.25)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-bolt text-warning me-0.5"></i> +25% Peak
                                </button>
                                <button type="button" class="btn btn-outline-secondary py-0.5 px-2" onclick="applyPricePreset(1.0)" style="font-size:10.5px; border-radius:4px; font-weight:600;">
                                    <i class="fa-solid fa-rotate-left me-0.5"></i> Base (৳{{ number_format($selectedRoom->price_per_night) }})
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1.5" style="font-size:11px;">Leave empty to keep standard base price (৳{{ number_format($selectedRoom->price_per_night) }}).</small>
                        </div>

                        <div class="mb-3.5 p-2.5 rounded bg-light border" style="border-radius:4px; border-color:#e8e8e8 !important;">
                            <div class="form-check form-switch m-0 d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" name="is_blocked" value="1" id="blockRoomCheck" style="cursor:pointer; width:38px; height:20px;">
                                <label class="form-check-label fw-bold text-danger ms-2.5 mb-0" for="blockRoomCheck" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-ban me-1"></i> Block Room / Mark as Sold Out
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="background-color: #1890ff; font-size:13px; border-radius:4px; border:none; letter-spacing:0.3px; height:38px; box-shadow: 0 2px 6px rgba(24,144,255,0.25);">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> SAVE CALENDAR RATES
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
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size:13.5px; text-transform:uppercase; letter-spacing:0.4px;">
                                <i class="fa-solid fa-calendar-days text-primary me-1.5"></i> {{ $daysCount }}-Day Rates Grid
                            </h6>
                            <small class="text-muted d-block mt-0.5" style="font-size:11px;">Selected Room: <strong>{{ $selectedRoom->name }}</strong></small>
                        </div>

                        {{-- 100% FUNCTIONAL INTERACTIVE TICK CHECKBOX FILTERS (Matching Screenshot) --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-secondary fw-bold me-1" style="font-size:11.5px;">Filter:</span>

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
                                <span class="tick-label" style="color:#cf1322;">Sold Out/Blocked</span>
                                <span class="badge-pill-count">{{ $stats['sold_out_days'] }}</span>
                            </label>

                            {{-- Seasonal Price Filter --}}
                            <label class="filter-tick-badge is-custom checked" id="lblChkCustom" title="Toggle Seasonal Rates" style="cursor:pointer;">
                                <input type="checkbox" id="chkCustom" checked onchange="applyTickFilters()" style="display:none;">
                                <span class="tick-box"><i class="fa-solid fa-check tick-icon"></i></span>
                                <span class="tick-label" style="color:#531dab;">Seasonal Rate</span>
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
</script>
@endsection
