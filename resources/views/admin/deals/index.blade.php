@extends('layouts.admin')

@section('title', 'Deals & Special Offers — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Deals &amp; Special Promotional Offers</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button class="btn-tbl-copy" onclick="copyTableToClipboard('dealsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('dealsTable', 'Deals')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('dealsTable', 'Deals')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('dealsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('dealsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button type="button" class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addDealModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add Special Deal</span>
            </button></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Deals &amp; Offers</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stockifly KPI Summary Cards Row --}}
    @php
        $dealColl = method_exists($deals, 'getCollection') ? $deals->getCollection() : collect($deals);
        $totalDeals   = method_exists($deals, 'total') ? $deals->total() : $dealColl->count();
        $activeDeals  = $dealColl->where('is_active', true)->count();
        $hotelDeals   = $dealColl->where('type', 'hotel')->count();
        $flightDeals  = $dealColl->whereIn('type', ['flight', 'package'])->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL DEALS &amp; OFFERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $totalDeals }} Active</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE LIVE DEALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $activeDeals }} Visible</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">HOTEL &amp; RESORT DEALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $hotelDeals }} Listed</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">FLIGHT &amp; PACKAGE DEALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $flightDeals }} Listed</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-tag me-2 text-primary"></i> Active Special Promotional Offers ({{ count($deals) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search deals..." onkeyup="filterTableSearch('dealsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="dealsTable">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;">#</th>
                        <th>Deal Title &amp; Subtitle</th>
                        <th>Category</th>
                        <th>Discount Badge</th>
                        <th>Original Price</th>
                        <th>Sale Price</th>
                        <th>Validity Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($deals as $deal)
                    <tr>
                        <td style="text-align:center; font-weight:600; color:#8c8c8c;">{{ $deal->id }}</td>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $deal->title }}</strong>
                            <span style="font-size:11.5px; color:#64748b;">{{ Str::limit($deal->subtitle, 50) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border" style="font-size:11px; text-transform:uppercase; font-weight:700; padding:4px 8px; border-radius:4px;">
                                {{ ucfirst($deal->type ?? 'hotel') }}
                            </span>
                        </td>
                        <td>
                            @if($deal->badge_text || $deal->discount_pct)
                                <span class="badge bg-danger text-white" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px; background:#ff4d4f;">
                                    {{ $deal->badge_text ?? ($deal->discount_pct . '% OFF') }}
                                </span>
                            @else
                                <span style="color:#cbd5e1; font-size:12px;">—</span>
                            @endif
                        </td>
                        <td style="font-size:12.5px; color:#64748b; text-decoration:line-through;">
                            {{ $deal->original_price ? '৳ ' . number_format($deal->original_price) : '—' }}
                        </td>
                        <td style="font-size:13.5px; font-weight:700; color:#28c76f;">
                            {{ $deal->sale_price ? '৳ ' . number_format($deal->sale_price) . ' BDT' : 'Special Rate' }}
                        </td>
                        <td style="font-size:12px; color:#64748b;">
                            @if($deal->valid_until)
                                <i class="fa-solid fa-clock me-1 text-warning"></i> {{ \Carbon\Carbon::parse($deal->valid_until)->format('M d, Y') }}
                            @else
                                <span style="color:#8c8c8c;">Ongoing</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.deals.toggle', $deal->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0" title="Click to toggle status">
                                    <span class="badge-status {{ $deal->is_active ? 'confirmed' : 'cancelled' }}">
                                        {{ $deal->is_active ? 'Active' : 'Inactive' }}
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
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.deals.edit', $deal->id) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Deal
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.deals.toggle', $deal->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid fa-power-off me-2"></i> {{ $deal->is_active ? 'Deactivate Deal' : 'Activate Deal' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.deals.destroy', $deal->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this deal?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Deal
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
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Special Deals Listed</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active promotional deals or flash sales created yet.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addDealModal">
                                    <i class="fa-solid fa-plus"></i> Create First Deal
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$deals" :perPage="15" />
    </div>

</div>

{{-- CREATE DEAL MODAL --}}
<div class="modal fade" id="addDealModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-tag text-primary me-2"></i> Create Special Deal &amp; Discount Offer
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.deals.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Title <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Flash Sale: Cox's Bazar Luxury Resorts" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Subtitle / Tagline</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="e.g. Exclusive beach resort discount for Prime members" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Discount %</label>
                            <input type="number" name="discount_pct" class="form-control" placeholder="e.g. 25" step="0.1" min="0" max="100" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Original Price (৳)</label>
                            <input type="number" name="original_price" class="form-control" placeholder="e.g. 10000" step="0.01" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sale Price (৳)</label>
                            <input type="number" name="sale_price" class="form-control" placeholder="e.g. 7500" step="0.01" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Deal Type <span style="color:#ff4d4f;">*</span></label>
                            <select name="type" class="form-select" required style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="hotel">Hotel &amp; Resort</option>
                                <option value="flight">Flight</option>
                                <option value="package">Tour Package</option>
                                <option value="activity">Activity</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Badge Text</label>
                            <input type="text" name="badge_text" class="form-control" placeholder="e.g. 25% OFF or FLASH SALE" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Valid Until (Expiry Date)</label>
                            <input type="datetime-local" name="valid_until" class="form-control" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Target Link URL</label>
                            <input type="text" name="link_url" class="form-control" placeholder="e.g. /search?destination=Cox%27s+Bazar" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Deal <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
