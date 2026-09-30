@extends('layouts.vendor')

@section('title', 'Financial Accounts & Statements — PRIME BOOKING Partner')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Financial Accounts &amp; Payout Statements</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('vendor.accounts.statement.print') }}" target="_blank" class="btn-tbl-col" style="text-decoration:none;">
                <i class="fa-solid fa-print me-1"></i> Print Statement
            </a>
            <a href="{{ route('vendor.payouts.index') }}" class="btn-add-primary ms-1" style="text-decoration:none;">
                <i class="fa-solid fa-money-bill-transfer me-1"></i> Request Payout
            </a>
        </div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Accounts &amp; Statements</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">AVAILABLE FOR WITHDRAWAL</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#28c76f; margin:0;">৳ {{ number_format($finance['withdrawable_balance'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-circle-check me-1 text-success"></i> Matured completed stays</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#0ea5e9; font-size:10.5px; font-weight:700;">UPCOMING IN-ESCROW</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#0ea5e9; margin:0;">৳ {{ number_format($finance['escrow_future_balance'] ?? 0, 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-clock-rotate-left me-1 text-info"></i> Releases on guest checkout</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#f0f9ff; color:#0ea5e9; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#0ea5e9;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#1890ff; font-size:10.5px; font-weight:700;">GROSS BOOKING TURNOVER</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">৳ {{ number_format($finance['gross_revenue'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-receipt me-1"></i> {{ $finance['total_bookings'] }} Completed Orders</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">SETTLED PAYOUTS</p>
                        <p class="kpi-value" style="font-size:22px; font-weight:800; color:#7367f0; margin:0;">৳ {{ number_format($finance['payouts_paid'], 2) }}</p>
                        <span class="text-muted" style="font-size:11px;"><i class="fa-solid fa-percent me-1 text-warning"></i> Comm: ৳{{ number_format($finance['commission_paid'], 2) }}</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:50%; background:#f0eefc; color:#7367f0; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
    </div>

    {{-- LEDGER DATA TABLE --}}
    <div class="data-table-card p-0">

        {{-- TOOLBAR: Default mode vs Bulk selection mode --}}
        <div class="saas-table-toolbar" style="padding:14px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">

            {{-- LEFT: Title OR Bulk action bar --}}
            <div id="toolbarDefault" style="display:flex; align-items:center; gap:10px;">
                <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-list-check text-primary me-2"></i>
                    Financial Audit Ledger &amp; Earnings Records
                </h6>
            </div>

            {{-- Bulk Action Bar (hidden initially, shows on row selection) --}}
            <div id="bulkActionBar" style="display:none; align-items:center; gap:8px; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; gap:8px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:6px 14px;">
                    <i class="fa-solid fa-square-check" style="color:#2067e1; font-size:14px;"></i>
                    <span id="selectedCountBadge" style="font-size:13px; font-weight:700; color:#1e40af;">0 Selected</span>
                </div>
                <button onclick="exportSelectedCSV()" style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-size:12.5px; font-weight:600; color:#374151; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                    <i class="fa-solid fa-file-csv" style="color:#16a34a;"></i> Export CSV
                </button>
                <button onclick="copySelectedRows()" style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-size:12.5px; font-weight:600; color:#374151; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                    <i class="fa-regular fa-copy" style="color:#2067e1;"></i> Copy
                </button>
                <button onclick="printSelectedRows()" style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-size:12.5px; font-weight:600; color:#374151; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                    <i class="fa-solid fa-print" style="color:#7367f0;"></i> Print
                </button>
                <button onclick="clearAllSelections()" style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#fff; border:1px solid #fecaca; border-radius:8px; font-size:12.5px; font-weight:600; color:#dc2626; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                    <i class="fa-solid fa-xmark"></i> Deselect All
                </button>
            </div>

            {{-- RIGHT: Smart Search --}}
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px; pointer-events:none;"></i>
                    <input type="text"
                           id="ledgerSearchInput"
                           placeholder="Search transactions, hotels..."
                           oninput="filterLedgerTable(this.value)"
                           style="padding:8px 12px 8px 36px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; color:#1e293b; background:#f8fafc; width:260px; outline:none; transition:all 0.2s;"
                           onfocus="this.style.borderColor='#2067e1'; this.style.background='#fff'; this.style.boxShadow='0 0 0 3px rgba(32,103,225,0.08)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'">
                </div>
                <button onclick="clearLedgerSearch()" id="ledgerClearBtn" style="display:none; padding:8px 12px; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; font-weight:600; color:#64748b; cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table stockifly-data-table align-middle mb-0" id="vendorLedgerTable">
                <thead>
                    <tr>
                        {{-- Master checkbox --}}
                        <th style="width:42px; text-align:center; padding:12px 14px;">
                            <input type="checkbox" id="masterCheckbox" onchange="toggleMasterSelect(this)"
                                   style="width:16px; height:16px; cursor:pointer; accent-color:#2067e1; border-radius:4px;">
                        </th>
                        <th>TXN Ref</th>
                        <th>Hotel / Booking</th>
                        <th style="text-align:right;">Gross (BDT)</th>
                        <th style="text-align:right;">Commission (12%)</th>
                        <th style="text-align:right;">Net Earnings</th>
                        <th style="text-align:center;">Method</th>
                        <th style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    @forelse($ledgers as $l)
                    <tr class="ledger-row" data-txn="{{ $l->txn_reference }}" data-hotel="{{ $l->property?->name ?? 'Hotel Reservation' }}" data-gross="{{ number_format($l->gross_amount, 2) }}" data-commission="{{ number_format($l->commission_amount, 2) }}" data-net="{{ number_format($l->net_amount, 2) }}" data-method="{{ strtoupper($l->payment_method ?? 'PAY_AT_HOTEL') }}" data-status="{{ ucfirst($l->status) }}">
                        <td style="padding:14px; text-align:center;">
                            <input type="checkbox" class="row-checkbox"
                                   onchange="onRowCheckboxChange()"
                                   style="width:16px; height:16px; cursor:pointer; accent-color:#2067e1; border-radius:4px;">
                        </td>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $l->txn_reference }}</strong>
                            <span class="order-date">{{ $l->created_at ? $l->created_at->format('d M Y, h:i A') : '' }}</span>
                        </td>
                        <td style="padding:14px 16px;">
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $l->property?->name ?? 'Hotel Reservation' }}</strong>
                            <small class="text-muted" style="font-size:11px;">#{{ $l->booking?->booking_code ?? 'DIRECT' }}</small>
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#0f172a; font-size:13px;">
                            ৳ {{ number_format($l->gross_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:700; color:#ff9f43; font-size:13px;">
                            -৳ {{ number_format($l->commission_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:right; font-weight:800; color:#28c76f; font-size:13.5px;">
                            ৳ {{ number_format($l->net_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <span class="badge-gateway" style="font-size:11px;">
                                {{ strtoupper($l->payment_method ?? 'PAY_AT_HOTEL') }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            @if($l->status === 'completed')
                                <span class="badge-status confirmed" style="font-size:11px; font-weight:700;">
                                    <i class="fa-solid fa-check me-1"></i> Settled
                                </span>
                            @else
                                <span class="badge-status pending" style="font-size:11px; font-weight:700;">
                                    {{ ucfirst($l->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0">No booking ledger records yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="noSearchResults" style="display:none; text-align:center; padding:40px 20px; color:#94a3b8;">
            <i class="fa-solid fa-magnifying-glass fa-2x mb-2 opacity-50"></i>
            <p class="mb-0 fw-semibold" style="font-size:14px;">No records matched your search.</p>
        </div>

        @if($ledgers->hasPages())
        <div class="stockifly-table-footer">
            <div class="footer-left">
                Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of {{ $ledgers->total() }} records
            </div>
            <div class="footer-right">
                {{ $ledgers->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
// ── Smart Search ──────────────────────────────────────────────────
function filterLedgerTable(query) {
    const q = query.trim().toLowerCase();
    const rows = document.querySelectorAll('#ledgerTableBody tr.ledger-row');
    const clearBtn = document.getElementById('ledgerClearBtn');
    const noResults = document.getElementById('noSearchResults');
    let visible = 0;

    clearBtn.style.display = q ? 'inline-flex' : 'none';

    rows.forEach(row => {
        if (row.innerText.toLowerCase().includes(q)) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    noResults.style.display = (visible === 0 && q) ? 'block' : 'none';
}

function clearLedgerSearch() {
    const input = document.getElementById('ledgerSearchInput');
    input.value = '';
    filterLedgerTable('');
    input.focus();
}

// ── Master Checkbox ───────────────────────────────────────────────
function toggleMasterSelect(master) {
    const checkboxes = document.querySelectorAll('#ledgerTableBody .row-checkbox');
    const visibleRows = document.querySelectorAll('#ledgerTableBody tr.ledger-row:not([style*="display: none"])');
    visibleRows.forEach(row => {
        const cb = row.querySelector('.row-checkbox');
        if (cb) cb.checked = master.checked;
        row.style.backgroundColor = master.checked ? '#f0f7ff' : '';
    });
    updateBulkBar();
}

// ── Individual Row Checkbox ───────────────────────────────────────
function onRowCheckboxChange() {
    const all   = document.querySelectorAll('#ledgerTableBody .row-checkbox');
    const checked = document.querySelectorAll('#ledgerTableBody .row-checkbox:checked');
    document.getElementById('masterCheckbox').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('masterCheckbox').checked = checked.length === all.length && all.length > 0;

    // Highlight selected row
    all.forEach(cb => {
        cb.closest('tr').style.backgroundColor = cb.checked ? '#f0f7ff' : '';
    });

    updateBulkBar();
}

// ── Toggle Bulk Action Bar ────────────────────────────────────────
function updateBulkBar() {
    const selected = document.querySelectorAll('#ledgerTableBody .row-checkbox:checked');
    const count    = selected.length;
    const bar      = document.getElementById('bulkActionBar');
    const title    = document.getElementById('toolbarDefault');
    const badge    = document.getElementById('selectedCountBadge');

    if (count > 0) {
        bar.style.display   = 'flex';
        title.style.display = 'none';
        badge.textContent   = count + ' Selected';
    } else {
        bar.style.display   = 'none';
        title.style.display = 'flex';
    }
}

// ── Deselect All ──────────────────────────────────────────────────
function clearAllSelections() {
    document.querySelectorAll('#ledgerTableBody .row-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('tr').style.backgroundColor = '';
    });
    document.getElementById('masterCheckbox').checked = false;
    document.getElementById('masterCheckbox').indeterminate = false;
    updateBulkBar();
}

// ── Export Selected to CSV ────────────────────────────────────────
function exportSelectedCSV() {
    const rows = document.querySelectorAll('#ledgerTableBody tr.ledger-row');
    let csv = 'TXN Ref,Hotel,Gross (BDT),Commission,Net Earnings,Method,Status\n';
    rows.forEach(row => {
        const cb = row.querySelector('.row-checkbox');
        if (cb && cb.checked) {
            const cells = row.querySelectorAll('td');
            const d = Array.from(cells).slice(1).map(c => '"' + c.innerText.trim().replace(/\n/g, ' ') + '"');
            csv += d.join(',') + '\n';
        }
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
    a.download = 'prime_booking_ledger_export.csv'; a.click();
}

// ── Copy Selected Rows Text ───────────────────────────────────────
function copySelectedRows() {
    const rows = document.querySelectorAll('#ledgerTableBody tr.ledger-row');
    let text = 'TXN Ref | Hotel | Gross | Commission | Net | Method | Status\n';
    rows.forEach(row => {
        const cb = row.querySelector('.row-checkbox');
        if (cb && cb.checked) {
            const cells = Array.from(row.querySelectorAll('td')).slice(1);
            text += cells.map(c => c.innerText.trim().replace(/\n/g, ' ')).join(' | ') + '\n';
        }
    });
    navigator.clipboard.writeText(text).then(() => {
        if (window.showToast) window.toastSuccess('Selected rows copied to clipboard!');
    });
}

// ── Print Selected Rows ───────────────────────────────────────────
function printSelectedRows() {
    const rows = document.querySelectorAll('#ledgerTableBody tr.ledger-row');
    let html = '<html><head><title>Prime Booking Ledger</title>';
    html += '<style>body{font-family:sans-serif;font-size:13px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #e2e8f0;padding:8px 12px;text-align:left;} th{background:#0f172a;color:#fff;}</style></head><body>';
    html += '<h2 style="margin-bottom:12px;">Prime Booking — Selected Ledger Records</h2><table>';
    html += '<thead><tr><th>TXN Ref</th><th>Hotel</th><th>Gross</th><th>Commission</th><th>Net</th><th>Method</th><th>Status</th></tr></thead><tbody>';
    rows.forEach(row => {
        const cb = row.querySelector('.row-checkbox');
        if (cb && cb.checked) {
            const cells = Array.from(row.querySelectorAll('td')).slice(1);
            html += '<tr>' + cells.map(c => '<td>' + c.innerText.trim().replace(/\n/g, '<br>') + '</td>').join('') + '</tr>';
        }
    });
    html += '</tbody></table></body></html>';
    const win = window.open('', '_blank'); win.document.write(html); win.document.close(); win.print();
}
</script>
@endpush

@endsection
