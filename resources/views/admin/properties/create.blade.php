@extends('layouts.admin')
@section('title', 'Add New Property Listing | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house me-1.5"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><strong style="color:#333;">Add New Listing</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:8px;">
        <h1 class="page-title m-0">Add New Property Listing</h1>
        <a href="{{ route('admin.properties.index') }}" class="btn-table-action" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-arrow-left"></i> <span>Back to Inventory</span>
        </a>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">
    <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="admin-alert error mb-4" style="border-radius:4px; padding:14px 18px;">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <strong>Please fix the input errors below:</strong>
                <span class="ms-2">{{ implode(', ', $errors->all()) }}</span>
            </div>
        @endif

        {{-- SINGLE CLEAN WHITE SAAS CARD CONTAINER --}}
        <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:28px;">

            {{-- 1. PROPERTY DETAILS --}}
            <div class="border-bottom pb-2.5 mb-4">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-hotel text-primary me-2.5" style="font-size:15px; width:20px;"></i>
                    <span>Property Details &amp; Classification</span>
                </h5>
            </div>
            <div class="row g-3.5 mb-4">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Property Full Name <span style="color:#ff4d4f;">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                        placeholder="e.g. Royal Tulip Sea Pearl Beach Resort & Spa" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label m-0" style="font-size:12.5px; font-weight:600; color:#1e293b;">Property Type <span style="color:#ff4d4f;">*</span></label>
                        <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptAdminCustomCategory()">
                            + Add Type
                        </button>
                    </div>
                    <select name="type" id="adminPropTypeSelect" class="form-select" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>Hotel &amp; Resort</option>
                        <option value="resort" {{ old('type') == 'resort' ? 'selected' : '' }}>Beach Resort &amp; Spa</option>
                        <option value="houseboat" {{ old('type') == 'houseboat' ? 'selected' : '' }}>Ship &amp; Houseboat</option>
                        <option value="homestay" {{ old('type') == 'homestay' ? 'selected' : '' }}>Home Stay &amp; Cottage</option>
                        <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Serviced Apartment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label m-0" style="font-size:12.5px; font-weight:600; color:#1e293b;">City / Destination <span style="color:#ff4d4f;">*</span></label>
                        <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptAdminCustomCity()">
                            + Add City
                        </button>
                    </div>
                    <select name="city" id="adminPropCitySelect" class="form-select" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;" onchange="onAdminCityChanged(this)">
                        <option value="">-- Select City / Destination --</option>
                        @if(isset($locations) && $locations->count())
                            @foreach($locations as $loc)
                                <option value="{{ $loc->name }}" 
                                        data-lat="{{ $loc->latitude }}" 
                                        data-lng="{{ $loc->longitude }}"
                                        {{ old('city') == $loc->name ? 'selected' : '' }}>
                                    {{ $loc->name }} @if($loc->country && $loc->country != 'Bangladesh') ({{ $loc->country }}) @endif
                                </option>
                            @endforeach
                        @else
                            <option value="Cox's Bazar Sea Beach">Cox's Bazar Sea Beach</option>
                            <option value="Dhaka City">Dhaka City</option>
                            <option value="Sundarbans & Mongla">Sundarbans & Mongla</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Star Rating <span style="color:#ff4d4f;">*</span></label>
                    <select name="star_rating" class="form-select" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <option value="5" {{ old('star_rating') == '5' ? 'selected' : '' }}>5 Star Luxury</option>
                        <option value="4" {{ old('star_rating') == '4' ? 'selected' : '' }}>4 Star Premium</option>
                        <option value="3" {{ old('star_rating') == '3' ? 'selected' : '' }}>3 Star Standard</option>
                        <option value="2" {{ old('star_rating') == '2' ? 'selected' : '' }}>2 Star Economy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Vendor Account</label>
                    <select name="vendor_id" class="form-select" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <option value="">Admin Managed (Prime Booking)</option>
                        @if(isset($vendors))
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Full Physical Address <span style="color:#ff4d4f;">*</span></label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                        placeholder="e.g. Inani Beach, Marine Drive Road, Kolatoli, Cox's Bazar 4700" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Nearest Landmark / Distance</label>
                    <input type="text" name="nearest_landmark" class="form-control" value="{{ old('nearest_landmark') }}"
                        placeholder="e.g. 2 mins walk from Kolatoli Beach Point" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Real-Time GPS Coordinates (Lat, Long)</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" name="latitude" id="adminLatitudeInput" class="form-control" value="{{ old('latitude', '21.4272') }}" placeholder="Lat: 21.4272" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;" onchange="updateAdminMarkerFromInputs()">
                        <input type="text" name="longitude" id="adminLongitudeInput" class="form-control" value="{{ old('longitude', '91.9702') }}" placeholder="Long: 91.9702" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;" onchange="updateAdminMarkerFromInputs()">
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-2.5 border rounded bg-light" style="border-radius:6px;">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <span class="fw-bold text-dark" style="font-size:12px;">
                                <i class="fa-solid fa-map-pin text-danger me-1"></i> Interactive Geolocation Pin Picker (OpenStreetMap)
                            </span>
                            <small class="text-secondary" style="font-size:11px;">
                                💡 Click on map or drag pin to auto-fill coordinates
                            </small>
                        </div>
                        <div id="adminMapPicker" style="height: 220px; width: 100%; border-radius: 4px; border: 1px solid #cbd5e1; z-index: 1;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Google Maps Embed / Location Link</label>
                    <input type="url" name="map_embed_url" class="form-control" value="{{ old('map_embed_url') }}"
                        placeholder="https://maps.google.com/..." style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Check-in / Check-out Hours</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" name="checkin_time" class="form-control" value="{{ old('checkin_time', '14:00') }}" placeholder="Checkin: 14:00" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <input type="text" name="checkout_time" class="form-control" value="{{ old('checkout_time', '12:00') }}" placeholder="Checkout: 12:00" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Hotel Contact Phone &amp; Email</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}" placeholder="+8801700000000" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}" placeholder="info@hotel.com" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Building Floors / Levels</label>
                    <input type="number" name="total_floors" class="form-control" value="{{ old('total_floors') }}" placeholder="e.g. 12 Floors" min="1" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Total Hotel Rooms</label>
                    <input type="number" name="total_rooms_count" class="form-control" value="{{ old('total_rooms_count') }}" placeholder="e.g. 150 Rooms" min="1" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Year Built / Renovated</label>
                    <input type="number" name="year_built" class="form-control" value="{{ old('year_built') }}" placeholder="e.g. 2023" min="1950" max="2030" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Front Desk Languages Spoken</label>
                    <div class="d-flex flex-wrap gap-2 p-2 rounded border bg-light">
                        @foreach(['English', 'Bengali', 'Hindi', 'Arabic', 'Chinese'] as $lang)
                            <label class="form-check-label d-inline-flex align-items-center gap-1.5 px-2 py-0.5 rounded border bg-white" style="font-size:11.5px; font-weight:600; color:#334155; cursor:pointer;">
                                <input class="form-check-input m-0" type="checkbox" name="languages_spoken[]" value="{{ $lang }}" {{ in_array($lang, ['English', 'Bengali']) ? 'checked' : '' }} style="cursor:pointer;">
                                {{ $lang }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Pets Policy</label>
                    <select name="pets_policy" class="form-select" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <option value="Pets Not Allowed" selected>🚫 Pets Not Allowed</option>
                        <option value="Pets Allowed">🐾 Pets Allowed (Free / Fee)</option>
                        <option value="Pets Allowed on Request">💬 Pets Allowed on Request</option>
                    </select>
                </div>
            </div>

            {{-- 2. PRICING & POLICIES --}}
            <div class="border-bottom pb-2.5 mb-4 mt-5">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-bangladeshi-taka-sign text-success me-2.5" style="font-size:15px; width:20px;"></i>
                    <span>Base Room Rates, MRP &amp; Commercial Terms</span>
                </h5>
            </div>
            <div class="row g-3.5 mb-4">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Standard Nightly Price (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold" style="font-size:13px; border-radius:4px 0 0 4px; height:38px;">৳</span>
                        <input type="number" name="price_per_night" class="form-control"
                            value="{{ old('price_per_night') }}" placeholder="8500" required style="font-size:13px; border-radius:0 4px 4px 0; height:38px; padding:0 14px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Original / Published MRP (BDT ৳)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold" style="font-size:13px; border-radius:4px 0 0 4px; height:38px;">৳</span>
                        <input type="number" name="original_price" class="form-control"
                            value="{{ old('original_price') }}" placeholder="11000" style="font-size:13px; border-radius:0 4px 4px 0; height:38px; padding:0 14px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Visibility Status</label>
                    <select name="status" class="form-select" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active — Published Live</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive — Draft Mode</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <div class="p-3 bg-light border d-flex align-items-center gap-4 flex-wrap" style="border-radius:4px;">
                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                {{ old('is_featured') ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                            <label class="form-check-label fw-bold text-dark mb-0" for="isFeatured" style="font-size:12.5px; cursor:pointer;">
                                <i class="fa-solid fa-star text-warning me-1.5"></i> Featured Property
                            </label>
                        </div>
                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="freeCancel"
                                {{ old('free_cancellation', '1') ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                            <label class="form-check-label fw-bold text-success mb-0" for="freeCancel" style="font-size:12.5px; cursor:pointer;">
                                <i class="fa-solid fa-circle-check me-1.5"></i> Free Cancellation
                            </label>
                        </div>
                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" id="noCC"
                                {{ old('no_credit_card_required', '1') ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                            <label class="form-check-label fw-bold text-primary mb-0" for="noCC" style="font-size:12.5px; cursor:pointer;">
                                <i class="fa-solid fa-credit-card me-1.5"></i> Pay at Hotel / Cash on Arrival
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. MEDIA & GALLERY --}}
            <div class="border-bottom pb-2.5 mb-4 mt-5">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-images me-2.5" style="font-size:15px; width:20px; color:#7367f0;"></i>
                    <span>Media Assets &amp; Video Property Tour</span>
                </h5>
            </div>
            <div class="row g-3.5 mb-4">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                        <i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Upload Primary Thumbnail (Device)
                    </label>
                    <input type="file" name="primary_image_file" class="form-control" accept="image/*" onchange="previewFile(this)" style="font-size:13px; border-radius:4px; height:38px; padding:4px 14px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                        <i class="fa-solid fa-link text-primary me-1"></i> OR External Thumbnail Image URL
                    </label>
                    <input type="url" name="primary_image" id="primaryImgUrl" class="form-control"
                        value="{{ old('primary_image') }}" placeholder="https://images.unsplash.com/photo-1566073771259-6a8506099945..."
                        oninput="previewUrl(this.value)" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                    <div id="imgPreviewWrap" class="mt-2.5" style="display:none;">
                        <img id="imgPreview" src="" style="height:80px; border-radius:4px; border:1px solid #cbd5e1; object-fit:cover;" alt="Preview">
                    </div>
                </div>

                {{-- Drag & Drop Multi-Image Dropzone for Admin Gallery Photos --}}
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                        <i class="fa-solid fa-photo-film text-primary me-1"></i> Property Gallery Photos (Drag &amp; Drop Multi-Upload)
                    </label>
                    <div id="adminGalleryDropzone" class="p-4 border-2 border-dashed rounded text-center" style="background:#f8fafc; border-color:#93c5fd; cursor:pointer; transition:all 0.2s ease;" onclick="document.getElementById('adminGalleryFileInput').click()">
                        <input type="file" id="adminGalleryFileInput" name="gallery_image_files[]" multiple accept="image/*" class="d-none" onchange="handleAdminGalleryFileSelect(this)">
                        <i class="fa-solid fa-cloud-arrow-up text-primary fs-2 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1" style="font-size:13.5px;">Drag &amp; drop photos here or click to browse</h6>
                        <p class="text-muted m-0" style="font-size:11.5px;">Supports JPG, PNG, WEBP high-resolution photos (Up to 10MB each)</p>
                    </div>
                    {{-- Instant Preview Thumbnails Container --}}
                    <div id="adminGalleryPreviewContainer" class="d-flex flex-wrap gap-2 mt-2.5"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Video Tour URL (YouTube Embed / MP4)</label>
                    <input type="url" name="video_url" class="form-control"
                        value="{{ old('video_url') }}" placeholder="https://www.youtube.com/embed/..." style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Additional Gallery URLs (one per line)</label>
                    <textarea name="gallery_images" class="form-control" rows="2"
                        placeholder="https://images.unsplash.com/photo-1571896349842...&#10;https://images.unsplash.com/photo-1582719478250..." style="font-size:13px; border-radius:4px; padding:8px 14px;">{{ old('gallery_images') }}</textarea>
                </div>
            </div>

            {{-- 4. DESCRIPTION --}}
            <div class="border-bottom pb-2.5 mb-4 mt-5">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-align-left text-warning me-2.5" style="font-size:15px; width:20px;"></i>
                    <span>Property Description &amp; Guest Policies</span>
                </h5>
            </div>
            <div class="row g-3.5 mb-4">
                <div class="col-md-12">
                    <textarea name="description" class="form-control" rows="4"
                        placeholder="Describe property location highlights, room features, check-in policies, complimentary breakfast..." required style="font-size:13px; border-radius:4px; padding:12px 14px;">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- 5. AMENITIES --}}
            <div class="border-bottom pb-2.5 mb-4 mt-5">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-list-check text-primary me-2.5" style="font-size:15px; width:20px;"></i>
                    <span>Property Amenities &amp; On-Site Facilities</span>
                </h5>
            </div>
            <div class="row g-3 mb-4">
                @foreach([
                    ['wifi',       'fa-wifi',              'Free WiFi'],
                    ['pool',       'fa-person-swimming',   'Swimming Pool'],
                    ['parking',    'fa-car',               'Free Parking'],
                    ['ac',         'fa-snowflake',         'Air Conditioning'],
                    ['restaurant', 'fa-utensils',          'Restaurant'],
                    ['breakfast',  'fa-mug-hot',           'Breakfast Included'],
                    ['gym',        'fa-dumbbell',          'Fitness Center'],
                    ['spa',        'fa-spa',               'Spa &amp; Wellness'],
                    ['bar',        'fa-wine-glass',        'Rooftop Bar'],
                    ['beachfront', 'fa-water',             'Beachfront View'],
                    ['pet',        'fa-paw',               'Pet-Friendly'],
                    ['transfer',   'fa-van-shuttle',       'Airport Shuttle'],
                ] as $am)
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 border bg-light d-flex align-items-center gap-2.5" style="border-radius:4px; cursor:pointer;">
                        <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" id="am_{{ $am[0] }}"
                            {{ in_array($am[0], old('amenities', [])) ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                        <label for="am_{{ $am[0] }}" class="mb-0 text-dark fw-semibold d-flex align-items-center gap-2" style="cursor:pointer; font-size:12.5px;">
                            <i class="fa-solid {{ $am[1] }} text-primary" style="width:16px;"></i> <span>{!! $am[2] !!}</span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Dynamic Custom Amenity Tag Builder --}}
            <div class="p-3 border rounded bg-light mb-4" style="border-radius:4px;">
                <label class="form-label fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between" style="font-size:12.5px;">
                    <span><i class="fa-solid fa-plus-circle text-primary me-1"></i> + Add Custom Hotel Facility / Amenity</span>
                    <small class="text-muted">Type any custom facility and click + Add</small>
                </label>
                <div class="input-group input-group-sm mb-2" style="max-width:400px;">
                    <input type="text" id="adminCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi, Boat Safari..." style="font-size:12.5px; height:36px; border-radius:4px 0 0 4px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addAdminCustomAmenity();}">
                    <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12.5px; border-radius:0 4px 4px 0;" onclick="addAdminCustomAmenity()">
                        + Add
                    </button>
                </div>
                <div id="adminCustomAmenitiesContainer" class="d-flex flex-wrap gap-2"></div>
            </div>

            {{-- 6. ROOM TYPES SETUP --}}
            <div class="border-bottom pb-2.5 mb-4 mt-5">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:15px; color:#0f172a;">
                    <i class="fa-solid fa-bed text-success me-2.5" style="font-size:15px; width:20px;"></i>
                    <span>Initial Room Categories Setup (Optional)</span>
                </h5>
            </div>
            <div class="row g-3.5 mb-4">
                @foreach([
                    ['Standard Deluxe Room', 8500, 10, 2],
                    ['Executive Sea View Suite', 14500, 5, 2],
                    ['Presidential Family Suite', 24500, 2, 4],
                ] as $r)
                <div class="col-md-4">
                    <div class="p-3 border bg-light" style="border-radius:4px;">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size:12.5px;">{{ $r[0] }}</label>
                        <div class="row g-2.5">
                            <div class="col-6">
                                <label class="form-label text-secondary mb-1" style="font-size:11px;">Price/Night (BDT)</label>
                                <input type="number" name="room_price[]" class="form-control form-control-sm" placeholder="{{ $r[1] }}" style="font-size:12px; border-radius:4px; height:34px; padding:0 10px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-secondary mb-1" style="font-size:11px;">Available Qty</label>
                                <input type="number" name="room_qty[]" class="form-control form-control-sm" placeholder="{{ $r[2] }}" style="font-size:12px; border-radius:4px; height:34px; padding:0 10px;">
                            </div>
                            <input type="hidden" name="room_type[]" value="{{ $r[0] }}">
                            <input type="hidden" name="room_beds[]" value="{{ $r[3] }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-top mt-5" style="border-color:#e2e8f0 !important;">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-light text-secondary border fw-bold d-inline-flex align-items-center gap-2" style="border-radius:4px; font-size:13px; height:38px; padding:0 20px;">
                    <span>Cancel</span>
                </a>
                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary fw-bold d-inline-flex align-items-center gap-2" style="border-radius:4px; font-size:13px; height:38px; padding:0 20px;">
                    <i class="fa-solid fa-floppy-disk"></i> <span>Save as Draft</span>
                </button>
                <button type="submit" name="action" value="publish" class="btn btn-primary text-white fw-bold d-inline-flex align-items-center gap-2" style="background-color:var(--primary); border-radius:4px; font-size:13px; height:38px; padding:0 24px; border:none;">
                    <i class="fa-solid fa-rocket"></i> <span>Publish Listing Live</span>
                </button>
            </div>

        </div>

    </form>
</div>

@endsection

@section('scripts')
{{-- Leaflet Maps CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ── Geolocation Interactive Map Picker (Leaflet + OpenStreetMap) ──
let adminPropertyMap = null;
let adminPropertyMarker = null;

const cityCoordinates = {
    "Dhaka City": [23.8103, 90.4125],
    "Dhaka": [23.8103, 90.4125],
    "Cox's Bazar Sea Beach": [21.4272, 91.9702],
    "Cox's Bazar": [21.4272, 91.9702],
    "Sylhet": [24.8949, 91.8687],
    "Chittagong": [22.3569, 91.7832],
    "Kuakata": [21.8167, 90.1167],
    "Sundarbans & Mongla": [22.4833, 89.6000],
    "Sreemangal": [24.3065, 91.7296],
    "Bandarban": [22.1953, 92.2184],
    "Rangamati": [22.6533, 92.1753],
    "Sajek Valley": [23.3820, 92.2938]
};

document.addEventListener("DOMContentLoaded", function () {
    const latInput = document.getElementById('adminLatitudeInput');
    const lngInput = document.getElementById('adminLongitudeInput');
    
    let defaultLat = parseFloat(latInput?.value) || 21.4272;
    let defaultLng = parseFloat(lngInput?.value) || 91.9702;

    adminPropertyMap = L.map('adminMapPicker').setView([defaultLat, defaultLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(adminPropertyMap);

    adminPropertyMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(adminPropertyMap);

    adminPropertyMarker.on('dragend', function (e) {
        const position = adminPropertyMarker.getLatLng();
        latInput.value = position.lat.toFixed(6);
        lngInput.value = position.lng.toFixed(6);
    });

    adminPropertyMap.on('click', function (e) {
        adminPropertyMarker.setLatLng(e.latlng);
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
    });

    setupAdminGalleryDropzone();
});

function updateAdminMarkerFromInputs() {
    const lat = parseFloat(document.getElementById('adminLatitudeInput').value);
    const lng = parseFloat(document.getElementById('adminLongitudeInput').value);
    if (!isNaN(lat) && !isNaN(lng) && adminPropertyMap && adminPropertyMarker) {
        adminPropertyMarker.setLatLng([lat, lng]);
        adminPropertyMap.panTo([lat, lng]);
    }
}

function onAdminCityChanged(select) {
    const selected = select.options[select.selectedIndex];
    if (!selected) return;
    const lat = selected.getAttribute('data-lat');
    const lng = selected.getAttribute('data-lng');
    const cityName = selected.value;

    let targetLat = lat ? parseFloat(lat) : (cityCoordinates[cityName] ? cityCoordinates[cityName][0] : null);
    let targetLng = lng ? parseFloat(lng) : (cityCoordinates[cityName] ? cityCoordinates[cityName][1] : null);

    if (targetLat && targetLng) {
        document.getElementById('adminLatitudeInput').value = targetLat.toFixed(6);
        document.getElementById('adminLongitudeInput').value = targetLng.toFixed(6);
        if (adminPropertyMap && adminPropertyMarker) {
            adminPropertyMarker.setLatLng([targetLat, targetLng]);
            adminPropertyMap.setView([targetLat, targetLng], 13);
        }
    }
}

// ── Drag & Drop Multi-Image Uploader with Live Preview ──
function setupAdminGalleryDropzone() {
    const dropzone = document.getElementById('adminGalleryDropzone');
    if (!dropzone) return;

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.style.background = '#eff6ff';
            dropzone.style.borderColor = '#2563eb';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.style.background = '#f8fafc';
            dropzone.style.borderColor = '#93c5fd';
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleAdminGalleryFiles(files);
    });
}

function handleAdminGalleryFileSelect(input) {
    handleAdminGalleryFiles(input.files);
}

function handleAdminGalleryFiles(files) {
    const container = document.getElementById('adminGalleryPreviewContainer');
    if (!files || !container) return;

    Array.from(files).forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const card = document.createElement('div');
            card.className = 'position-relative border rounded p-1 shadow-sm bg-white';
            card.style.width = '100px';
            card.style.height = '85px';
            card.innerHTML = `
                <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:4px;" alt="preview">
                <span class="badge bg-dark position-absolute bottom-0 start-0 m-1 opacity-75" style="font-size:8px;">${(file.size / 1024).toFixed(0)} KB</span>
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 rounded-circle d-flex align-items-center justify-content-center m-1 shadow-sm" style="width:18px; height:18px; font-size:9px;" onclick="this.parentElement.remove()" title="Remove">✕</button>
            `;
            container.appendChild(card);
        };
        reader.readAsDataURL(file);
    });
}

function promptAdminCustomCategory() {
    const select = document.getElementById('adminPropTypeSelect');
    const custom = prompt("Enter new Property Category (e.g. Glamping Tent, Floating Cottage, Luxury Villa, Heritage Palace):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "✨ " + custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function promptAdminCustomCity() {
    const select = document.getElementById('adminPropCitySelect');
    const custom = prompt("Enter new Destination City or Region (e.g. Saint Martin Island, Kaptai Lake, Jaflong, Bangkok, Dubai):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "📍 " + custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function addAdminCustomAmenity() {
    const input = document.getElementById('adminCustomAmenityInput');
    const val = input.value.trim();
    if (!val) return;
    const container = document.getElementById('adminCustomAmenitiesContainer');
    const pill = document.createElement('span');
    pill.className = 'badge bg-white text-dark border d-inline-flex align-items-center gap-1.5 p-2 shadow-xs';
    pill.style.fontSize = '12px';
    pill.innerHTML = `<i class="fa-solid fa-circle-check text-success"></i> ${val} <input type="hidden" name="amenities[]" value="${val}"> <button type="button" class="btn-close ms-1" style="font-size:8px;" onclick="this.parentElement.remove()" title="Remove"></button>`;
    container.appendChild(pill);
    input.value = '';
}

function previewUrl(url) {
    const wrap = document.getElementById('imgPreviewWrap');
    const img = document.getElementById('imgPreview');
    if (url && url.startsWith('http')) {
        img.src = url;
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}
function previewFile(input) {
    const wrap = document.getElementById('imgPreviewWrap');
    const img  = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
