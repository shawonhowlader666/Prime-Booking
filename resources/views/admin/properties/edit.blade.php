@extends('layouts.admin')
@section('title', 'Edit: ' . $property->name . ' | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><strong style="color:#333;">Edit Listing</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title" style="font-size:16px;">Editing: {{ $property->name }}</h1>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('hotels.preview', $property->id) }}" target="_blank" class="btn-table-action primary" style="padding:6px 14px; background:#4f46e5; border-color:#4f46e5;">
                <i class="fa-solid fa-eye me-1"></i> Preview on Web <i class="fa-solid fa-external-link ms-1"></i>
            </a>
            <a href="{{ route('admin.properties.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:6px 14px;">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">
    <div style="max-width:920px; margin:0 auto;">

        @if(session('success'))
            <div class="admin-alert success mb-3">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="admin-alert error mb-3">
                <i class="fa-solid fa-circle-xmark me-1"></i>
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- STEP 1 --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-hotel me-1"></i> Basic Info &amp; Category
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Property Full Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $property->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label m-0">Property Type <span style="color:#ff4d4f;">*</span></label>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptAdminEditCategory()">
                                + Add Type
                            </button>
                        </div>
                        <select name="type" id="adminEditPropTypeSelect" class="form-select" required>
                            @foreach(['hotel' => 'Hotel & Resort', 'houseboat' => 'Sundarban Ship & Houseboat', 'homestay' => 'Home Stay & Eco Cottage', 'apartment' => 'Apartment / Suite', 'resort' => 'Beach Resort'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $property->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                            @if(!in_array($property->type, ['hotel', 'houseboat', 'homestay', 'apartment', 'resort']))
                                <option value="{{ $property->type }}" selected>✨ {{ ucfirst($property->type) }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label m-0">City / Tourist Destination <span style="color:#ff4d4f;">*</span></label>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptAdminEditCity()">
                                + Add City
                            </button>
                        </div>
                        <select name="city" id="adminEditPropCitySelect" class="form-select" required onchange="onAdminCityChanged(this)">
                            @if(isset($locations) && $locations->count())
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->name }}" 
                                            data-lat="{{ $loc->latitude }}" 
                                            data-lng="{{ $loc->longitude }}"
                                            {{ old('city', $property->city) == $loc->name ? 'selected' : '' }}>
                                        {{ $loc->name }} @if($loc->country && $loc->country != 'Bangladesh') ({{ $loc->country }}) @endif
                                    </option>
                                @endforeach
                            @endif
                            @if(!empty($property->city) && (!isset($locations) || !$locations->contains('name', $property->city)))
                                <option value="{{ $property->city }}" selected>📍 {{ $property->city }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Star Rating <span style="color:#ff4d4f;">*</span></label>
                        <select name="star_rating" class="form-select" required>
                            <option value="5" {{ old('star_rating', $property->star_rating) == 5 ? 'selected' : '' }}>★★★★★ — 5 Star Luxury</option>
                            <option value="4" {{ old('star_rating', $property->star_rating) == 4 ? 'selected' : '' }}>★★★★ — 4 Star Premium</option>
                            <option value="3" {{ old('star_rating', $property->star_rating) == 3 ? 'selected' : '' }}>★★★ — 3 Star Standard</option>
                            <option value="2" {{ old('star_rating', $property->star_rating) == 2 ? 'selected' : '' }}>★★ — 2 Star Budget</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Assign to Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">Admin Listed</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ old('vendor_id', $property->vendor_id) == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Full Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $property->address) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nearest Landmark / Distance</label>
                        <input type="text" name="nearest_landmark" class="form-control" value="{{ old('nearest_landmark', $property->nearest_landmark) }}" placeholder="e.g. 2 mins walk from Beach Point">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Real-Time GPS Coordinates (Lat, Long)</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="latitude" id="adminEditLatitudeInput" class="form-control" value="{{ old('latitude', $property->latitude) }}" placeholder="Lat: 21.4272" onchange="updateAdminEditMarkerFromInputs()">
                            <input type="text" name="longitude" id="adminEditLongitudeInput" class="form-control" value="{{ old('longitude', $property->longitude) }}" placeholder="Long: 91.9702" onchange="updateAdminEditMarkerFromInputs()">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2.5 border rounded bg-light" style="border-radius:6px;">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="fw-bold text-dark" style="font-size:12px;">
                                    <i class="fa-solid fa-map-pin text-danger me-1"></i> Interactive Geolocation Pin Picker (OpenStreetMap)
                                </span>
                                <small class="text-secondary" style="font-size:11px;">
                                    💡 Click on map or drag pin to update latitude &amp; longitude coordinates
                                </small>
                            </div>
                            <div id="adminEditMapPicker" style="height: 220px; width: 100%; border-radius: 4px; border: 1px solid #cbd5e1; z-index: 1;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Google Maps Embed / Location Link</label>
                        <input type="url" name="map_embed_url" class="form-control" value="{{ old('map_embed_url', $property->map_embed_url) }}" placeholder="https://maps.google.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Check-in / Check-out Hours</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="checkin_time" class="form-control" value="{{ old('checkin_time', $property->checkin_time ?? '14:00') }}" placeholder="Checkin: 14:00">
                            <input type="text" name="checkout_time" class="form-control" value="{{ old('checkout_time', $property->checkout_time ?? '12:00') }}" placeholder="Checkout: 12:00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hotel Contact Phone &amp; Email</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $property->contact_phone) }}" placeholder="+8801700000000">
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $property->contact_email) }}" placeholder="info@hotel.com">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Building Floors / Levels</label>
                        <input type="number" name="total_floors" class="form-control" value="{{ old('total_floors', $property->total_floors) }}" placeholder="e.g. 12 Floors" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Hotel Rooms</label>
                        <input type="number" name="total_rooms_count" class="form-control" value="{{ old('total_rooms_count', $property->total_rooms_count) }}" placeholder="e.g. 150 Rooms" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Year Built / Renovated</label>
                        <input type="number" name="year_built" class="form-control" value="{{ old('year_built', $property->year_built) }}" placeholder="e.g. 2023" min="1950" max="2030">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Front Desk Languages Spoken</label>
                        <div class="d-flex flex-wrap gap-2 p-2 rounded border bg-light">
                            @php $curLangs = (array)($property->languages_spoken ?? ['English', 'Bengali']); @endphp
                            @foreach(['English', 'Bengali', 'Hindi', 'Arabic', 'Chinese'] as $lang)
                                <label class="form-check-label d-inline-flex align-items-center gap-1.5 px-2 py-0.5 rounded border bg-white" style="font-size:11.5px; font-weight:600; color:#334155; cursor:pointer;">
                                    <input class="form-check-input m-0" type="checkbox" name="languages_spoken[]" value="{{ $lang }}" {{ in_array($lang, $curLangs) ? 'checked' : '' }} style="cursor:pointer;">
                                    {{ $lang }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pets Policy</label>
                        <select name="pets_policy" class="form-select">
                            <option value="Pets Not Allowed" {{ old('pets_policy', $property->pets_policy) == 'Pets Not Allowed' ? 'selected' : '' }}>🚫 Pets Not Allowed</option>
                            <option value="Pets Allowed" {{ old('pets_policy', $property->pets_policy) == 'Pets Allowed' ? 'selected' : '' }}>🐾 Pets Allowed (Free / Fee)</option>
                            <option value="Pets Allowed on Request" {{ old('pets_policy', $property->pets_policy) == 'Pets Allowed on Request' ? 'selected' : '' }}>💬 Pets Allowed on Request</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Pricing --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Pricing &amp; Visibility
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="price_per_night" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('price_per_night', $property->price_per_night) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Original Price (for Discount)</label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="original_price" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('original_price', $property->original_price) }}" placeholder="Leave blank if no discount">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contract Commission Rate (%) <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <input type="number" step="0.01" min="0" max="100" name="commission_rate" class="form-control" style="border-radius:6px 0 0 6px;"
                                value="{{ old('commission_rate', $property->commission_rate ?? 15.00) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Listing Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>Active — Published</option>
                            <option value="inactive" {{ old('status', $property->status) == 'inactive' ? 'selected' : '' }}>Inactive — Draft</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                {{ old('is_featured', $property->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isFeatured" style="font-size:13px;">
                                <i class="fa-solid fa-star" style="color:#ff9f43;"></i> Mark as Featured (Homepage Carousel)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Images --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-images me-1"></i> Property Images &amp; Media
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cover Photo (Device Upload)</label>
                        <input type="file" name="primary_image_file" class="form-control" accept="image/*" onchange="previewFile(this)">
                        <small class="text-muted" style="font-size:11px;">Upload new JPG/PNG from your computer or phone.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">OR Primary Image Direct URL</label>
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control"
                            value="{{ old('primary_image', $property->primary_image) }}"
                            placeholder="https://images.unsplash.com/photo-..."
                            oninput="previewUrl(this.value)">
                    </div>
                    @if($property->primary_image)
                    <div class="col-12">
                        <div style="margin-bottom:4px; display:flex; align-items:center; gap:12px; padding:6px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                            <img src="{{ $property->primary_image }}" style="height:55px; width:75px; border-radius:4px; border:1px solid #cbd5e1; object-fit:cover;" alt="Current Image">
                            <div>
                                <span class="d-block fw-bold text-dark" style="font-size:11.5px;">Current Active Cover Image</span>
                                <span style="font-size:11px; color:#64748b; word-break:break-all;">{{ $property->primary_image }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-12" id="imgPreviewWrap" style="display:none;">
                        <img id="imgPreview" src="" style="height:80px; border-radius:4px; border:1px solid #cbd5e1; object-fit:cover;" alt="Preview">
                    </div>

                    {{-- Drag & Drop Multi-Image Dropzone for Admin Gallery Photos --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">
                            <i class="fa-solid fa-photo-film text-primary me-1"></i> Add More Gallery Photos (Drag &amp; Drop Multi-Upload)
                        </label>
                        <div id="adminEditGalleryDropzone" class="p-4 border-2 border-dashed rounded text-center" style="background:#f8fafc; border-color:#93c5fd; cursor:pointer; transition:all 0.2s ease;" onclick="document.getElementById('adminEditGalleryFileInput').click()">
                            <input type="file" id="adminEditGalleryFileInput" name="gallery_image_files[]" multiple accept="image/*" class="d-none" onchange="handleAdminEditGalleryFileSelect(this)">
                            <i class="fa-solid fa-cloud-arrow-up text-primary fs-2 mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1" style="font-size:13.5px;">Drag &amp; drop new photos here or click to browse</h6>
                            <p class="text-muted m-0" style="font-size:11.5px;">Supports JPG, PNG, WEBP high-resolution photos</p>
                        </div>
                        {{-- Instant Preview Thumbnails Container --}}
                        <div id="adminEditGalleryPreviewContainer" class="d-flex flex-wrap gap-2 mt-2.5"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><i class="fa-solid fa-file-video text-danger me-1"></i> Upload Video Tour File (MP4)</label>
                        <input type="file" name="video_file" class="form-control" accept="video/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa-brands fa-youtube text-danger me-1"></i> OR Video Tour URL (YouTube / Embed)</label>
                        <input type="url" name="video_url" class="form-control"
                            value="{{ old('video_url', $property->video_url) }}" placeholder="e.g. https://www.youtube.com/embed/dQw4w9WgXcQ">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gallery Image URLs (one per line)</label>
                        <textarea name="gallery_images" class="form-control" rows="2"
                            placeholder="https://cdn.example.com/image1.jpg&#10;https://cdn.example.com/image2.jpg">{{ old('gallery_images', $galleryText) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Description --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-align-left me-1"></i> Description &amp; Guest Info
                </div>
                <textarea name="description" class="form-control" rows="5" required>{{ old('description', $property->description) }}</textarea>
            </div>

            {{-- STEP 5: Amenities --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-list-check me-1"></i> Amenities &amp; Facilities
                </div>
                <div class="row g-2">
                    @php $currentAmenities = old('amenities', $property->amenities ?? []); @endphp
                    @foreach([
                        ['wifi','fa-wifi','Free WiFi'],
                        ['pool','fa-person-swimming','Swimming Pool'],
                        ['parking','fa-car','Free Parking'],
                        ['ac','fa-snowflake','Air Conditioning'],
                        ['restaurant','fa-utensils','Restaurant'],
                        ['breakfast','fa-mug-hot','Breakfast Included'],
                        ['gym','fa-dumbbell','Fitness Center'],
                        ['spa','fa-spa','Spa & Wellness'],
                        ['bar','fa-wine-glass','Bar & Lounge'],
                        ['beachfront','fa-water','Beachfront / Sea View'],
                        ['pet','fa-paw','Pet-Friendly'],
                        ['transfer','fa-van-shuttle','Airport Transfer'],
                        ['laundry','fa-shirt','Laundry Service'],
                        ['elevator','fa-elevator','Elevator / Lift'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <div style="padding:8px 10px; border:1px solid {{ in_array($am[0], $currentAmenities) ? '#1890ff' : '#e8e8e8' }}; border-radius:6px; display:flex; align-items:center; gap:8px; cursor:pointer; background:{{ in_array($am[0], $currentAmenities) ? '#e6f7ff' : '#fafafa' }}; transition:all 0.15s;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" id="eam_{{ $am[0] }}"
                                {{ in_array($am[0], $currentAmenities) ? 'checked' : '' }}
                                style="cursor:pointer;">
                            <label for="eam_{{ $am[0] }}" style="font-size:12px; color:#334155; cursor:pointer; margin:0; display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid {{ $am[1] }}" style="color:var(--primary); width:14px;"></i> {{ $am[2] }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Dynamic Custom Amenity Tag Builder --}}
                <div class="p-3 border rounded bg-light mt-3" style="border-radius:4px;">
                    <label class="form-label fw-bold text-dark mb-1.5 d-flex align-items-center justify-content-between" style="font-size:12.5px;">
                        <span><i class="fa-solid fa-plus-circle text-primary me-1"></i> + Add Custom Hotel Facility / Amenity</span>
                        <small class="text-muted">Type any custom facility and click + Add</small>
                    </label>
                    <div class="input-group input-group-sm mb-2" style="max-width:400px;">
                        <input type="text" id="adminEditCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi, Boat Safari..." style="font-size:12.5px; height:36px; border-radius:4px 0 0 4px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addAdminEditCustomAmenity();}">
                        <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12.5px; border-radius:0 4px 4px 0;" onclick="addAdminEditCustomAmenity()">
                            + Add
                        </button>
                    </div>
                    <div id="adminEditCustomAmenitiesContainer" class="d-flex flex-wrap gap-2">
                        @php
                            $standardList = ['wifi','pool','parking','ac','restaurant','breakfast','gym','spa','bar','beachfront','pet','transfer','laundry','elevator'];
                        @endphp
                        @if(is_array($currentAmenities))
                            @foreach($currentAmenities as $cAm)
                                @if(!in_array($cAm, $standardList))
                                <span class="badge bg-white text-dark border d-inline-flex align-items-center gap-1.5 p-2 shadow-xs" style="font-size:12px;">
                                    <i class="fa-solid fa-circle-check text-success"></i> {{ $cAm }}
                                    <input type="hidden" name="amenities[]" value="{{ $cAm }}">
                                    <button type="button" class="btn-close ms-1" style="font-size:8px;" onclick="this.parentElement.remove()" title="Remove"></button>
                                </span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div style="display:flex; justify-content:flex-end; gap:10px; padding:16px 0; flex-wrap:wrap;">
                <a href="{{ route('admin.properties.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:8px 20px;">
                    Cancel
                </a>
                <button type="submit" class="btn-add-primary" style="padding:8px 28px;">
                    Save Changes <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
{{-- Leaflet Maps CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ── Geolocation Interactive Map Picker (Leaflet + OpenStreetMap) ──
let adminEditPropertyMap = null;
let adminEditPropertyMarker = null;

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
    const latInput = document.getElementById('adminEditLatitudeInput');
    const lngInput = document.getElementById('adminEditLongitudeInput');
    
    let defaultLat = parseFloat(latInput?.value) || 21.4272;
    let defaultLng = parseFloat(lngInput?.value) || 91.9702;

    adminEditPropertyMap = L.map('adminEditMapPicker').setView([defaultLat, defaultLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(adminEditPropertyMap);

    adminEditPropertyMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(adminEditPropertyMap);

    adminEditPropertyMarker.on('dragend', function (e) {
        const position = adminEditPropertyMarker.getLatLng();
        latInput.value = position.lat.toFixed(6);
        lngInput.value = position.lng.toFixed(6);
    });

    adminEditPropertyMap.on('click', function (e) {
        adminEditPropertyMarker.setLatLng(e.latlng);
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
    });

    setupAdminEditGalleryDropzone();
});

function updateAdminEditMarkerFromInputs() {
    const lat = parseFloat(document.getElementById('adminEditLatitudeInput').value);
    const lng = parseFloat(document.getElementById('adminEditLongitudeInput').value);
    if (!isNaN(lat) && !isNaN(lng) && adminEditPropertyMap && adminEditPropertyMarker) {
        adminEditPropertyMarker.setLatLng([lat, lng]);
        adminEditPropertyMap.panTo([lat, lng]);
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
        document.getElementById('adminEditLatitudeInput').value = targetLat.toFixed(6);
        document.getElementById('adminEditLongitudeInput').value = targetLng.toFixed(6);
        if (adminEditPropertyMap && adminEditPropertyMarker) {
            adminEditPropertyMarker.setLatLng([targetLat, targetLng]);
            adminEditPropertyMap.setView([targetLat, targetLng], 13);
        }
    }
}

// ── Drag & Drop Multi-Image Uploader with Live Preview ──
function setupAdminEditGalleryDropzone() {
    const dropzone = document.getElementById('adminEditGalleryDropzone');
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
        handleAdminEditGalleryFiles(files);
    });
}

function handleAdminEditGalleryFileSelect(input) {
    handleAdminEditGalleryFiles(input.files);
}

function handleAdminEditGalleryFiles(files) {
    const container = document.getElementById('adminEditGalleryPreviewContainer');
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

function promptAdminEditCategory() {
    const select = document.getElementById('adminEditPropTypeSelect');
    const custom = prompt("Enter new Property Category (e.g. Glamping Tent, Floating Cottage, Luxury Villa, Heritage Palace):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "✨ " + custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function promptAdminEditCity() {
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

function addAdminEditCustomAmenity() {
    const input = document.getElementById('adminEditCustomAmenityInput');
    const val = input.value.trim();
    if (!val) return;
    const container = document.getElementById('adminEditCustomAmenitiesContainer');
    const pill = document.createElement('span');
    pill.className = 'badge bg-white text-dark border d-inline-flex align-items-center gap-1.5 p-2 shadow-xs';
    pill.style.fontSize = '12px';
    pill.innerHTML = `<i class="fa-solid fa-circle-check text-success"></i> ${val} <input type="hidden" name="amenities[]" value="${val}"> <button type="button" class="btn-close ms-1" style="font-size:8px;" onclick="this.parentElement.remove()" title="Remove"></button>`;
    container.appendChild(pill);
    input.value = '';
}

function previewUrl(url) {
    const wrap = document.getElementById('imgPreviewWrap');
    const img  = document.getElementById('imgPreview');
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
