@extends('layouts.admin')

@section('title', 'Deals & Special Offers — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Deals &amp; Offers</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <div>
            <h1 class="page-title">Deals &amp; Special Offers</h1>
            <span style="font-size:12px; color:#8c8c8c;">Manage time-limited promotions &amp; promotional discounts</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('dealsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('dealsTable', 'Deals')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('dealsTable', 'Deals')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('dealsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('dealsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <a href="{{ route('admin.deals.create') }}" class="btn-add-primary">
                <i class="fa-solid fa-plus me-1"></i> Add Special Deal
            </a>
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

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-tag me-1 text-primary"></i> Time-Limited Deals &amp; Offers ({{ count($deals) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search deals..." onkeyup="filterTableSearch('dealsTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="dealsTable">
                <thead>
                    <tr>
                        <th style="width:50px;">Order</th>
                        <th>Preview</th>
                        <th>Title &amp; Subtitle</th>
                        <th>Category</th>
                        <th>Discount Badge</th>
                        <th>Pricing (BDT)</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deals as $deal)
                    <tr>
                        <td><strong>#{{ $deal->sort_order }}</strong></td>
                        <td>
                            @if($deal->image_url)
                            <img src="{{ $deal->image_url }}" alt="{{ $deal->title }}" style="width:55px; height:38px; object-fit:cover; border-radius:4px; border:1px solid #e2e8f0;">
                            @else
                            <div style="width:55px; height:38px; background:#f0f2f5; border-radius:4px; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-tag" style="color:#ccc;"></i></div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600; font-size:13px; color:#1e293b;">{{ $deal->title }}</div>
                            <small style="color:#8c8c8c;">{{ $deal->subtitle }}</small>
                        </td>
                        <td><span class="badge bg-info text-dark" style="font-size:11px;">{{ ucfirst($deal->type) }}</span></td>
                        <td><span class="badge bg-warning text-dark" style="font-size:11px;">{{ $deal->badge_text ?: $deal->discount_pct.'% OFF' }}</span></td>
                        <td>
                            @if($deal->sale_price)
                            <strong class="text-success" style="font-size:13px;">BDT {{ number_format($deal->sale_price) }}</strong>
                            @if($deal->original_price)
                            <del style="font-size:11px; color:#8c8c8c; margin-left:4px;">BDT {{ number_format($deal->original_price) }}</del>
                            @endif
                            @else
                            <span style="color:#8c8c8c;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($deal->valid_until)
                            <small style="font-weight:500;">{{ $deal->valid_until->format('d M Y, H:i') }}</small>
                            @else
                            <span style="font-size:11px; color:#8c8c8c;">No Expiry</span>
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
                        <td colspan="9" class="text-center py-5" style="background:#ffffff;">
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

        @if(method_exists($deals, 'hasPages') && $deals->hasPages())
        <div class="px-3 py-2 border-top">{{ $deals->links() }}</div>
        @endif
    </div>

</div>
@endsection
