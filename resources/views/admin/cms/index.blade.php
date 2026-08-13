@extends('layouts.admin')
@section('title', 'Website Pages CMS | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS Pages</span>
        <span class="sep">-</span><strong style="color:#333;">Website Pages CMS</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Website Static Pages CMS Content</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage static pages, legal disclaimers, partner portals, and SEO content</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('cmsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('cmsTable', 'CMS_Pages')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('cmsTable', 'CMS_Pages')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('cmsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('cmsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addCmsModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add New CMS Page</span>
            </button>
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
    @php
        $pageColl = method_exists($pages, 'getCollection') ? $pages->getCollection() : collect($pages);
        $totalCount = count($pages);
        $legalCount = $pageColl->where('group', 'legal')->count();
        $partnerCount = $pageColl->where('group', 'partner')->count();
        $generalCount = $pageColl->where('group', 'general')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">PUBLISHED PAGES</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $totalCount }} Pages</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">GENERAL PAGES</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $generalCount }} Pages</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">LEGAL &amp; PRIVACY</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $legalCount }} Docs</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">PARTNER &amp; HOST</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $partnerCount }} Portals</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-file-lines me-2 text-primary"></i> Website Pages Directory ({{ count($pages) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search pages..." onkeyup="filterTableSearch('cmsTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="cmsTable">
                <thead>
                    <tr>
                        <th>Page Key</th>
                        <th>Page Title</th>
                        <th>Content Group</th>
                        <th>Frontend Live Page</th>
                        <th>Last Updated</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>
                            <code style="font-size:12px; background:#f1f5f9; color:#0f172a; padding:3px 8px; border-radius:4px; font-weight:600;">{{ $page->key }}</code>
                        </td>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $page->title }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary border-opacity-25" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">
                                {{ ucfirst($page->group) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ url('/pages/' . $page->key) }}" target="_blank" class="badge bg-success bg-opacity-10 text-success text-decoration-none fw-bold" style="font-size:11px; padding:4px 8px; border-radius:4px;">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Live Page Link
                            </a>
                        </td>
                        <td style="font-size:12.5px; color:#64748b;">
                            {{ $page->updated_at ? $page->updated_at->format('d M Y, H:i') : 'N/A' }}
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3" href="{{ route('admin.cms.edit', $page) }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Page Content
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#previewCmsModal{{ $page->id }}">
                                            <i class="fa-solid fa-eye text-info me-2"></i> Quick Preview Content
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 text-success" href="{{ url('/pages/' . $page->key) }}" target="_blank">
                                            <i class="fa-solid fa-globe me-2"></i> View Live Web Page
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.cms.destroy', $page) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this CMS page?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Page
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- PREVIEW CONTENT MODAL --}}
                    <div class="modal fade" id="previewCmsModal{{ $page->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                        <i class="fa-solid fa-file-lines text-primary me-2"></i> Preview: {{ $page->title }}
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding:20px; max-height:450px; overflow-y:auto;">
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <span class="text-secondary d-block mb-1" style="font-size:11.5px; font-weight:700;">PAGE SYSTEM IDENTIFIER:</span>
                                        <code>{{ $page->key }}</code> | <span class="badge bg-secondary" style="font-size:10.5px;">{{ ucfirst($page->group) }}</span>
                                    </div>
                                    <div class="p-3 bg-white border rounded">
                                        {!! $page->content ?: '<p class="text-muted italic">No content written yet.</p>' !!}
                                    </div>
                                </div>
                                <div class="modal-footer d-flex justify-content-between" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                    <a href="{{ route('admin.cms.edit', $page) }}" class="btn btn-primary btn-sm fw-bold" style="border-radius:4px; background-color:var(--primary); border:none;">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Full Page HTML
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-dismiss="modal" style="border-radius:4px;">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No CMS Pages Created</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no static website content pages found in the database.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addCmsModal">
                                    <i class="fa-solid fa-plus"></i> Create First Page
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$pages" :perPage="15" />
    </div>

</div>

{{-- ADD CMS PAGE MODAL --}}
<div class="modal fade" id="addCmsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('admin.cms.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                        <i class="fa-solid fa-file-lines text-primary me-2"></i> Add New CMS Static Page
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Page Title <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Refund &amp; Cancellation Policy" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="row g-2.5 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Page Unique Key <span style="color:#ff4d4f;">*</span></label>
                            <input type="text" name="key" class="form-control" placeholder="e.g. refund_policy" required style="font-size:13px; height:38px; border-radius:4px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Group Category <span style="color:#ff4d4f;">*</span></label>
                            <select name="group" class="form-select" style="font-size:13px; height:38px; border-radius:4px;" required>
                                <option value="general">General</option>
                                <option value="legal">Legal &amp; Privacy</option>
                                <option value="partner">Partner &amp; Host</option>
                                <option value="support">Help &amp; Support</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Page Content (HTML supported)</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Enter page text or HTML content..." style="font-size:13px; border-radius:4px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Page <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
