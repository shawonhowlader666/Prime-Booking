@extends('layouts.admin')
@section('title', 'Edit Room: ' . $room->name)

@section('content')

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.rooms.index', $property->id) }}">{{ Str::limit($property->name, 25) }} Rooms</a>
        <span class="sep">-</span><strong style="color:#333;">Edit Room</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title" style="font-size:16px;">Editing: {{ $room->name }}</h1>
        <a href="{{ route('admin.rooms.index', $property->id) }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959;">
            <i class="fa-solid fa-arrow-left"></i> Back to Rooms
        </a>
    </div>
</div>

<div class="page-content-area">
    <div style="max-width:780px; margin:0 auto;">

        @if($errors->any())
            <div class="admin-alert error mb-3">
                <i class="fa-solid fa-circle-xmark me-1"></i> {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <form action="{{ route('admin.rooms.update', [$property->id, $room->id]) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-card mb-3">
                <div class="form-section-title"><i class="fa-solid fa-bed me-1"></i> Room Details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Room Type Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $room->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bed Type</label>
                        <select name="bed_type" class="form-select">
                            @foreach(['1 King Bed', '1 Queen Bed', '2 Twin Beds', '1 Super King + Sofa Bed', 'Bunk Beds'] as $bt)
                                <option value="{{ $bt }}" {{ old('bed_type', $room->bed_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room Size (m²)</label>
                        <input type="number" name="room_size_sqm" class="form-control" value="{{ old('room_size_sqm', $room->room_size_sqm) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max Adults <span style="color:#ff4d4f;">*</span></label>
                        <input type="number" name="max_adults" class="form-control" value="{{ old('max_adults', $room->max_adults) }}" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max Children</label>
                        <input type="number" name="max_children" class="form-control" value="{{ old('max_children', $room->max_children) }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Total Rooms</label>
                        <input type="number" name="total_rooms" class="form-control" value="{{ old('total_rooms', $room->total_rooms) }}" min="1">
                    </div>
                </div>
            </div>

            <div class="form-card mb-3">
                <div class="form-section-title"><i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Pricing & Policy</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="price_per_night" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('price_per_night', $room->price_per_night) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Breakfast</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="breakfast_included" value="1"
                                id="breakfastCheck" {{ old('breakfast_included', $room->breakfast_included) ? 'checked' : '' }}>
                            <label class="form-check-label" for="breakfastCheck" style="font-size:13px;">Included</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cancellation</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="free_cancellation" value="1"
                                id="cancelCheck" {{ old('free_cancellation', $room->free_cancellation) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cancelCheck" style="font-size:13px;">Free</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card mb-3">
                <div class="form-section-title"><i class="fa-solid fa-sparkles me-1"></i> Popular In-Room Amenities</div>
                <div class="d-flex flex-wrap gap-2 p-2.5 rounded border" style="background:#f8fafc;">
                    @php $currentFacs = is_array($room->facilities) ? $room->facilities : []; @endphp
                    @foreach(['Air Conditioning', 'Free Wi-Fi', 'Smart Flat TV', 'Sea / City View', 'Private Balcony', 'Hot Water / Geyser', 'Tea & Coffee Maker', 'Mini Fridge', 'Work Desk', 'Safety Locker'] as $amenity)
                        <label class="form-check-label d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded border bg-white" style="font-size:11.5px; font-weight:600; color:#334155; cursor:pointer;">
                            <input class="form-check-input m-0" type="checkbox" name="amenities[]" value="{{ $amenity }}" {{ in_array($amenity, $currentFacs) ? 'checked' : '' }} style="cursor:pointer;">
                            {{ $amenity }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-card mb-3">
                <div class="form-section-title"><i class="fa-solid fa-list-ul me-1"></i> Additional Custom Facilities (one per line)</div>
                @php
                    $presetList = ['Air Conditioning', 'Free Wi-Fi', 'Smart Flat TV', 'Sea / City View', 'Private Balcony', 'Hot Water / Geyser', 'Tea & Coffee Maker', 'Mini Fridge', 'Work Desk', 'Safety Locker'];
                    $customLines = array_diff($currentFacs, $presetList);
                @endphp
                <textarea name="facilities_text" class="form-control" rows="4">{{ implode("\n", $customLines) }}</textarea>
            </div>

            {{-- Danger Zone --}}
            <div class="form-card mb-3" style="border-color:#ffccc7; background:#fff8f8;">
                <div class="form-section-title" style="color:#ff4d4f; background:linear-gradient(135deg,#fff1f0,#fff8f8); border-color:#ffccc7;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Delete This Room Type
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:4px 0;">
                    <p style="margin:0; font-size:12.5px; color:#8c8c8c;">Remove "{{ $room->name }}" from {{ $property->name }} permanently.</p>
                    <form action="{{ route('admin.rooms.destroy', [$property->id, $room->id]) }}" method="POST"
                        onsubmit="return confirm('Delete room type permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-table-action danger" style="padding:8px 18px;">
                            Delete Room <i class="fa-solid fa-trash ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; padding:12px 0;">
                <a href="{{ route('admin.rooms.index', $property->id) }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:8px 20px;">Cancel</a>
                <button type="submit" class="btn-add-primary" style="padding:8px 28px;">
                    Save Changes <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
