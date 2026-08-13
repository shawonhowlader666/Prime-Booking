@extends('layouts.admin')

@section('title', 'Destination Banners & Media — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">Destination Banners &amp; Media Manager</h1>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"><button class="btn-tbl-copy" onclick="copyTableToClipboard('destinationsTable')"><i class="fa-solid fa-copy"></i> Copy</button>
            <button class="btn-tbl-excel" onclick="exportTableExcel('destinationsTable', 'Destinations')"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button class="btn-export-csv" onclick="exportTableCSV('destinationsTable', 'Destinations')"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-export-pdf" onclick="printTable('destinationsTable')"><i class="fa-solid fa-file-pdf"></i> PDF</button>
            <button class="btn-tbl-copy" onclick="printTable('destinationsTable')"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn-add-primary" data-bs-toggle="modal" data-bs-target="#createDestinationModal">
                <i class="fa-solid fa-plus me-1"></i> Add Destination Banner
            </button></div>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><span>CMS &amp; Banners</span>
        <span class="sep">-</span><strong style="color:#333;">Destination Banners</strong>
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
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-map-location-dot me-1 text-primary"></i> Popular Travel Destinations ({{ count($destinations) }} Listed)</h6>
            <div style="width:240px;">
                <input type="text" class="form-control form-control-sm" placeholder="Quick search destinations..." onkeyup="filterTableSearch('destinationsTable', this.value)">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-stockifly mb-0" id="destinationsTable">
                <thead>
                    <tr>
                        <th style="width:75px;">Media</th>
                        <th>Destination Name</th>
                        <th>Highlight Tagline</th>
                        <th>Real Hotels</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $d)
                    <tr>
                        <td>
                            <div class="position-relative overflow-hidden rounded border" style="width:56px; height:38px;">
                                @if($d->video_url)
                                    <video src="{{ $d->video_url }}" class="w-100 h-100" style="object-fit:cover;" autoplay loop muted></video>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-0.5" style="font-size:7px;">VIDEO</span>
                                @else
                                    <img src="{{ $d->image_url }}" class="w-100 h-100" style="object-fit:cover;" alt="{{ $d->name }}">
                                @endif
                            </div>
                        </td>
                        <td><strong style="color:#1e293b; font-size:13px;">{{ $d->name }}</strong></td>
                        <td style="color:#64748b; font-size:12px;">{{ $d->tagline ?? '—' }}</td>
                        <td>
                            <span class="badge bg-info text-dark" style="font-size:11px;">
                                {{ $d->properties_count }} Hotels
                            </span>
                        </td>
                        <td style="font-weight:600; font-size:12.5px;">{{ $d->sort_order }}</td>
                        <td>
                            @if($d->is_active)
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
                                        <button class="dropdown-item py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $d->id }}">
                                            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Destination Banner
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('admin.destinations.destroy', $d->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this destination banner?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete Destination
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- Edit Modal for each destination --}}
                    <div class="modal fade" id="editModal{{ $d->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                <div class="modal-header border-bottom p-3 bg-light">
                                    <h6 class="modal-title fw-bold text-dark mb-0">Edit {{ $d->name }} Banner Media</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.destinations.update', $d->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-3">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark" style="font-size:12px;">Destination Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $d->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark" style="font-size:12px;">Tagline / Highlight</label>
                                            <input type="text" name="tagline" class="form-control" value="{{ $d->tagline }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark" style="font-size:12px;">High-Res Banner Image URL</label>
                                            <input type="url" name="image_url" class="form-control" value="{{ $d->image_url }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark" style="font-size:12px;">Optional Video MP4 URL</label>
                                            <input type="url" name="video_url" class="form-control" value="{{ $d->video_url }}" placeholder="https://example.com/video.mp4">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark" style="font-size:12px;">Sort Order</label>
                                            <input type="number" name="sort_order" class="form-control" value="{{ $d->sort_order }}">
                                        </div>
                                        <div class="form-check form-switch pt-1">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck{{ $d->id }}" {{ $d->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark ms-2" for="activeCheck{{ $d->id }}" style="font-size:12px;">Show on Homepage Marquee Slider</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-2 bg-light">
                                        <button type="button" class="btn-export-csv" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn-add-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="background:#ffffff;">
                            <div style="max-width:340px; margin:0 auto; padding:24px 0;">
                                <div style="width:68px; height:68px; border-radius:50%; background:#f8fafc; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:14px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </div>
                                <h6 style="font-weight:700; color:#1e293b; margin-bottom:4px; font-size:14px;">No Destination Banners Found</h6>
                                <p style="font-size:12px; color:#64748b; margin-bottom:16px;">There are no destination banners or media records added to the database.</p>
                                <button class="btn-add-primary d-inline-flex align-items-center gap-1" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#createDestinationModal">
                                    <i class="fa-solid fa-plus"></i> Add First Destination Banner
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :items="$destinations" :perPage="20" />
    </div>

</div>

{{-- Create Modal --}}
<div class="modal fade" id="createDestinationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom p-3 bg-light">
                <h6 class="modal-title fw-bold text-dark mb-0"><i class="fa-solid fa-plus text-primary me-2"></i> Add New Destination Banner</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.destinations.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12px;">Destination Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Saint Martin Island" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12px;">Tagline</label>
                        <input type="text" name="tagline" class="form-control" placeholder="e.g. Coral Reef Island & Blue Waters">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12px;">High-Res Banner Image URL <span class="text-danger">*</span></label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://images.unsplash.com/..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12px;">Optional Video MP4 URL</label>
                        <input type="url" name="video_url" class="form-control" placeholder="https://example.com/video.mp4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size:12px;">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="1">
                    </div>
                </div>
                <div class="modal-footer border-top p-2 bg-light">
                    <button type="button" class="btn-export-csv" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-add-primary">Add Destination Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
