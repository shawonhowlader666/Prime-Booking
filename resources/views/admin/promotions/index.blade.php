@extends('layouts.admin')

@section('title', 'Promotions Manager — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Promotions &amp; Banner Campaigns</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button class="btn-tbl-copy" onclick="copyTableToClipboard('promotionsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('promotionsTable', 'Promotions')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('promotionsTable', 'Promotions')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('promotionsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('promotionsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addPromotionModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>New Promotion</span>
            </button></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Promotions Manager</strong>
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
                <option value="activities"    @selected(request('type')=='activities')>⛵ Activities &amp; Tours</option>
                <option value="hero_banner"   @selected(request('type')=='hero_banner')>⭐ Main Hero Banner</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="1" @selected(request('status')==='1')>🟢 Active Only</option>
                <option value="0" @selected(request('status')==='0')>🔴 Inactive</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Search Campaign</label>
            <input type="text" name="search" class="form-control" placeholder="Search by title, badge, or destination..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-sm-6 col-md-2 d-flex gap-2">
            <button type="submit" class="btn-add-primary flex-grow-1" style="border-radius:4px; height:34px;">Filter</button>
            <a href="{{ route('admin.promotions.index') }}" class="btn-export-csv d-inline-flex align-items-center justify-content-center" style="border-radius:4px; width:34px; height:34px; padding:0;" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
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

    {{-- Stockifly KPI Summary Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL CAMPAIGNS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] ?? 0 }}</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE BANNERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['active'] ?? 0 }}</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">HOTEL PROMOS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['accommodation'] ?? 0 }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">FLIGHT PROMOS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $stats['flights'] ?? 0 }}</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">HERO CAROUSEL</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $stats['hero'] ?? 0 }}</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ea5455; font-size:10.5px; font-weight:700;">VENDOR BANNERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ea5455; margin:0;">{{ $stats['vendor_promos'] ?? 0 }}</p>
                <div class="kpi-accent-bar" style="background:#ea5455;"></div>
            </div>
        </div>
    </div>

    {{-- Promotions Data Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Active &amp; Scheduled Promotional Banners</h6>
            <span class="live-feed-badge">Live Banners Feed</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" id="promotionsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;">#</th>
                        <th>Banner Preview</th>
                        <th>Title &amp; Subtitle</th>
                        <th>Campaign Type</th>
                        <th>Badge Text</th>
                        <th>CTA Action</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                    <tr>
                        <td style="text-align:center; font-weight:600; color:#8c8c8c;">{{ $promo->id }}</td>
                        <td>
                            <div style="width:120px; height:48px; border-radius:6px; overflow:hidden; border:1px solid #e2e8f0; position:relative; background:{{ $promo->bg_color ?? '#1e293b' }};">
                                @if($promo->image_url)
                                    <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-white font-monospace" style="font-size:10px; padding:4px;">
                                        {{ Str::limit($promo->title, 15) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong style="color:#0f172a; font-size:13.5px; display:block;">{{ $promo->title }}</strong>
                            <span style="color:#64748b; font-size:11.5px;">{{ Str::limit($promo->subtitle, 45) }}</span>
                        </td>
                        <td>
                            @php
                                $typeLabels = [
                                    'accommodation' => ['🏨 Accommodation', 'background:#e6f7ff; color:#1890ff; border:1px solid #91d5ff;'],
                                    'flights'       => ['✈️ Flights',       'background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f;'],
                                    'activities'    => ['⛵ Activities',    'background:#fff7e6; color:#fa8c16; border:1px solid #ffd591;'],
                                    'hero_banner'   => ['⭐ Main Hero',     'background:#f9f0ff; color:#722ed1; border:1px solid #d3ade6;'],
                                ];
                                $tMeta = $typeLabels[$promo->type] ?? ['Campaign', 'background:#f5f5f5; color:#595959;'];
                            @endphp
                            <span style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; {{ $tMeta[1] }}">
                                {{ $tMeta[0] }}
                            </span>
                        </td>
                        <td>
                            @if($promo->badge_text)
                                <span style="font-size:11px; font-weight:700; background:{{ $promo->badge_bg ?? '#ff4d4f' }}; color:#ffffff; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                                    {{ $promo->badge_text }}
                                </span>
                            @else
                                <span style="color:#cbd5e1; font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ $promo->cta_link ?? '#' }}" target="_blank" style="font-size:12px; color:var(--primary); font-weight:600; text-decoration:none;">
                                {{ $promo->cta_text ?? 'View Deal' }} <i class="fa-solid fa-arrow-up-right-from-square style="font-size:10px;"></i>
                            </a>
                        </td>
                        <td style="font-weight:700; color:#334155; font-size:13px;">{{ $promo->sort_order }}</td>
                        <td>
                            <form action="{{ route('admin.promotions.toggle', $promo) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0" title="Click to toggle status">
                                    <span class="badge-status {{ $promo->is_active ? 'confirmed' : 'cancelled' }}">
                                        {{ $promo->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.promotions.edit', $promo) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Promotion
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.promotions.toggle', $promo) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid fa-power-off me-2"></i> {{ $promo->is_active ? 'Deactivate Banner' : 'Activate Banner' }}
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
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addPromotionModal">
                                    <i class="fa-solid fa-plus"></i> Create First Promotion
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$promotions" :perPage="15" />
    </div>

</div>

{{-- CREATE PROMOTION MODAL --}}
<div class="modal fade" id="addPromotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-bullhorn text-primary me-2"></i> Create New Promotion Banner
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.promotions.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Title <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Cox's Bazar Summer Beach Special" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Subtitle / Tagline</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="e.g. Up to 40% OFF on Luxury Resorts with free breakfast" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Campaign Type <span style="color:#ff4d4f;">*</span></label>
                            <select name="type" class="form-select" required style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="accommodation">🏨 Accommodation (Hotels &amp; Resorts)</option>
                                <option value="flights">✈️ Flights &amp; Airlines</option>
                                <option value="activities">⛵ Activities &amp; Tours</option>
                                <option value="hero_banner">⭐ Main Hero Carousel</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Badge Text</label>
                            <input type="text" name="badge_text" class="form-control" placeholder="e.g. 40% OFF or HOT DEAL" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">CTA Button Text</label>
                            <input type="text" name="cta_text" class="form-control" placeholder="e.g. Book Resort Now" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">CTA Target Link</label>
                            <input type="text" name="cta_link" class="form-control" placeholder="e.g. /hotels?city=Cox%27s+Bazar" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Background Image URL</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://images.unsplash.com/photo-..." style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Promotion <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
