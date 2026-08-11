@extends('layouts.admin')
@section('title', 'Add Room Type | ' . $property->name)

@section('content')

<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><a href="{{ route('admin.rooms.index', $property->id) }}">{{ Str::limit($property->name, 25) }}</a>
        <span class="sep">-</span><strong style="color:#333;">Add Room Type</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Add Room Type — {{ Str::limit($property->name, 35) }}</h1>
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

        <form action="{{ route('admin.rooms.store', $property->id) }}" method="POST">
            @csrf

            <div class="form-card mb-3">
                <div class="form-section-title"><i class="fa-solid fa-bed me-1"></i> Room Details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Room Type Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="e.g. Deluxe Sea View King Room" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bed Type <span style="color:#ff4d4f;">*</span></label>
                        <select name="bed_type" class="form-select" required>
                            <option value="1 King Bed">1 King Bed</option>
                            <option value="1 Queen Bed">1 Queen Bed</option>
                            <option value="2 Twin Beds">2 Twin Beds</option>
                            <option value="1 Super King + Sofa Bed">1 Super King + Sofa Bed</option>
                            <option value="Bunk Beds">Bunk Beds</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room Size (m²)</label>
                        <input type="number" name="room_size_sqm" class="form-control"
                            value="{{ old('room_size_sqm') }}" placeholder="42">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max Adults <span style="color:#ff4d4f;">*</span></label>
                        <input type="number" name="max_adults" class="form-control"
                            value="{{ old('max_adults', 2) }}" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max Children</label>
                        <input type="number" name="max_children" class="form-control"
                            value="{{ old('max_children', 1) }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Total Rooms Available</label>
                        <input type="number" name="total_rooms" class="form-control"
                            value="{{ old('total_rooms', 10) }}" min="1">
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
                                value="{{ old('price_per_night') }}" placeholder="12500" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Breakfast</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="breakfast_included" value="1"
                                id="breakfastCheck" {{ old('breakfast_included') ? 'checked' : '' }}>
                            <label class="form-check-label" for="breakfastCheck" style="font-size:13px;">Breakfast Included</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cancellation</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="free_cancellation" value="1"
                                id="cancelCheck" {{ old('free_cancellation', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="cancelCheck" style="font-size:13px;">Free Cancellation</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card mb-3">
                <div class="form-section-title"><i class="fa-solid fa-list-ul me-1"></i> Room Facilities (one per line)</div>
                <textarea name="facilities_text" class="form-control" rows="5"
                    placeholder="Private Sea View Balcony&#10;Flat-screen Satellite TV&#10;Mini Bar & Refrigerator&#10;Coffee / Tea Maker&#10;In-room Safe&#10;Luxury Bathrobe & Slippers">{{ old('facilities_text') }}</textarea>
                <p style="font-size:11px; color:#8c8c8c; margin:6px 0 0;">Enter each facility on a new line — these show as bullet points on the hotel detail page.</p>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; padding:12px 0;">
                <a href="{{ route('admin.rooms.index', $property->id) }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:8px 20px;">Cancel</a>
                <button type="submit" class="btn-add-primary" style="padding:8px 28px;">
                    Add Room Type <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
