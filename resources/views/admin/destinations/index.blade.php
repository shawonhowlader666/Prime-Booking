@extends('layouts.main', ['activePage' => 'admin'])

@section('title', 'Destination Banners & Media Manager | Admin Control Panel')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 24px;">
                    <i class="fa-solid fa-map-location-dot text-primary me-2"></i> {{ __('Destination Banners & Media Manager') }}
                </h3>
                <p class="text-secondary small mb-0">Add &amp; update banner images/videos for popular BD travel destinations (2-sec auto slide &amp; real property counts).</p>
            </div>
            
            <button class="btn text-white fw-bold rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#createDestinationModal" style="background-color: #2067e1;">
                <i class="fa-solid fa-plus me-1"></i> {{ __('Add New Destination Banner') }}
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Destinations Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px;">
                        <tr>
                            <th class="ps-4">Banner Media</th>
                            <th>Destination Name</th>
                            <th>Tagline</th>
                            <th>Real Hotels Registered</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($destinations as $d)
                        <tr>
                            <td class="ps-4">
                                <div class="position-relative overflow-hidden rounded-3 border" style="width: 72px; height: 48px;">
                                    @if($d->video_url)
                                        <video src="{{ $d->video_url }}" class="w-100 h-100" style="object-fit: cover;" autoplay loop muted></video>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-1" style="font-size: 8px;">VIDEO</span>
                                    @else
                                        <img src="{{ $d->image_url }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $d->name }}">
                                    @endif
                                </div>
                            </td>
                            <td class="fw-bold text-dark">{{ $d->name }}</td>
                            <td class="text-secondary small">{{ $d->tagline ?? '—' }}</td>
                            <td>
                                <span class="badge bg-light text-primary border fw-bold px-2.5 py-1">
                                    {{ $d->properties_count }} Real Hotels
                                </span>
                            </td>
                            <td class="fw-semibold text-secondary">{{ $d->sort_order }}</td>
                            <td>
                                <span class="badge bg-{{ $d->is_active ? 'success' : 'secondary' }} fw-bold px-3 py-1">
                                    {{ $d->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
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
                                                @csrf
                                                @method('DELETE')
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
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <div class="modal-header border-bottom p-4 bg-light">
                                        <h5 class="modal-title fw-bold text-dark">Edit {{ $d->name }} Banner Media</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.destinations.update', $d->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">Destination Name</label>
                                                <input type="text" name="name" class="form-control rounded-3" value="{{ $d->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">Tagline / Highlight</label>
                                                <input type="text" name="tagline" class="form-control rounded-3" value="{{ $d->tagline }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">High-Res Banner Image URL</label>
                                                <input type="url" name="image_url" class="form-control rounded-3" value="{{ $d->image_url }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">Optional Video MP4 URL</label>
                                                <input type="url" name="video_url" class="form-control rounded-3" value="{{ $d->video_url }}" placeholder="https://example.com/video.mp4">
                                                <small class="text-muted">Supports MP4 background video loops.</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark">Sort Order</label>
                                                <input type="number" name="sort_order" class="form-control rounded-3" value="{{ $d->sort_order }}">
                                            </div>
                                            <div class="form-check form-switch pt-2">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck{{ $d->id }}" {{ $d->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-dark ms-2" for="activeCheck{{ $d->id }}">Show on Homepage Marquee Slider</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top p-3 bg-light">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn text-white fw-bold rounded-pill px-4" style="background-color: #2067e1;">SAVE CHANGES</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">No destination banners configured.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-table-footer :items="$destinations" :perPage="20" />
        </div>

    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createDestinationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom p-4 bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-plus text-primary me-2"></i> Add New Destination Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.destinations.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Destination Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Saint Martin Island" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Tagline</label>
                        <input type="text" name="tagline" class="form-control rounded-3" placeholder="e.g. Coral Reef Island & Blue Waters">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">High-Res Banner Image URL <span class="text-danger">*</span></label>
                        <input type="url" name="image_url" class="form-control rounded-3" placeholder="https://images.unsplash.com/..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Optional Video MP4 URL</label>
                        <input type="url" name="video_url" class="form-control rounded-3" placeholder="https://example.com/video.mp4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control rounded-3" value="1">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-bold rounded-pill px-4" style="background-color: #2067e1;">ADD DESTINATION</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
