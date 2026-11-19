@extends('layouts.vendor')
@section('title', 'Edit Room: ' . $room->name . ' | Vendor Partner Portal')

@section('content')

<div class="page-header-card mb-3">
    <div class="page-breadcrumb mb-1">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('vendor.properties.index') }}">My Properties</a>
        <span class="sep">-</span><a href="{{ route('vendor.rooms.index', $property->id) }}">{{ Str::limit($property->name, 25) }} Rooms</a>
        <span class="sep">-</span><strong style="color:#333;">Edit Room Category</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title m-0" style="font-size:16px;">
                <i class="fa-solid fa-bed text-primary me-2"></i>Editing: {{ $room->name }}
            </h1>
            <span class="text-muted" style="font-size:11.5px;">Property: <strong>{{ $property->name }}</strong> (#PROP-{{ str_pad($property->id, 4, '0', STR_PAD_LEFT) }})</span>
        </div>
        <a href="{{ route('vendor.rooms.index', $property->id) }}" class="btn-export-csv" style="border-color:#cbd5e1; color:#475569; font-weight:600; text-decoration:none;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Rooms List
        </a>
    </div>
</div>

<div class="page-content-area" style="padding: 10px 0 30px;">
    <div style="max-width:980px; margin:0 auto;">

        @if(session('success'))
            <div class="admin-alert success mb-3">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="admin-alert error mb-3">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <strong>Please fix the errors below:</strong>
                <span class="ms-2">{{ implode(', ', $errors->all()) }}</span>
            </div>
        @endif

        <form action="{{ route('vendor.rooms.update', [$property->id, $room->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="_full_form" value="1">

            {{-- 1. ROOM BASIC & BED DETAILS --}}
            <div class="form-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:22px;">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                        <i class="fa-solid fa-bed text-primary me-1.5"></i> 1. Room Specifications &amp; Bed Configuration
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Room Name / Category Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $room->name) }}" placeholder="e.g. Deluxe Sea View King Room" required style="height:38px; font-size:13px;">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Bed Configuration <span class="text-danger">*</span></label>
                        <select name="bed_type" class="form-select" required style="height:38px; font-size:13px;">
                            @foreach([
                                '1 King Bed' => '1 King Bed (Extra Large)',
                                '1 Queen Bed' => '1 Queen Bed',
                                '2 Twin Beds' => '2 Twin / Single Beds',
                                '3 Single Beds' => '3 Single Beds (Triple Room)',
                                '1 Super King + Sofa Bed' => '1 Super King + Sofa Bed',
                                'Bunk Beds' => 'Bunk Beds (Family Room)',
                                '1 Double Bed' => '1 Double Bed'
                            ] as $btVal => $btLbl)
                                <option value="{{ $btVal }}" {{ old('bed_type', $room->bed_type) == $btVal ? 'selected' : '' }}>{{ $btLbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Max Adults <span class="text-danger">*</span></label>
                        <input type="number" name="max_adults" class="form-control" value="{{ old('max_adults', $room->max_adults ?? 2) }}" min="1" required style="height:38px; font-size:13px;">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Max Children</label>
                        <input type="number" name="max_children" class="form-control" value="{{ old('max_children', $room->max_children ?? 1) }}" min="0" style="height:38px; font-size:13px;">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Room Size (m²)</label>
                        <input type="number" name="room_size_sqm" class="form-control" value="{{ old('room_size_sqm', $room->room_size_sqm) }}" placeholder="e.g. 38" min="1" style="height:38px; font-size:13px;">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Total Units Inventory <span class="text-danger">*</span></label>
                        <input type="number" name="total_rooms" class="form-control" value="{{ old('total_rooms', $room->total_rooms ?? 10) }}" min="1" required style="height:38px; font-size:13px;">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Room View</label>
                        <select name="view_type" class="form-select" style="height:38px; font-size:13px;">
                            @foreach([
                                'Sea View / Ocean Front' => 'Direct Ocean / Sea View',
                                'City Skyline View' => 'City Skyline View',
                                'Mountain / Hill View' => 'Scenic Mountain / Hill View',
                                'Garden & Pool View' => 'Lush Garden & Pool View',
                                'Lake / River View' => 'Lake / River View',
                                'Courtyard View' => 'Quiet Inner Courtyard View'
                            ] as $vtVal => $vtLbl)
                                <option value="{{ $vtVal }}" {{ old('view_type', $room->view_type) == $vtVal ? 'selected' : '' }}>{{ $vtLbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Balcony Type</label>
                        <select name="balcony_type" class="form-select" style="height:38px; font-size:13px;">
                            @foreach([
                                'Private Balcony' => 'Private Balcony',
                                'Terrace' => 'Large Terrace',
                                'French Balcony' => 'French Balcony',
                                'No Balcony' => 'No Balcony'
                            ] as $balVal => $balLbl)
                                <option value="{{ $balVal }}" {{ old('balcony_type', $room->balcony_type) == $balVal ? 'selected' : '' }}>{{ $balLbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Smoking Policy</label>
                        <select name="smoking_policy" class="form-select" style="height:38px; font-size:13px;">
                            <option value="Non-Smoking" {{ old('smoking_policy', $room->smoking_policy) == 'Non-Smoking' ? 'selected' : '' }}>100% Non-Smoking</option>
                            <option value="Smoking Allowed" {{ old('smoking_policy', $room->smoking_policy) == 'Smoking Allowed' ? 'selected' : '' }}>Smoking Allowed</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2. PHOTO GALLERY MANAGER --}}
            <div class="form-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:22px;">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                        <i class="fa-solid fa-images text-primary me-1.5"></i> 2. Room Photos Gallery
                    </h6>
                </div>
                @php
                    $roomImgs = is_array($room->images) ? $room->images : (json_decode($room->images ?? '[]', true) ?: []);
                @endphp
                <x-room.photo-manager :photos="$roomImgs" prefix="vendor_room_{{ $room->id }}" title="Current Room Photos" />

                <div class="col-12 mt-3">
                    <label class="form-label text-muted" style="font-size:11.5px;">
                        <i class="fa-solid fa-link text-primary me-1"></i> Additional Photo URLs (one per line fallback):
                    </label>
                    <textarea name="image_urls" class="form-control" rows="2" placeholder="https://.../photo1.jpg&#10;https://.../photo2.jpg" style="font-size:12px;">{{ old('image_urls') }}</textarea>
                </div>
            </div>

            {{-- 3. PRICING & POLICIES --}}
            <div class="form-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:22px;">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                        <i class="fa-solid fa-bangladeshi-taka-sign text-primary me-1.5"></i> 3. Room Pricing &amp; Guest Policies
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Base Selling Price (BDT ৳ / Night) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" name="price_per_night" class="form-control" value="{{ old('price_per_night', $room->price_per_night) }}" required step="0.01" min="0" style="height:38px; font-size:13px;">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:12px; color:#334155;">Original Regular Price (Strikethrough Discount)</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="original_price" class="form-control" value="{{ old('original_price', $room->original_price) }}" placeholder="Optional (e.g. 5000)" step="0.01" min="0" style="height:38px; font-size:13px;">
                        </div>
                        <small class="text-muted" style="font-size:10.5px;">Leave blank if not offering discount.</small>
                    </div>

                    <div class="col-md-4">
                        <div class="p-2.5 rounded border bg-light h-100 d-flex align-items-center">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input type="hidden" name="breakfast_included" value="0">
                                <input class="form-check-input" type="checkbox" name="breakfast_included" value="1" id="vBreakfastCheck" {{ old('breakfast_included', $room->breakfast_included) ? 'checked' : '' }} style="cursor:pointer; width:34px; height:18px;">
                                <label class="form-check-label fw-bold text-dark mb-0" for="vBreakfastCheck" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-mug-hot text-warning me-1"></i> Free Breakfast Included
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-2.5 rounded border bg-light h-100 d-flex align-items-center">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input type="hidden" name="free_cancellation" value="0">
                                <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="vCancelCheck" {{ old('free_cancellation', $room->free_cancellation ?? true) ? 'checked' : '' }} style="cursor:pointer; width:34px; height:18px;">
                                <label class="form-check-label fw-bold text-dark mb-0" for="vCancelCheck" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i> Free Cancellation
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-2.5 rounded border bg-light h-100 d-flex align-items-center">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input type="hidden" name="extra_bed_allowed" value="0">
                                <input class="form-check-input" type="checkbox" name="extra_bed_allowed" value="1" id="vExtraBedCheck" {{ old('extra_bed_allowed', $room->extra_bed_allowed) ? 'checked' : '' }} style="cursor:pointer; width:34px; height:18px;">
                                <label class="form-check-label fw-bold text-dark mb-0" for="vExtraBedCheck" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-plus text-primary me-1"></i> Extra Bed Allowed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. BATHROOM & ROOM AMENITIES --}}
            <div class="form-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:22px;">
                <div class="border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size:14px;">
                        <i class="fa-solid fa-bath text-primary me-1.5"></i> 4. Bathroom Features &amp; Room Amenities
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold mb-2" style="font-size:12px; color:#334155;">Bathroom Features</label>
                        <div class="p-3 rounded border" style="background:#f8fafc; display:grid; grid-template-columns:repeat(auto-fill, minmax(185px, 1fr)); gap:10px;">
                            @php $currBFeats = is_array($room->bathroom_features) ? $room->bathroom_features : ['Private Bathroom', 'Hot Water Geyser']; @endphp
                            @foreach([
                                ['Private Bathroom', 'fa-solid fa-shower'],
                                ['Hot Water Geyser', 'fa-solid fa-fire'],
                                ['Bathtub / Jacuzzi', 'fa-solid fa-bath'],
                                ['Hairdryer', 'fa-solid fa-wind'],
                                ['Free Luxury Toiletries', 'fa-solid fa-pump-soap'],
                                ['Bathrobe & Slippers', 'fa-solid fa-vest'],
                            ] as $bf)
                                <label class="form-check-label d-flex align-items-center rounded border bg-white px-3 py-2 m-0" style="font-size:12px; font-weight:600; color:#334155; cursor:pointer; gap:10px;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="bathroom_features[]" value="{{ $bf[0] }}" {{ in_array($bf[0], $currBFeats) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                    <i class="{{ $bf[1] }}" style="color:#64748b; font-size:13px; width:16px; text-align:center;"></i>
                                    <span>{{ $bf[0] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold mb-2" style="font-size:12px; color:#334155;">Popular In-Room Amenities</label>
                        <div class="p-3 rounded border" style="background:#f8fafc; display:grid; grid-template-columns:repeat(auto-fill, minmax(185px, 1fr)); gap:10px;">
                            @php $currFacs = is_array($room->facilities) ? $room->facilities : []; @endphp
                            @foreach([
                                ['Air Conditioning', 'fa-solid fa-snowflake'],
                                ['Free Wi-Fi', 'fa-solid fa-wifi'],
                                ['Smart Flat TV', 'fa-solid fa-tv'],
                                ['Tea & Coffee Maker', 'fa-solid fa-mug-hot'],
                                ['Mini Fridge', 'fa-solid fa-box'],
                                ['Work Desk', 'fa-solid fa-laptop'],
                                ['Safety Locker', 'fa-solid fa-vault'],
                                ['Electric Kettle', 'fa-solid fa-fire-burner'],
                                ['Soundproofing', 'fa-solid fa-volume-xmark'],
                                ['Daily Housekeeping', 'fa-solid fa-broom'],
                            ] as $am)
                                <label class="form-check-label d-flex align-items-center rounded border bg-white px-3 py-2 m-0" style="font-size:12px; font-weight:600; color:#334155; cursor:pointer; gap:10px;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="amenities[]" value="{{ $am[0] }}" {{ in_array($am[0], $currFacs) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                    <i class="{{ $am[1] }}" style="color:#64748b; font-size:13px; width:16px; text-align:center;"></i>
                                    <span>{{ $am[0] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold mb-1" style="font-size:12px; color:#334155;">Additional Notes / Features (1 per line)</label>
                        <textarea name="facilities_text" class="form-control" rows="2" placeholder="e.g. Extra Pillows&#10;Connecting Room Option" style="font-size:12px;">{{ old('facilities_text', implode("\n", array_diff($currFacs, ['Air Conditioning', 'Free Wi-Fi', 'Smart Flat TV', 'Tea & Coffee Maker', 'Mini Fridge', 'Work Desk', 'Safety Locker', 'Electric Kettle', 'Soundproofing', 'Daily Housekeeping']))) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STICKY SAVE ACTIONS --}}
            <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded border shadow-sm sticky-bottom" style="z-index:100;">
                <a href="{{ route('vendor.rooms.index', $property->id) }}" class="btn btn-light border px-4 fw-semibold" style="height:38px; font-size:13px;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Rooms List
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-xs" style="background:#2067e1; height:38px; font-size:13px;">
                    <i class="fa-solid fa-floppy-disk me-1.5"></i> Save Room Category
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
