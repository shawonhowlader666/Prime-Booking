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

        <form action="{{ route('admin.rooms.update', [$property->id, $room->id]) }}" method="POST" enctype="multipart/form-data">
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
                    @php
                        $adminRoomImg = (!empty($room->images) && is_array($room->images)) ? $room->images[0] : '';
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa-solid fa-image text-primary me-1"></i> Room Cover Photo (Direct URL)</label>
                        <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $adminRoomImg) }}" placeholder="https://images.unsplash.com/photo-...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa-solid fa-cloud-arrow-up text-secondary me-1"></i> Or Upload New Photo</label>
                        <input type="file" name="image_file" accept="image/*" class="form-control">
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
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa-solid fa-mountain-sun" style="color:#64748b; margin-right:6px;"></i> Room View <span style="color:#ff4d4f;">*</span></label>
                        <select name="view_type" class="form-select" required>
                            @foreach([
                                'Sea View / Ocean Front' => 'Direct Ocean / Sea View',
                                'Beachfront View' => 'Beachfront & Wave View',
                                'City Skyline View' => 'City Skyline View',
                                'Mountain / Hill View' => 'Scenic Mountain / Hill View',
                                'Garden & Pool View' => 'Lush Garden & Pool View',
                                'Lake / River View' => 'Lake / River View',
                                'Sunset View' => 'Sunset Panoramic View',
                                'Courtyard View' => 'Quiet Inner Courtyard View'
                            ] as $vVal => $vLbl)
                                <option value="{{ $vVal }}" {{ old('view_type', $room->view_type) == $vVal ? 'selected' : '' }}>{{ $vLbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa-solid fa-shower" style="color:#64748b; margin-right:6px;"></i> Attached Bathrooms <span style="color:#ff4d4f;">*</span></label>
                        <select name="bathroom_count" class="form-select" required>
                            <option value="1" {{ old('bathroom_count', $room->bathroom_count) == 1 ? 'selected' : '' }}>1 Attached Bathroom</option>
                            <option value="2" {{ old('bathroom_count', $room->bathroom_count) == 2 ? 'selected' : '' }}>2 Attached Bathrooms</option>
                            <option value="3" {{ old('bathroom_count', $room->bathroom_count) == 3 ? 'selected' : '' }}>3 Attached Bathrooms</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa-solid fa-ban-smoking" style="color:#64748b; margin-right:6px;"></i> Smoking Policy <span style="color:#ff4d4f;">*</span></label>
                        <select name="smoking_policy" class="form-select" required>
                            <option value="Non-Smoking" {{ old('smoking_policy', $room->smoking_policy) == 'Non-Smoking' ? 'selected' : '' }}>100% Non-Smoking Room</option>
                            <option value="Smoking Allowed" {{ old('smoking_policy', $room->smoking_policy) == 'Smoking Allowed' ? 'selected' : '' }}>Smoking Permitted</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa-solid fa-tree-city" style="color:#64748b; margin-right:6px;"></i> Balcony / Terrace</label>
                        <select name="balcony_type" class="form-select">
                            <option value="Private Balcony" {{ old('balcony_type', $room->balcony_type) == 'Private Balcony' ? 'selected' : '' }}>Private Balcony</option>
                            <option value="Terrace" {{ old('balcony_type', $room->balcony_type) == 'Terrace' ? 'selected' : '' }}>Large Open Terrace</option>
                            <option value="French Balcony" {{ old('balcony_type', $room->balcony_type) == 'French Balcony' ? 'selected' : '' }}>French Balcony</option>
                            <option value="No Balcony" {{ old('balcony_type', $room->balcony_type) == 'No Balcony' ? 'selected' : '' }}>No Balcony</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Bathroom Features &amp; Toiletries</label>
                        <div class="d-flex flex-wrap gap-2 p-2.5 rounded border" style="background:#f8fafc;">
                            @php $currentBFeats = is_array($room->bathroom_features) ? $room->bathroom_features : ['Private Bathroom', 'Hot Water Geyser']; @endphp
                            @foreach([
                                ['Private Bathroom', 'fa-solid fa-shower'],
                                ['Hot Water Geyser', 'fa-solid fa-fire'],
                                ['Bathtub / Jacuzzi', 'fa-solid fa-bath'],
                                ['Hairdryer', 'fa-solid fa-wind'],
                                ['Free Luxury Toiletries', 'fa-solid fa-pump-soap'],
                                ['Bathrobe & Slippers', 'fa-solid fa-vest'],
                            ] as $bFeat)
                                <label class="form-check-label d-inline-flex align-items-center rounded border bg-white shadow-xs" style="padding:6px 12px; font-size:12px; font-weight:600; color:#334155; cursor:pointer; gap:8px;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="bathroom_features[]" value="{{ $bFeat[0] }}" {{ in_array($bFeat[0], $currentBFeats) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                    <i class="{{ $bFeat[1] }}" style="color:#64748b; font-size:13px;"></i>
                                    <span>{{ $bFeat[0] }}</span>
                                </label>
                            @endforeach
                        </div>
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
                        <label class="form-label">Extra Bed</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="extra_bed_allowed" value="1"
                                id="extraBedCheck" {{ old('extra_bed_allowed', $room->extra_bed_allowed) ? 'checked' : '' }}>
                            <label class="form-check-label" for="extraBedCheck" style="font-size:13px;">Allowed</label>
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
                    @foreach([
                        ['Air Conditioning', 'fa-solid fa-snowflake'],
                        ['Free Wi-Fi', 'fa-solid fa-wifi'],
                        ['Smart Flat TV', 'fa-solid fa-tv'],
                        ['Sea / City View', 'fa-solid fa-mountain-sun'],
                        ['Private Balcony', 'fa-solid fa-tree-city'],
                        ['Hot Water / Geyser', 'fa-solid fa-fire'],
                        ['Tea & Coffee Maker', 'fa-solid fa-mug-hot'],
                        ['Mini Fridge', 'fa-solid fa-box'],
                        ['Work Desk', 'fa-solid fa-laptop'],
                        ['Safety Locker', 'fa-solid fa-vault'],
                    ] as $amenity)
                        <label class="form-check-label d-inline-flex align-items-center rounded border bg-white shadow-xs" style="padding:6px 12px; font-size:12px; font-weight:600; color:#334155; cursor:pointer; gap:8px;">
                            <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="amenities[]" value="{{ $amenity[0] }}" {{ in_array($amenity[0], $currentFacs) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                            <i class="{{ $amenity[1] }}" style="color:#64748b; font-size:13px;"></i>
                            <span>{{ $amenity[0] }}</span>
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
