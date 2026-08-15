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
                    <select name="city" id="adminPropCitySelect" class="form-select" required style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <option value="Cox's Bazar Sea Beach" {{ old('city') == "Cox's Bazar Sea Beach" ? 'selected' : '' }}>Cox's Bazar Sea Beach</option>
                        <option value="Dhaka City" {{ old('city') == 'Dhaka City' ? 'selected' : '' }}>Dhaka City</option>
                        <option value="Sylhet & Sreemangal" {{ old('city') == 'Sylhet & Sreemangal' ? 'selected' : '' }}>Sylhet &amp; Sreemangal</option>
                        <option value="Sajek Valley & Rangamati" {{ old('city') == 'Sajek Valley & Rangamati' ? 'selected' : '' }}>Sajek Valley &amp; Rangamati</option>
                        <option value="Sundarbans & Mongla" {{ old('city') == 'Sundarbans & Mongla' ? 'selected' : '' }}>Sundarbans &amp; Mongla</option>
                        <option value="Kuakata Sunset Beach" {{ old('city') == 'Kuakata Sunset Beach' ? 'selected' : '' }}>Kuakata Sunset Beach</option>
                        <option value="Chittagong City" {{ old('city') == 'Chittagong City' ? 'selected' : '' }}>Chittagong City</option>
                        <option value="Bandarban Hill District" {{ old('city') == 'Bandarban Hill District' ? 'selected' : '' }}>Bandarban Hill District</option>
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
                        <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="Lat: 21.4272" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                        <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="Long: 91.9702" style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
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
                    <span>Pricing &amp; Booking Options</span>
                </h5>
            </div>
            <div class="row g-3.5 mb-4">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Base Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                    <div style="display:flex;">
                        <span class="input-group-text bg-light text-dark fw-bold" style="font-size:12.5px; border-radius:4px 0 0 4px; padding:0 14px; height:38px;">৳ BDT</span>
                        <input type="number" name="price_per_night" class="form-control"
                            value="{{ old('price_per_night') }}" placeholder="8500" required style="font-size:13px; border-radius:0 4px 4px 0; height:38px; padding:0 14px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Original Crossed-Out Rate (BDT)</label>
                    <div style="display:flex;">
                        <span class="input-group-text bg-light text-muted" style="font-size:12.5px; border-radius:4px 0 0 4px; padding:0 14px; height:38px;">৳ BDT</span>
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
                        <i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Upload Thumbnail Photo (Device)
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
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Video Tour URL (YouTube Embed / MP4)</label>
                    <input type="url" name="video_url" class="form-control"
                        value="{{ old('video_url') }}" placeholder="https://www.youtube.com/embed/..." style="font-size:13px; border-radius:4px; height:38px; padding:0 14px;">
                </div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">Gallery Image URLs (one per line)</label>
                    <textarea name="gallery_images" class="form-control" rows="3"
                        placeholder="https://images.unsplash.com/photo-1571896349842...&#10;https://images.unsplash.com/photo-1582719478250..." style="font-size:13px; border-radius:4px; padding:12px 14px;">{{ old('gallery_images') }}</textarea>
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
<script>
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

function previewImage(url) {
    const wrap = document.getElementById('imgPreviewWrap');
    const img = document.getElementById('imgPreview');
    if (url && url.startsWith('http')) {
        img.src = url;
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}
</script>
@endsection
