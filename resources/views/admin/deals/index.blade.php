@extends('layouts.admin')

@section('title', 'Deals & Special Offers — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Deals &amp; Offers</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Deals &amp; Special Promotional Offers</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage early-bird discounts, flash sales, and seasonal promotional rates</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('dealsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('dealsTable', 'Deals')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('dealsTable', 'Deals')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('dealsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('dealsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <a href="{{ route('admin.deals.create') }}" class="btn-add-primary" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add Special Deal</span>
            </a>
        </div>
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
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL DEALS &amp; OFFERS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ count($deals) }} Listed</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">ACTIVE LIVE DEALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $deals->where('is_active', true)->count() }} Active</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">HOTEL &amp; RESORT DEALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $deals->where('type', 'hotel')->count() }} Deals</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">FLIGHT &amp; PACKAGE DEALS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#7367f0; margin:0;">{{ $deals->whereIn('type', ['flight', 'package', 'activity'])->count() }} Deals</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-tag me-2 text-primary"></i> Time-Limited Deals &amp; Offers ({{ count($deals) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search deals..." onkeyup="filterTableSearch('dealsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="dealsTable">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:260px;">Deal Preview &amp; Title</th>
                        <th>Category</th>
                        <th>Discount Badge</th>
                        <th>Pricing (BDT)</th>
                        <th>Validity Period</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deals as $deal)
                    <tr>
                        <td><strong>#{{ $deal->id }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="{{ $deal->image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=300&q=80' }}" alt="" style="width: 52px; height: 38px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                <div>
                                    <div style="font-weight:700; font-size:13px; color:#1e293b;">{{ $deal->title }}</div>
                                    @if($deal->subtitle)
                                    <small style="color:#64748b; font-size:11px;">{{ $deal->subtitle }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border" style="font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px;">{{ ucfirst($deal->type) }}</span></td>
                        <td><span class="badge bg-warning text-dark" style="font-size:11px; font-weight:800; padding:4px 8px; border-radius:4px;">{{ $deal->badge_text ?: ($deal->discount_pct ? $deal->discount_pct.'% OFF' : 'SPECIAL DEAL') }}</span></td>
                        <td>
                            @if($deal->sale_price)
                            <strong style="color:#28c76f; font-size:13.5px;">৳ {{ number_format($deal->sale_price) }} BDT</strong>
                            @if($deal->original_price)
                            <del style="font-size:11px; color:#94a3b8; margin-left:4px;">৳ {{ number_format($deal->original_price) }}</del>
                            @endif
                            @else
                            <span style="color:#94a3b8; font-size:11px;">Special Rate</span>
                            @endif
                        </td>
                        <td>
                            @if($deal->valid_until)
                            <span style="font-size:11.5px; font-weight:500; color:#475569;"><i class="fa-solid fa-clock me-1 text-primary"></i> Until {{ $deal->valid_until->format('d M Y') }}</span>
                            @else
                            <span style="font-size:11.5px; color:#64748b;">Always Active</span>
                            @endif
                        </td>
                        <td>
                            @if($deal->is_active)
                            <span class="badge-status confirmed">🟢 Active</span>
                            @else
                            <span class="badge-status cancelled">⚪ Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.deals.edit', $deal) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Special Deal
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.deals.destroy', $deal) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this deal?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Special Deal
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
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Deals &amp; Special Offers Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active special offer campaigns listed in the database.</p>
                                <a href="{{ route('admin.deals.create') }}" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;">
                                    <i class="fa-solid fa-plus"></i> Add First Special Deal
                                </a>
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
@endsection
