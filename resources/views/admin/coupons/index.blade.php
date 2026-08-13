@extends('layouts.admin')
@section('title', 'Promo Coupons & Discounts | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Promo Coupons</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Promo Coupons &amp; Discount Management</h1>
            <span style="font-size:12.5px; color:#64748b;">Create checkout promo codes, minimum spend rules, and usage limits</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('couponsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('couponsTable', 'Promo_Coupons')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('couponsTable', 'Promo_Coupons')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('couponsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('couponsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Create New Coupon</span>
            </button>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-4" style="border-radius:4px; padding:12px 16px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI Summary --}}
    @php
        $couponColl = method_exists($coupons, 'getCollection') ? $coupons->getCollection() : collect($coupons);
        $totalCouponsCount  = method_exists($coupons, 'total') ? $coupons->total() : $couponColl->count();
        $activeCouponsCount = $couponColl->where('status', 'active')->count();
        $sumRedemptions     = $couponColl->sum('used_count');
        $percCouponsCount   = $couponColl->where('type', 'percentage')->count();
    @endphp
    {{-- Stockifly KPI Summary Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10px; font-weight:700;">TOTAL COUPONS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#1e293b; margin:0;">{{ $totalCouponsCount }}</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10px; font-weight:700;">ACTIVE COUPONS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#28c76f; margin:0;">{{ $activeCouponsCount }}</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10px; font-weight:700;">TOTAL REDEMPTIONS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#ff9f43; margin:0;">{{ $sumRedemptions }}</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10px; font-weight:700;">% DISCOUNT COUPONS</p>
                <p class="kpi-value" style="font-size:18px; font-weight:800; color:#7367f0; margin:0;">{{ $percCouponsCount }}</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-ticket me-2 text-primary"></i> Active Promotional Discount Codes ({{ count($coupons) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search coupons..." onkeyup="filterTableSearch('couponsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="couponsTable">
                <thead>
                    <tr>
                        <th>Coupon Code</th>
                        <th>Discount Value</th>
                        <th>Min Spend</th>
                        <th>Usage Progress</th>
                        <th>Expiration</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($coupons as $c)
                    <tr>
                        <td>
                            <strong style="font-size:13.5px; color:var(--primary); font-family:monospace; letter-spacing:1.5px; background:#e6f7ff; padding:4px 10px; border-radius:4px; border:1px solid #bae7ff;">{{ $c->code }}</strong>
                        </td>
                        <td>
                            <strong style="color:#28c76f; font-size:13.5px;">
                                {{ $c->type == 'percentage' ? $c->amount . '%' : '৳ ' . number_format($c->amount) . ' BDT' }} OFF
                            </strong>
                        </td>
                        <td style="font-size:12.5px; color:#334155;">
                            {{ $c->min_spend ? '৳ ' . number_format($c->min_spend) . ' BDT' : 'No minimum' }}
                        </td>
                        <td>
                            <strong style="font-size:12.5px;">{{ $c->used_count ?? 0 }}</strong>
                            <span style="color:#8c8c8c; font-size:11px;"> / {{ $c->usage_limit ?? '∞' }} uses</span>
                            @if($c->usage_limit)
                                <div style="height:4px; background:#f1f5f9; border-radius:2px; margin-top:4px; width:100px;">
                                    <div style="height:100%; background:#1890ff; border-radius:2px; width:{{ min(100, (($c->used_count ?? 0) / $c->usage_limit) * 100) }}%;"></div>
                                </div>
                            @endif
                        </td>
                        <td style="font-size:12px;">
                            @if($c->expires_at)
                                @if(\Carbon\Carbon::parse($c->expires_at)->isPast())
                                    <span style="color:#ff4d4f; font-weight:600;"><i class="fa-solid fa-clock me-1"></i>Expired {{ \Carbon\Carbon::parse($c->expires_at)->diffForHumans() }}</span>
                                @else
                                    <span style="color:#28c76f; font-weight:600;"><i class="fa-solid fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($c->expires_at)->format('M d, Y') }}</span>
                                @endif
                            @else
                                <span style="color:#8c8c8c;">No expiry</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ ($c->status ?? 'active') == 'active' ? 'confirmed' : 'cancelled' }}">
                                {{ ucfirst($c->status ?? 'Active') }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3"
                                            onclick="openEditModal({{ $c->id }}, '{{ $c->code }}', '{{ $c->type }}', {{ $c->amount }}, {{ $c->min_spend ?? 0 }}, {{ $c->usage_limit ?? 0 }}, '{{ $c->expires_at ?? '' }}', '{{ $c->status ?? 'active' }}')">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Coupon Code
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.coupons.toggle', $c->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid fa-power-off me-2"></i> {{ ($c->status ?? 'active') == 'active' ? 'Deactivate Coupon' : 'Activate Coupon' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete coupon {{ $c->code }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Coupon
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Promo Coupons Yet</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active discount codes created in the system database.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addCouponModal">
                                    <i class="fa-solid fa-plus"></i> Create First Coupon
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$coupons" :perPage="20" />
    </div>

</div>

{{-- CREATE COUPON MODAL --}}
<div class="modal fade" id="addCouponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-ticket text-primary me-2"></i> Create Promo Coupon
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Coupon Code <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="code" id="newCouponCode" class="form-control" placeholder="e.g. SAVE10" required style="text-transform:uppercase; font-weight:700; font-family:monospace; font-size:14px; letter-spacing:1.5px; height:38px; border-radius:4px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateCode()" style="white-space:nowrap; padding:0 14px; font-size:12.5px; height:38px; border-radius:4px;">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto
                            </button>
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Discount Type</label>
                            <select name="type" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (BDT)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Discount Value <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" name="amount" class="form-control" placeholder="e.g. 10 or 500" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Min Spend (BDT)</label>
                            <input type="number" name="min_spend" class="form-control" placeholder="e.g. 2000" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control" placeholder="e.g. 100" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Expiration Date</label>
                        <input type="date" name="expires_at" class="form-control" min="{{ date('Y-m-d') }}" style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Save Coupon <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT COUPON MODAL --}}
<div class="modal fade" id="editCouponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-pen text-primary me-2"></i> Edit Coupon — <span id="editModalCode" style="font-family:monospace; color:var(--primary);"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCouponForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Coupon Code</label>
                        <input type="text" name="code" id="editCode" class="form-control" required style="font-family:monospace; font-size:14px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; height:38px; border-radius:4px;">
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Discount Type</label>
                            <select name="type" id="editType" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (BDT)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Discount Value</label>
                            <input type="number" name="amount" id="editAmount" class="form-control" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Min Spend (BDT)</label>
                            <input type="number" name="min_spend" id="editMinSpend" class="form-control" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Usage Limit</label>
                            <input type="number" name="usage_limit" id="editUsageLimit" class="form-control" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Expiration Date</label>
                            <input type="date" name="expires_at" id="editExpiresAt" class="form-control" style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Status</label>
                            <select name="status" id="editStatus" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Update Coupon <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openEditModal(id, code, type, amount, minSpend, usageLimit, expiresAt, status) {
    document.getElementById('editCouponForm').action = '/admin/coupons/' + id;
    document.getElementById('editModalCode').textContent  = code;
    document.getElementById('editCode').value       = code;
    document.getElementById('editType').value       = type;
    document.getElementById('editAmount').value     = amount;
    document.getElementById('editMinSpend').value   = minSpend || '';
    document.getElementById('editUsageLimit').value = usageLimit || '';
    document.getElementById('editExpiresAt').value  = expiresAt || '';
    document.getElementById('editStatus').value     = status || 'active';
    new bootstrap.Modal(document.getElementById('editCouponModal')).show();
}

function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'PA';
    for (let i = 0; i < 6; i++) code += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('newCouponCode').value = code;
}
</script>
@endsection
