@extends('layouts.admin')
@section('title', 'Homepage Hero Banner Slider | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS Pages</span>
        <span class="sep">-</span><strong style="color:#333;">Hero Banner Slider</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <div>
            <h1 class="page-title m-0">Homepage Hero Slider &amp; Banner Manager</h1>
            <span style="font-size:12.5px; color:#64748b;">Manage homepage carousel slides, offer badges, gallery file uploads, and hero taglines</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <button class="btn-tbl-copy" onclick="copyTableToClipboard('heroSlidesTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('heroSlidesTable', 'Hero_Slides')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('heroSlidesTable', 'Hero_Slides')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('heroSlidesTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('heroSlidesTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#addSlideModal" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-plus"></i> <span>Add New Banner Slide</span>
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
        $activeSlides = $slides->where('status', 'active')->count();
        $inactiveSlides = $slides->where('status', 'inactive')->count();
        $offerCount = $slides->filter(fn($s) => !empty($s->badge_text))->count();
        $totalSlidesCount = count($slides);
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#7367f0; font-size:10.5px; font-weight:700;">ACTIVE CAROUSEL SLIDES</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#1e293b; margin:0;">{{ $activeSlides }} Slides</p>
                <div class="kpi-accent-bar" style="background:#7367f0;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#28c76f; font-size:10.5px; font-weight:700;">OFFER PROMOTIONS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#28c76f; margin:0;">{{ $offerCount }} Badges</p>
                <div class="kpi-accent-bar" style="background:#28c76f;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#ff9f43; font-size:10.5px; font-weight:700;">INACTIVE / DRAFT</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#ff9f43; margin:0;">{{ $inactiveSlides }} Slides</p>
                <div class="kpi-accent-bar" style="background:#ff9f43;"></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="padding:16px 20px;">
                <p class="kpi-label mb-1" style="color:#00cfe8; font-size:10.5px; font-weight:700;">TOTAL SLIDE RECORDS</p>
                <p class="kpi-value" style="font-size:20px; font-weight:800; color:#00cfe8; margin:0;">{{ $totalSlidesCount }} Total</p>
                <div class="kpi-accent-bar" style="background:#00cfe8;"></div>
            </div>
        </div>
    </div>

    {{-- MAIN HERO TEXT CARD --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="card-header bg-white" style="padding:16px 20px; border-bottom:1px solid #e2e8f0;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14.5px;">
                <i class="fa-solid fa-heading text-primary me-2"></i> Main Homepage Hero Heading &amp; Tagline
            </h6>
        </div>
        <div class="card-body" style="padding:20px;">
            <form action="{{ route('admin.content.hero.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Hero Main Title / Heading <span style="color:#ff4d4f;">*</span></label>
                    <input type="text" name="hero_title" class="form-control" value="{{ $heroTitle }}" required style="font-size:13px; height:38px; border-radius:4px;">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Hero Subtitle / Description</label>
                    <textarea name="hero_subtitle" class="form-control" rows="2" style="font-size:13px; border-radius:4px;">{{ $heroSubtitle }}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-add-primary" style="font-size:13px; height:36px; padding:0 20px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
                        Save Hero Tagline <i class="fa-solid fa-check ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SAAS DATA TABLE CARD --}}
    <div class="data-table-card p-0" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
        <div class="saas-table-toolbar" style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <h6 class="mb-0 fw-bold text-dark" style="font-size:14px;"><i class="fa-solid fa-images me-2 text-primary"></i> Active Carousel Banner Slides ({{ count($slides) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search slides..." onkeyup="filterTableSearch('heroSlidesTable', this.value)" style="font-size:12.5px; border-radius:4px; height:34px; padding:0 12px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="heroSlidesTable">
                <thead>
                    <tr>
                        <th style="width:70px;">Preview</th>
                        <th>Slide Title &amp; Details</th>
                        <th>Offer Badge Text</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($slides as $s)
                    <tr>
                        <td>
                            <img src="{{ $s->image_path }}" alt="Slide Preview" class="rounded border shadow-sm" style="width:58px; height:38px; object-fit:cover; border-radius:4px;">
                        </td>
                        <td>
                            <strong style="font-size:13.5px; color:#0f172a; display:block;">{{ $s->title }}</strong>
                            <span style="font-size:11px; color:#64748b;">Slide ID #{{ $s->id }}</span>
                        </td>
                        <td>
                            @if($s->badge_text)
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size:11px; padding:4px 8px; border-radius:4px;">
                                    <i class="fa-solid fa-tag me-1"></i> {{ $s->badge_text }}
                                </span>
                            @else
                                <span style="font-size:11.5px; color:#94a3b8;">No badge text</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px;">
                                Order #{{ $s->sort_order }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status {{ $s->status == 'active' ? 'confirmed' : 'cancelled' }}">
                                {{ ucfirst($s->status) }}
                            </span>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <div class="dropdown action-gear-dropdown d-inline-block">
                                <button class="btn btn-light btn-sm action-gear-btn shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; border-radius:4px; background:#f1f5f9; color:#475569;">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:4px; font-size:12.5px; border:1px solid #e2e8f0; padding:4px 0; z-index:1050;">
                                    <li>
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#editSlideModal{{ $s->id }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Banner Slide
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('content.hero.slides.toggle', $s->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-warning">
                                                <i class="fa-solid fa-ban me-2"></i> {{ $s->status === 'active' ? 'Deactivate Slide' : 'Activate Slide' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('content.hero.slides.destroy', $s->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this banner slide?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Slide
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- EDIT SLIDE MODAL --}}
                    <div class="modal fade" id="editSlideModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                                <form action="{{ route('content.hero.slides.update', $s->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                                        <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                                            <i class="fa-solid fa-pen text-primary me-2"></i> Edit Slide #{{ $s->id }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="padding:20px;">
                                        <div class="mb-3 text-center">
                                            <img src="{{ $s->image_path }}" class="img-fluid rounded border shadow-sm modal-preview-img{{ $s->id }}" style="height:90px; width:100%; object-fit:cover; border-radius:4px;">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Slide Title <span style="color:#ff4d4f;">*</span></label>
                                            <input type="text" name="title" class="form-control" value="{{ $s->title }}" required style="font-size:13px; height:38px; border-radius:4px;">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Upload File from Gallery / Computer</label>
                                            <input type="file" name="slide_file" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;">
                                            <span style="font-size:10.5px; color:#94a3b8;">OR keep image URL below:</span>
                                            <input type="text" name="image_url" class="form-control" value="{{ $s->image_path }}" style="font-size:13px; height:38px; border-radius:4px;">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Banner Offer Badge Text</label>
                                            <input type="text" name="badge_text" class="form-control" value="{{ $s->badge_text }}" style="font-size:13px; height:38px; border-radius:4px;">
                                        </div>
                                        <div class="row g-2.5 mb-3">
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sort Order</label>
                                                <input type="number" name="sort_order" class="form-control" value="{{ $s->sort_order }}" style="font-size:13px; height:38px; border-radius:4px;">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Status</label>
                                                <select name="status" class="form-select" style="font-size:13px; height:38px; border-radius:4px;">
                                                    <option value="active" {{ $s->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $s->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                                        <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Save Slide Changes <i class="fa-solid fa-check ms-1"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-images"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Carousel Slides Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no active hero banner slides listed in the database.</p>
                                <button type="button" class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                                    <i class="fa-solid fa-plus"></i> Create First Slide
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$slides" :perPage="15" />
    </div>

</div>

{{-- ADD SLIDE MODAL --}}
<div class="modal fade" id="addSlideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:4px; border:1px solid #e2e8f0; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
            <form action="{{ route('content.hero.slides.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:16px 20px;">
                    <h6 class="modal-title fw-bold" style="font-size:15px; color:#0f172a;">
                        <i class="fa-solid fa-plus text-primary me-2"></i> Add New Banner Slide
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Slide Title <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Cox's Bazar Sea Beach Resort" required style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Upload File from Gallery / Computer</label>
                        <input type="file" name="slide_file" class="form-control form-control-sm mb-1" accept="image/*" style="font-size:11.5px; border-radius:4px;">
                        <span style="font-size:10.5px; color:#94a3b8;">OR paste image URL below:</span>
                        <input type="text" name="image_url" class="form-control" placeholder="https://images.unsplash.com/..." style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Banner Offer Badge Text</label>
                        <input type="text" name="badge_text" class="form-control" placeholder="e.g. Up to 25% Off Luxury Hotels" style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:6px;">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ count($slides) + 1 }}" style="font-size:13px; height:38px; border-radius:4px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px;">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-dismiss="modal" style="font-size:13px; height:36px; border-radius:4px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold text-white" style="font-size:13px; height:36px; border-radius:4px; background-color:var(--primary); border:none;">Create Slide <i class="fa-solid fa-check ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
