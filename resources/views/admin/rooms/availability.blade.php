@extends('layouts.admin')
@section('title', 'Room Availability & Extranet Calendar — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Extranet Room Availability &amp; Calendar Grid</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            @if($selectedRoom)
                <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#modalBulkCalendarUpdate">
                    <i class="fa-solid fa-sliders me-1"></i> Bulk Date &amp; Rate Overrides
                </button>
            @endif
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Hotels &amp; Resorts</a>
        <span class="sep">-</span><strong style="color:#333;">Extranet Calendar</strong>
    </div>
</div>

<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:6px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- SELECTOR TOOLBAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <form method="GET" action="{{ route('admin.rooms.availability') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label mb-1 text-muted" style="font-size:11.5px; font-weight:700;">SELECT HOTEL / RESORT:</label>
                <select name="property_id" class="form-select form-select-sm" style="font-size:13px; font-weight:600;" onchange="this.form.submit()">
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ ($selectedProperty && $selectedProperty->id === $prop->id) ? 'selected' : '' }}>
                            {{ $prop->name }} ({{ $prop->city }}) {{ $prop->vendor ? '— Partner: ' . $prop->vendor->name : '— Admin Direct' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label mb-1 text-muted" style="font-size:11.5px; font-weight:700;">ROOM TYPE:</label>
                <select name="room_id" class="form-select form-select-sm" style="font-size:13px; font-weight:600;" onchange="this.form.submit()">
                    @if($selectedProperty && $selectedProperty->rooms->isNotEmpty())
                        @foreach($selectedProperty->rooms as $rm)
                            <option value="{{ $rm->id }}" {{ ($selectedRoom && $selectedRoom->id === $rm->id) ? 'selected' : '' }}>
                                {{ $rm->name }} (Base: ৳{{ number_format((float)$rm->price_per_night) }})
                            </option>
                        @endforeach
                    @else
                        <option value="">No Rooms Configured</option>
                    @endif
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label mb-1 text-muted" style="font-size:11.5px; font-weight:700;">START DATE:</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate->format('Y-m-d') }}" onchange="this.form.submit()" style="font-size:13px;">
            </div>

            <div class="col-md-2">
                <label class="form-label mb-1 text-muted" style="font-size:11.5px; font-weight:700;">TIMEFRAME:</label>
                <select name="days" class="form-select form-select-sm" style="font-size:13px;" onchange="this.form.submit()">
                    <option value="14" {{ $daysCount == 14 ? 'selected' : '' }}>Next 14 Days</option>
                    <option value="30" {{ $daysCount == 30 ? 'selected' : '' }}>Next 30 Days</option>
                    <option value="60" {{ $daysCount == 60 ? 'selected' : '' }}>Next 60 Days</option>
                </select>
            </div>
        </form>
    </div>

    @if($selectedRoom)
        {{-- KPI SUMMARY ROW --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="kpi-card" style="padding:16px 20px;">
                    <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">AVAILABLE NIGHTS</p>
                    <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['available_days'] }} / {{ $stats['total_days'] }} Days</p>
                    <div class="kpi-accent-bar" style="background:#28c76f;"></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" style="padding:16px 20px;">
                    <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">BLOCKED / SOLD OUT</p>
                    <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['blocked_days'] }} Days</p>
                    <div class="kpi-accent-bar" style="background:#ea5455;"></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" style="padding:16px 20px;">
                    <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">AVG NIGHTLY RATE</p>
                    <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">৳{{ number_format($stats['avg_rate']) }}</p>
                    <div class="kpi-accent-bar" style="background:#7367f0;"></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" style="padding:16px 20px;">
                    <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">ACTIVE RESERVATIONS</p>
                    <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['total_bookings'] }} Bookings</p>
                    <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
                </div>
            </div>
        </div>

        {{-- CALENDAR MATRIX GRID --}}
        <div class="card border border-gray-200 rounded-3 mb-4 bg-white shadow-xs p-3" style="border-radius:8px !important;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-calendar-check text-primary me-2"></i> Multi-Day Inventory Matrix: <span class="text-primary">{{ $selectedProperty->name }} — {{ $selectedRoom->name }}</span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="font-size:11px;">
                        <i class="fa-solid fa-circle me-1" style="font-size:8px;"></i> Open / Available
                    </span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1" style="font-size:11px;">
                        <i class="fa-solid fa-ban me-1" style="font-size:8px;"></i> Blocked / Full
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1" style="font-size:11px;">
                        <i class="fa-solid fa-bolt me-1" style="font-size:8px;"></i> Weekend Surge
                    </span>
                </div>
            </div>

            <div class="row g-2">
                @foreach($matrix as $day)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="p-2.5 rounded border text-center position-relative" style="background: {{ $day['is_blocked'] ? '#fff1f0' : ($day['is_weekend'] ? '#f0f5ff' : '#ffffff') }}; border-color: {{ $day['is_today'] ? '#1890ff !important; border-width: 2px !important;' : '#e2e8f0;' }}; min-height:120px; display:flex; flex-direction:column; justify-content:space-between;">
                            
                            {{-- Day Header --}}
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $day['is_weekend'] ? 'bg-primary' : 'bg-light text-secondary border' }}" style="font-size:10px; font-weight:700;">
                                        {{ $day['day_name'] }}
                                    </span>
                                    <strong style="font-size:13px; color: {{ $day['is_today'] ? '#1890ff' : '#334155' }};">
                                        {{ $day['day_num'] }} {{ $day['month_name'] }}
                                    </strong>
                                </div>

                                {{-- Availability Units --}}
                                <div class="my-1.5">
                                    @if($day['is_blocked'])
                                        <span class="badge bg-danger text-white w-100 py-1" style="font-size:11px;">
                                            <i class="fa-solid fa-lock me-1"></i> BLOCKED
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 w-100 py-1" style="font-size:11px;">
                                            <i class="fa-solid fa-bed me-1"></i> {{ $day['available_units'] }} / {{ $day['capacity'] }} Avail
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Nightly Rate & Surge --}}
                            <div class="pt-1 border-top" style="border-color: rgba(0,0,0,0.06) !important;">
                                <strong style="font-size:13px; color: {{ $day['is_weekend'] ? '#1890ff' : '#0f172a' }}; display:block;">
                                    ৳{{ number_format((float)$day['price']) }}
                                </strong>
                                <span style="font-size:9.5px; color:#64748b;">
                                    {{ $day['pricing_note'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="card p-5 text-center bg-white border rounded-3">
            <i class="fa-solid fa-hotel fa-3x text-muted mb-3"></i>
            <h5>No Rooms Found</h5>
            <p class="text-muted">Please configure hotel rooms first to manage live calendar extranet inventory.</p>
        </div>
    @endif

</div>

{{-- BULK OVERRIDE MODAL --}}
@if($selectedRoom)
<div class="modal fade" id="modalBulkCalendarUpdate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:6px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('admin.rooms.availability.bulk-update') }}" method="POST">
                @csrf
                <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> Bulk Calendar Rate &amp; Block Overrides
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="p-2.5 bg-light rounded border mb-3">
                        <span class="text-muted d-block" style="font-size:11.5px;">Applying changes for:</span>
                        <strong class="text-dark" style="font-size:13px;">{{ $selectedProperty->name }} — {{ $selectedRoom->name }}</strong>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600;">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" required style="font-size:13px; height:38px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600;">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ $startDate->copy()->addDays(7)->format('Y-m-d') }}" required style="font-size:13px; height:38px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600;">Action Type <span class="text-danger">*</span></label>
                        <select name="action" class="form-select" style="font-size:13px; height:38px;">
                            <option value="block">🚫 Block / Close Dates (Stop Bookings)</option>
                            <option value="unblock">✅ Open / Unblock Dates</option>
                            <option value="set_price">৳ Set Custom Nightly Rate</option>
                            <option value="reset_price">🔄 Reset to Base Rate (৳{{ number_format((float)$selectedRoom->price_per_night) }})</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600;">Custom Rate per Night (BDT)</label>
                        <input type="number" step="1" name="price" class="form-control" placeholder="e.g. 4500 (if set custom rate chosen)" style="font-size:13px; height:38px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; background-color:var(--primary); border:none;">Apply Extranet Changes <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
