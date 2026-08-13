@extends('layouts.admin')
@section('title', 'Guest Reviews & Rating Moderation — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Guest Reviews &amp; Rating Moderation</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button type="button" class="btn-tbl-copy" onclick="copyTableToClipboard('reviewsTable')" title="Copy Table to Clipboard"><i class="fa-regular fa-copy"></i> Copy</button>
            <button type="button" class="btn-tbl-excel" onclick="exportTableExcel('reviewsTable', 'reviews')" title="Export to Excel"><i class="fa-solid fa-file-excel"></i> XL</button>
            <button type="button" class="btn-export-csv" onclick="exportTableCSV('reviewsTable', 'reviews')" title="Export CSV"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button type="button" class="btn-export-pdf" onclick="exportTablePDF('reviewsTable', 'reviews')" title="Export PDF"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button type="button" class="btn-tbl-print" onclick="printTable('reviewsTable')" title="Print Table"><i class="fa-solid fa-print"></i> Print</button></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>Reservations</span>
        <span class="sep">-</span><strong style="color:#333;">Guest Reviews</strong>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">

    @if(session('success'))
        <div class="admin-alert success mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KPI SUMMARY ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#8c8c8c; font-size:10.5px; font-weight:700;">TOTAL REVIEWS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $stats['total'] ?? count($reviews) }} Reviews</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#1890ff;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">APPROVED REVIEWS</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $stats['approved'] ?? 0 }} Published</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#f6ffed; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">PENDING MODERATION</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['pending'] ?? 0 }} Pending</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">AVERAGE RATING SCORE</p>
                        <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $stats['avg'] ?? '4.8' }} / 5.0 ★</p>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                </div>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
    </div>

    {{-- STOCKIFLY FILTER BAR --}}
    <div class="card border border-gray-200 rounded-3 mb-4 bg-white p-3 shadow-xs" style="border-radius: 8px !important;">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by guest name, property, or review comment..." value="{{ request('search') }}" style="font-size: 13px;">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" style="font-size: 13px;">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved &amp; Published</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Moderation</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="rating" class="form-select form-select-sm" style="font-size: 13px;">
                    <option value="all" {{ request('rating') == 'all' ? 'selected' : '' }}>All Rating Stars</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars ★★★★★</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars ★★★★☆</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars ★★★☆☆</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars ★★☆☆☆</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star ★☆☆☆☆</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="background-color: #2067e1; font-size: 12.5px;">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'rating']))
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-light btn-sm text-secondary border fw-bold" title="Reset Filters" style="font-size: 12.5px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0">
        <div class="saas-table-toolbar">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-star me-1 text-warning"></i> All Guest Testimonials &amp; Ratings ({{ count($reviews) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search reviews..." onkeyup="filterTableSearch('reviewsTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="reviewsTable">
                <thead>
                    <tr>
                        <th style="width:36px; text-align:center;"><input type="checkbox" class="tbl-select-checkbox tbl-master-check" onclick="toggleAllRows('reviewsTable', this)" title="Select All Rows"></th>
                        <th>Guest Name</th>
                        <th>Property Name</th>
                        <th>Rating Score</th>
                        <th>Review Comment</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($reviews as $r)
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="tbl-row-check tbl-select-checkbox" onchange="updateRowHighlight(this)"></td>
                        <td>
                            <strong style="font-size:13px; color:#1e293b; display:block;">{{ $r->guest_name ?? optional($r->user)->name ?? 'Verified Guest' }}</strong>
                            <span style="font-size:11px; color:#8c8c8c;">{{ optional($r->user)->email ?? 'Registered User' }}</span>
                        </td>
                        <td>
                            <strong style="font-size:12.5px; color:#334155;">{{ Str::limit(optional($r->property)->name ?? $r->property_name ?? 'Property N/A', 28) }}</strong>
                        </td>
                        <td>
                            <span style="color:#ff9f43; font-size:13px; font-weight:700;">{{ str_repeat('★', $r->rating ?? 5) }}</span>
                            <span style="font-size:11px; color:#64748b; font-weight:700;">({{ $r->rating ?? 5 }}.0)</span>
                        </td>
                        <td style="font-size:12px; color:#475569; max-width:280px; white-space:normal;">
                            "{{ Str::limit($r->comment ?? 'Great stay experience!', 90) }}"
                        </td>
                        <td style="font-size:11.5px; color:#8c8c8c;">
                            {{ $r->created_at ? (is_string($r->created_at) ? $r->created_at : $r->created_at->format('M d, Y')) : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-status {{ strtolower($r->status ?? 'approved') == 'approved' ? 'confirmed' : 'pending' }}">
                                {{ ucfirst($r->status ?? 'Approved') }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <form action="{{ route('admin.reviews.toggle', $r->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-primary">
                                                <i class="fa-solid {{ strtolower($r->status ?? 'approved') == 'approved' ? 'fa-xmark text-warning' : 'fa-check text-success' }} me-2"></i>
                                                {{ strtolower($r->status ?? 'approved') == 'approved' ? 'Unapprove Review' : 'Approve & Publish' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.reviews.destroy', $r->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this review permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Review
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
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Guest Reviews Recorded</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no guest ratings or feedback comments matching your filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :items="$reviews" :perPage="20" />
    </div>

</div>
@endsection


