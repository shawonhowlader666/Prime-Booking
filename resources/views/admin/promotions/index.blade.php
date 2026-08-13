@extends('layouts.admin')

@section('title', 'Promotions Manager — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Promotions Manager</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <div>
            <h1 class="page-title">Promotions &amp; Banner Campaigns</h1>
            <span style="font-size:12px; color:#8c8c8c;">Control homepage banners for Accommodation, Flights, and Activities</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('promotionsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('promotionsTable', 'Promotions')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('promotionsTable', 'Promotions')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('promotionsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('promotionsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <a href="{{ route('admin.promotions.create') }}" class="btn-add-primary">
                <i class="fa-solid fa-plus me-1"></i> New Promotion
            </a>
        </div>
    </div>
</div>

{{-- PAGE FILTERS BAR --}}
<div class="page-filters-bar">
    <form method="GET" action="{{ route('admin.promotions.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Campaign Type</label>
            <select name="type" class="form-select">
                <option value="">All Campaign Types</option>
                <option value="accommodation" @selected(request('type')=='accommodation')>🏨 Accommodation</option>
                <option value="flights"       @selected(request('type')=='flights')>✈️ Flights</option>
                <option value="activities"    @selected(request('type')=='activities')>🎯 Activities</option>
                <option value="destination"   @selected(request('type')=='destination')>🗺️ Destination</option>
                <option value="general"       @selected(request('type')=='general')>📢 General</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="1" @selected(request('status')==='1')>🟢 Live / Active</option>
                <option value="0" @selected(request('status')==='0')>⚪ Inactive</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Search Banner</label>
            <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
            <button type="submit" class="btn-add-primary flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
            <a href="{{ route('admin.promotions.index') }}" class="btn-tbl-copy text-center" style="padding: 6px 14px;">Reset</a>
        </div>
    </form>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Summary Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="kpi-card" style="padding:12px 14px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10px; font-weight:700;">TOTAL CAMPAIGNS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] }}</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card" style="padding:12px 14px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10px; font-weight:700;">ACTIVE BANNERS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['active'] }}</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card" style="padding:12px 14px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10px; font-weight:700;">ACCOMMODATIONS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#1890ff; margin:0;">{{ $stats['accommodation'] }}</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card" style="padding:12px 14px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10px; font-weight:700;">FLIGHTS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#7367f0; margin:0;">{{ $stats['flights'] }}</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card" style="padding:12px 14px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10px; font-weight:700;">ACTIVITIES</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#00cfe8; margin:0;">{{ $stats['activities'] }}</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card" style="padding:12px 14px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10px; font-weight:700;">VENDOR PROMOS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['vendor_promos'] }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-bullhorn me-1 text-primary"></i> All Marketing Promotions &amp; Banners ({{ count($promotions) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search table..." onkeyup="filterTableSearch('promotionsTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="promotionsTable">
                <thead>
                    <tr>
                        <th style="width:45px;">#</th>
                        <th>Banner Preview</th>
                        <th>Title / Subtitle</th>
                        <th>Type</th>
                        <th>Call to Action</th>
                        <th>Status</th>
                        <th>Schedule</th>
                        <th>Sort</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                    <tr>
                        <td><strong>#{{ $promo->id }}</strong></td>
                        <td>
                            <div style="
                                background: {{ $promo->bg_color_end ? "linear-gradient(135deg, {$promo->bg_color}, {$promo->bg_color_end})" : $promo->bg_color }};
                                color: {{ $promo->text_color }};
                                padding: 6px 10px;
                                border-radius: 4px;
                                font-size: 11px;
                                font-weight: 700;
                                min-width: 90px;
                                text-align: center;
                                border: 1px solid rgba(0,0,0,0.1);
                            ">
                                @if($promo->badge_text)
                                <div style="font-size:9px;background:{{ $promo->badge_bg }};color:#000;border-radius:3px;padding:1px 4px;margin-bottom:3px;display:inline-block;">
                                    {{ $promo->badge_text }}
                                </div><br>
                                @endif
                                {{ Str::limit($promo->title, 18) }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600; font-size:13px; color:#1e293b;">{{ $promo->title }}</div>
                            @if($promo->subtitle)
                            <div style="font-size:11px; color:#8c8c8c;">{{ $promo->subtitle }}</div>
                            @endif
                            @if($promo->vendor_id)
                            <span class="badge bg-warning text-dark" style="font-size:10px;">Vendor: {{ $promo->vendor?->name }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeIcons = ['accommodation'=>'🏨','flights'=>'✈️','activities'=>'🎯','destination'=>'🗺️','general'=>'📢'];
                            @endphp
                            <span style="font-size:12.5px; font-weight:500;">{{ $typeIcons[$promo->type] ?? '📢' }} {{ ucfirst($promo->type) }}</span>
                        </td>
                        <td>
                            @if($promo->cta_text)
                            <div style="font-size:12px; font-weight:600;">{{ $promo->cta_text }}</div>
                            @endif
                            @if($promo->cta_link)
                            <div style="font-size:11px; color:#1890ff;">{{ Str::limit($promo->cta_link, 26) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($promo->is_live)
                                <span class="badge-status confirmed">🟢 Live</span>
                            @elseif($promo->status_label === 'Scheduled')
                                <span class="badge-status pending">⏰ Scheduled</span>
                            @elseif($promo->status_label === 'Expired')
                                <span class="badge-status cancelled">⌛ Expired</span>
                            @else
                                <span class="badge-status cancelled">⚪ Inactive</span>
                            @endif
                        </td>
                        <td style="font-size:11px;">
                            @if($promo->starts_at)
                            <div>From: {{ $promo->starts_at->format('d M Y') }}</div>
                            @endif
                            @if($promo->ends_at)
                            <div>To: {{ $promo->ends_at->format('d M Y') }}</div>
                            @endif
                            @if(!$promo->starts_at && !$promo->ends_at)
                            <span style="color:#8c8c8c;">Always on</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12px; font-weight:600;">{{ $promo->sort_order }}</span>
                            @if($promo->is_featured)
                            <span title="Featured"><i class="fa-solid fa-star" style="color:#f5c518; font-size:11px;"></i></span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.promotions.edit', $promo) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Promotion Banner
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.promotions.toggle', $promo) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid {{ $promo->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i> 
                                                {{ $promo->is_active ? 'Deactivate Banner' : 'Activate Banner' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.promotions.destroy', $promo) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this promotion?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Promotion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Promotions Available</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active or scheduled promotion banners found in the database.</p>
                                <a href="{{ route('admin.promotions.create') }}" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;">
                                    <i class="fa-solid fa-plus"></i> Create First Promotion
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($promotions, 'hasPages') && $promotions->hasPages())
        <div class="px-3 py-2 border-top">{{ $promotions->links() }}</div>
        @endif
    </div>

</div>
@endsection
