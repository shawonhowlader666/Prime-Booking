@extends('layouts.vendor')
@section('title', 'Edit Room: ' . $room->name)

@section('content')

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('vendor.rooms.index', $property->id) }}">{{ Str::limit($property->name, 25) }} Rooms</a>
        <span class="sep">-</span><strong style="color:#333;">Edit Room Category</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title" style="font-size:16px;">Editing: {{ $room->name }}</h1>
        <a href="{{ route('vendor.rooms.index', $property->id) }}" class="btn-header-action" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:6px 14px; border-radius:4px; font-size:12px; font-weight:600; text-decoration:none;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Rooms List
        </a>
    </div>
</div>

<div class="page-content-area" style="padding: 20px;">
    <div style="max-width:820px; margin:0 auto;">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-xmark me-1"></i> {{ implode(', ', $errors->all()) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('vendor.rooms.update', [$property->id, $room->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="card border-0 shadow-sm mb-4" style="border-radius:6px; border:1px solid #e2e8f0 !important;">
                <div class="card-header bg-white py-2.5 px-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0" style="font-size:13.5px;">
                        <i class="fa-solid fa-bed text-primary me-1"></i> Room Category Details
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold" style="font-size:12px;">Room Name / Category Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $room->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px;">Bed Configuration <span class="text-danger">*</span></label>
                            <select name="bed_type" class="form-select" required>
                                @foreach([
                                    '1 King Bed' => '1 King Bed (Extra Large)',
                                    '1 Queen Bed' => '1 Queen Bed',
                                    '2 Twin Beds' => '2 Twin / Single Beds',
                                    '3 Single Beds' => '3 Single Beds (Triple Room)',
                                    'Super King Bed' => 'Super King Bed + Jacuzzi Suite',
                                    'Bunk Beds' => 'Bunk Beds (Family Room)'
                                ] as $btVal => $btLbl)
                                    <option value="{{ $btVal }}" {{ old('bed_type', $room->bed_type) == $btVal ? 'selected' : '' }}>{{ $btLbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Existing Room Photos Gallery Manager --}}
                        @php
                            $roomImgs = is_array($room->images) ? $room->images : (json_decode($room->images ?? '[]', true) ?: []);
                        @endphp
                        @if(!empty($roomImgs) && count($roomImgs) > 0)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="background:#f8fafc; border:1px solid #cbd5e1 !important; border-radius:6px;">
                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                    <span class="fw-bold text-dark" style="font-size:12.5px;">
                                        <i class="fa-solid fa-images text-primary me-1.5"></i> Current Room Photos ({{ count($roomImgs) }})
                                    </span>
                                    <small class="text-muted" style="font-size:11px;">Check to delete any specific photo</small>
                                </div>
                                <div class="card-body p-2.5">
                                    <div class="row g-2" style="max-height: 240px; overflow-y: auto;">
                                        @foreach($roomImgs as $pIdx => $pUrl)
                                        <div class="col-md-3 col-sm-4 col-6">
                                            <div class="p-1.5 bg-white rounded border position-relative d-flex flex-column gap-1">
                                                <div class="rounded overflow-hidden" style="height: 80px; background:#000;">
                                                    <img src="{{ media_url($pUrl) }}" class="w-100 h-100" style="object-fit:cover;" alt="Room Photo" loading="lazy">
                                                </div>
                                                <div class="form-check form-check-sm mt-1 mb-0">
                                                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $pUrl }}" id="delPageRoomImg_{{ $pIdx }}">
                                                    <label class="form-check-label text-danger fw-semibold" for="delPageRoomImg_{{ $pIdx }}" style="font-size:11px; cursor:pointer;">
                                                        <i class="fa-solid fa-trash-can me-1"></i> Delete
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px;"><i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Upload More Photos (Multi-Upload)</label>
                            <input type="file" name="image_files[]" multiple accept="image/*" class="form-control">
                            <small class="text-muted" style="font-size:11px;">Select single or multiple photos at once</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px;"><i class="fa-solid fa-link text-primary me-1"></i> Additional Photo URLs (one per line)</label>
                            <textarea name="image_url" class="form-control" rows="2" placeholder="https://.../photo1.jpg&#10;https://.../photo2.jpg"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px;">Base Price (৳ / Night) <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_night" class="form-control" value="{{ old('price_per_night', $room->price_per_night) }}" required step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px;">Room Size (m²)</label>
                            <input type="number" name="room_size_sqm" class="form-control" value="{{ old('room_size_sqm', $room->room_size_sqm) }}" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size:12px;">Total Units <span class="text-danger">*</span></label>
                            <input type="number" name="total_rooms" class="form-control" value="{{ old('total_rooms', $room->total_rooms ?? 10) }}" required min="1">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold" style="font-size:12px;">Max Adults <span class="text-danger">*</span></label>
                            <input type="number" name="max_adults" class="form-control" value="{{ old('max_adults', $room->max_adults ?? 2) }}" required min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold" style="font-size:12px;">Max Children</label>
                            <input type="number" name="max_children" class="form-control" value="{{ old('max_children', $room->max_children ?? 1) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:12px;">Room View</label>
                            <select name="view_type" class="form-select">
                                @foreach(['Sea View', 'Ocean Front', 'City View', 'Garden View', 'Pool View', 'Mountain / Hill View', 'Courtyard View'] as $vt)
                                    <option value="{{ $vt }}" {{ old('view_type', $room->view_type) == $vt ? 'selected' : '' }}>{{ $vt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-2.5 px-3 border-top d-flex align-items-center justify-content-between">
                    <a href="{{ route('vendor.rooms.index', $property->id) }}" class="btn btn-sm btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-4" style="background:#2067e1;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

