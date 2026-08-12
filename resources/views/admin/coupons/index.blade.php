@extends('layouts.admin')
@section('title', 'Promo Coupons & Discounts | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Marketing</span>
        <span class="sep">-</span><strong style="color:#333;">Promo Coupons</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Promo Coupons &amp; Discount Management</h1>
        <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
            <i class="fa-solid fa-plus"></i> Create New Coupon
        </button>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
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
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:14px 16px;">
                <div class="kpi-icon" style="background:#1890ff; width:34px; height:34px; font-size:14px; border-radius:8px; margin-bottom:8px;"><i class="fa-solid fa-ticket"></i></div>
                <p class="kpi-value" style="font-size:18px;">{{ $totalCouponsCount }}</p>
                <p class="kpi-label">Total Coupons</p>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:14px 16px;">
                <div class="kpi-icon" style="background:#28c76f; width:34px; height:34px; font-size:14px; border-radius:8px; margin-bottom:8px;"><i class="fa-solid fa-check-circle"></i></div>
                <p class="kpi-value" style="font-size:18px;">{{ $activeCouponsCount }}</p>
                <p class="kpi-label">Active Coupons</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:14px 16px;">
                <div class="kpi-icon" style="background:#ff9f43; width:34px; height:34px; font-size:14px; border-radius:8px; margin-bottom:8px;"><i class="fa-solid fa-fire"></i></div>
                <p class="kpi-value" style="font-size:18px;">{{ $sumRedemptions }}</p>
                <p class="kpi-label">Total Redemptions</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:14px 16px;">
                <div class="kpi-icon" style="background:#7367f0; width:34px; height:34px; font-size:14px; border-radius:8px; margin-bottom:8px;"><i class="fa-solid fa-percent"></i></div>
                <p class="kpi-value" style="font-size:18px;">{{ $percCouponsCount }}</p>
                <p class="kpi-label">% Discount Coupons</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- Coupons Table --}}
    <div class="data-table-card">
        <div class="data-table-card-header">
            <h6>Active Promotional Discount Codes</h6>
            <span class="live-feed-badge">Active Marketing</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-stockifly" style="width:100%;">
                <thead>
                    <tr>
                        <th>Coupon Code</th>
                        <th>Discount Value</th>
                        <th>Min Spend</th>
                        <th>Usage</th>
                        <th>Expiration</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($coupons as $c)
                    <tr>
                        <td>
                            <strong style="font-size:14px; color:var(--primary); font-family:monospace; letter-spacing:1.5px; background:#e6f7ff; padding:3px 8px; border-radius:4px;">{{ $c->code }}</strong>
                        </td>
                        <td>
                            <strong style="color:#28c76f; font-size:13px;">
                                {{ $c->type == 'percentage' ? $c->amount . '%' : 'BDT ' . number_format($c->amount) }} OFF
                            </strong>
                        </td>
                        <td style="font-size:12.5px; color:#334155;">
                            {{ $c->min_spend ? 'BDT ' . number_format($c->min_spend) : 'No minimum' }}
                        </td>
                        <td>
                            <strong style="font-size:12.5px;">{{ $c->used_count ?? 0 }}</strong>
                            <span style="color:#8c8c8c; font-size:11px;"> / {{ $c->usage_limit ?? '∞' }} uses</span>
                            @if($c->usage_limit)
                                <div style="height:3px; background:#f0f0f0; border-radius:2px; margin-top:4px; width:80px;">
                                    <div style="height:100%; background:#1890ff; border-radius:2px; width:{{ min(100, (($c->used_count ?? 0) / $c->usage_limit) * 100) }}%;"></div>
                                </div>
                            @endif
                        </td>
                        <td style="font-size:12px;">
                            @if($c->expires_at)
                                @if(\Carbon\Carbon::parse($c->expires_at)->isPast())
                                    <span style="color:#ff4d4f;"><i class="fa-solid fa-clock me-1"></i>Expired {{ \Carbon\Carbon::parse($c->expires_at)->diffForHumans() }}</span>
                                @else
                                    <span style="color:#28c76f;"><i class="fa-solid fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($c->expires_at)->format('M d, Y') }}</span>
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
                        <td colspan="7" style="text-align:center; padding:40px; color:#8c8c8c;">
                            <i class="fa-solid fa-ticket" style="font-size:32px; color:#d9d9d9; display:block; margin-bottom:10px;"></i>
                            <strong style="display:block; font-size:14px; color:#1e293b; margin-bottom:6px;">No Promo Coupons Yet</strong>
                            <span style="font-size:12px;">Click "Create New Coupon" to add your first discount code.</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($coupons, 'links'))
            <div style="padding:10px 16px; border-top:1px solid #f0f0f0; font-size:12px;">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     CREATE COUPON MODAL
     ═══════════════════════════════════════════════ --}}
<div class="modal fade" id="addCouponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.18);">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0; padding:16px 20px;">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#1e293b;">
                    <i class="fa-solid fa-ticket text-primary me-2"></i> Create Promo Coupon
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Coupon Code <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="code" id="newCouponCode" class="form-control" placeholder="e.g. SAVE10" required style="text-transform:uppercase; font-weight:700; font-family:monospace; font-size:15px; letter-spacing:2px;">
                            <button type="button" class="btn-export-csv" onclick="generateCode()" style="white-space:nowrap; padding:0 12px;">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Auto
                            </button>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Discount Type</label>
                            <select name="type" class="form-select">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (BDT)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Discount Value <span style="color:#ff4d4f;">*</span></label>
                            <input type="number" name="amount" class="form-control" placeholder="e.g. 10 or 500" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Min Spend (BDT)</label>
                            <input type="number" name="min_spend" class="form-control" placeholder="e.g. 2000">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control" placeholder="e.g. 100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Expiration Date</label>
                        <input type="date" name="expires_at" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:12px 20px;">
                    <button type="button" class="btn-export-csv" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-add-primary">Save Coupon <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     EDIT COUPON MODAL
     ═══════════════════════════════════════════════ --}}
<div class="modal fade" id="editCouponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:10px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.18);">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0; padding:16px 20px; background:linear-gradient(135deg,#e6f7ff,#f0f9ff);">
                <h6 class="modal-title fw-bold" style="font-size:15px; color:#1e293b;">
                    <i class="fa-solid fa-pen text-primary me-2"></i> Edit Coupon — <span id="editModalCode" style="font-family:monospace; color:var(--primary);"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCouponForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Coupon Code</label>
                        <input type="text" name="code" id="editCode" class="form-control" required style="font-family:monospace; font-size:15px; font-weight:700; letter-spacing:2px; text-transform:uppercase;">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Discount Type</label>
                            <select name="type" id="editType" class="form-select">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (BDT)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Discount Value</label>
                            <input type="number" name="amount" id="editAmount" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Min Spend (BDT)</label>
                            <input type="number" name="min_spend" id="editMinSpend" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Usage Limit</label>
                            <input type="number" name="usage_limit" id="editUsageLimit" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Expiration Date</label>
                            <input type="date" name="expires_at" id="editExpiresAt" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:11px; font-weight:600; color:#8c8c8c; text-transform:uppercase;">Status</label>
                            <select name="status" id="editStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0; padding:12px 20px;">
                    <button type="button" class="btn-export-csv" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-add-primary">Update Coupon <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Pre-fill edit modal with coupon data
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

// Auto-generate coupon code
function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'PA';
    for (let i = 0; i < 6; i++) code += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('newCouponCode').value = code;
}
</script>
@endsection

