@extends('layouts.admin')
@section('title', 'Edit: ' . $property->name . ' | PRIME BOOKING Admin')

@section('content')

{{-- 1. Backdrop Overlay (covers the left 1/3 of the screen, click to close) --}}
<a href="{{ route('admin.properties.index') }}" class="edit-drawer-backdrop" title="Click to close editor and return to inventory"></a>

{{-- 2. Slide-Over Drawer Container (Takes 2/3 of screen width from the right) --}}
<div class="edit-drawer-container">

    {{-- DRAWER STICKY TOP HEADER --}}
    <div class="edit-drawer-header">
        <div class="edit-drawer-header-left">
            <span class="edit-drawer-header-badge">#PROP-{{ str_pad($property->id, 4, '0', STR_PAD_LEFT) }}</span>
            <h1 class="edit-drawer-header-title" title="{{ $property->name }}">
                <i class="fa-solid fa-pen-to-square text-primary me-1"></i> Editing: {{ $property->name }}
            </h1>
        </div>

        <div class="edit-drawer-header-actions">
            {{-- Manage Rooms Button (Crisp 4px, icon on left only) --}}
            <a href="{{ route('admin.rooms.index', $property->id) }}" class="btn-manage-rooms" title="Manage Rooms for this Property">
                <i class="fa-solid fa-bed"></i>
                <span>Manage Rooms ({{ $property->rooms->count() }})</span>
            </a>

            {{-- Preview on Web Button (Solid blue, crisp 4px, icon on left only) --}}
            <a href="{{ route('hotels.preview', $property->id) }}" target="_blank" class="btn-preview-web" title="View Live Hotel Page">
                <i class="fa-solid fa-eye"></i>
                <span>Preview on Web</span>
            </a>

            {{-- Close Drawer Button (X) --}}
            <a href="{{ route('admin.properties.index') }}" class="btn-drawer-close" title="Close Drawer (Esc)">
                <i class="fa-solid fa-xmark"></i>
            </a>
        </div>
    </div>

    {{-- DRAWER FORM (Flex column layout so footer stays sticky at bottom) --}}
    <form id="editPropertyForm" class="edit-drawer-form" action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- DRAWER SCROLLABLE BODY --}}
        <div class="edit-drawer-body">

            @if(session('success'))
                <div class="admin-alert success mb-3">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="admin-alert error mb-3">
                    <i class="fa-solid fa-circle-xmark me-1"></i>
                    {{ implode(', ', $errors->all()) }}
                </div>
            @endif

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
                        @php
                            $bdLocationGroups = app(\App\Services\Geo\BangladeshLocationService::class)->getGroupedDropdownOptions();
                            $currentCity = old('city', $property->city);
                        @endphp
                        <select name="city" id="adminEditPropCitySelect" class="form-select fw-semibold" required onchange="onAdminCityChanged(this)">
                            @foreach($bdLocationGroups as $groupName => $options)
                                <optgroup label="{{ $groupName }}">
                                    @foreach($options as $opt)
                                        <option value="{{ $opt['value'] }}"
                                                data-lat="{{ $opt['lat'] ?? '' }}"
                                                data-lng="{{ $opt['lng'] ?? '' }}"
                                                @selected($currentCity === $opt['value'])>
                                            {{ $opt['label'] }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            @if(!empty($currentCity))
                                <option value="{{ $currentCity }}" selected style="display:none;">📍 {{ $currentCity }}</option>
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
                    <div class="col-md-4">
                        <label class="form-label">Full Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $property->address) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Postal / ZIP Code</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $property->postal_code) }}" placeholder="e.g. 4700">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nearest Landmark / Distance</label>
                        <input type="text" name="nearest_landmark" class="form-control" value="{{ old('nearest_landmark', $property->nearest_landmark) }}" placeholder="e.g. 2 mins walk from Beach Point, 3.2 km from Airport">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Real-Time GPS Coordinates (Lat, Long)</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="latitude" id="adminEditLatitudeInput" class="form-control" value="{{ old('latitude', $property->latitude) }}" placeholder="Lat: 21.4272" onchange="updateAdminEditMarkerFromInputs()">
                            <input type="text" name="longitude" id="adminEditLongitudeInput" class="form-control" value="{{ old('longitude', $property->longitude) }}" placeholder="Long: 91.9702" onchange="updateAdminEditMarkerFromInputs()">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded bg-light" style="padding:12px 14px; border-radius:4px; border-color:#cbd5e1 !important;">
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
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">Front Desk Languages Spoken</label>
                        <div style="display:flex; flex-wrap:wrap; gap:8px; padding:8px 10px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:4px;">
                            @php $curLangs = (array)($property->languages_spoken ?? ['English', 'Bengali']); @endphp
                            @foreach(['English', 'Bengali', 'Hindi', 'Arabic', 'Chinese'] as $lang)
                                <label style="display:inline-flex; align-items:center; gap:8px; padding:4px 10px; border-radius:4px; border:1px solid #cbd5e1; background:#ffffff; font-size:12px; font-weight:600; color:#334155; cursor:pointer; user-select:none; margin:0;">
                                    <input type="checkbox" name="languages_spoken[]" value="{{ $lang }}" {{ in_array($lang, $curLangs) ? 'checked' : '' }} style="cursor:pointer; margin:0; width:15px; height:15px; accent-color:#1890ff;">
                                    <span>{{ $lang }}</span>
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
                    <div class="col-12">
                        <label class="form-label">
                            <i class="fa-solid fa-clipboard-check text-primary me-1"></i> House Rules, Check-in &amp; Deposit Policies (Agoda Extranet Standard)
                        </label>
                        <textarea name="house_rules" class="form-control" rows="2" placeholder="e.g. Valid national NID / passport required at check-in. Minimum check-in age is 18. Marriage certificate or proof of relationship required for couples. Security deposit payable in cash or card upon arrival.">{{ old('house_rules', $property->house_rules) }}</textarea>
                        <small class="text-muted" style="font-size:11px;">Guests will see this under 'Property Policies' on the public hotel page</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Cancellation &amp; Refund Policy
                        </label>
                        <input type="text" name="cancellation_policy" class="form-control" value="{{ old('cancellation_policy', $property->cancellation_policy) }}" placeholder="e.g. Free cancellation up to 48 hours before check-in. Non-refundable within 48 hours.">
                        <small class="text-muted" style="font-size:11px;">Displayed on checkout and printed on guest booking vouchers</small>
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
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                {{ old('is_featured', $property->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isFeatured" style="font-size:13px;">
                                <i class="fa-solid fa-star" style="color:#ff9f43;"></i> Mark as Featured (Homepage Carousel)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP: Agoda Card Highlights & Marketing Badges --}}
            <div class="form-card mb-3" style="border-left: 4px solid #2067e1;">
                <div class="form-section-title" style="color: #2067e1;">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Agoda Search Card Highlights &amp; Marketing Badges
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Card Feature Snippet / Key Highlights (Agoda Bottom Line)</label>
                        <input type="text" name="feature_snippet" class="form-control" 
                               value="{{ old('feature_snippet', $property->feature_snippet) }}" 
                               placeholder="e.g. Panoramic Sea Views from 12th Floor, Five-Minute Walk to Kolatoly Beach">
                        <small class="text-muted" style="font-size:11.5px;">
                            Appears at the bottom of the hotel card with a sparkle icon (Exact Agoda feature snippet).
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Top Photo Ribbon Badge (Top Left Corner of Image)</label>
                        <input type="text" name="ribbon_badge" class="form-control" 
                               value="{{ old('ribbon_badge', $property->ribbon_badge) }}" 
                               placeholder="e.g. Best rated 5-star hotels or Top Beach Resort (Leave blank for none)">
                        <small class="text-muted" style="font-size:11.5px;">
                            Displays an authentic Agoda blue ribbon over the hotel photo.
                        </small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Rooms Remaining / Urgency</label>
                        <input type="number" name="rooms_left" class="form-control" 
                               value="{{ old('rooms_left', $property->rooms_left ?? 0) }}" min="0" max="99">
                        <small class="text-muted" style="font-size:11px;">e.g. "1 still available" if 1-5</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Location Score (Out of 10)</label>
                        <input type="number" step="0.1" name="location_score" class="form-control" 
                               value="{{ old('location_score', $property->location_score) }}" min="0" max="10">
                        <small class="text-muted" style="font-size:11px;">e.g. 9.1 Location score</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Guest Rating Score (Out of 10)</label>
                        <input type="number" step="0.1" name="rating_score" class="form-control" 
                               value="{{ old('rating_score', $property->rating_score) }}" min="0" max="10">
                        <small class="text-muted" style="font-size:11px;">e.g. 9.2 Exceptional</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Total Reviews Count</label>
                        <input type="number" name="total_reviews" class="form-control" 
                               value="{{ old('total_reviews', $property->total_reviews ?? 0) }}" min="0">
                        <small class="text-muted" style="font-size:11px;">e.g. 1,280 reviews</small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input type="hidden" name="free_cancellation" value="0">
                            <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="freeCancelCheck"
                                {{ old('free_cancellation', $property->free_cancellation ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" for="freeCancelCheck" style="font-size:12.5px;">
                                <i class="fa-solid fa-check-circle"></i> + FREE CANCELLATION
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input type="hidden" name="no_credit_card_required" value="0">
                            <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" id="noCreditCheck"
                                {{ old('no_credit_card_required', $property->no_credit_card_required ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="noCreditCheck" style="font-size:12.5px;">
                                <i class="fa-regular fa-credit-card"></i> Book without a credit card
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input type="hidden" name="is_sold_out" value="0">
                            <input class="form-check-input" type="checkbox" name="is_sold_out" value="1" id="isSoldOutCheck"
                                {{ old('is_sold_out', $property->is_sold_out ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="isSoldOutCheck" style="font-size:12.5px;">
                                <i class="fa-solid fa-ban"></i> Mark as "Sold out on your dates!"
                            </label>
                        </div>
                    </div>
                </div>
            </div>


            {{-- STEP 3: Images & Media --}}
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
                        <input type="text" name="primary_image" id="primaryImgUrl" class="form-control"
                            value="{{ old('primary_image', $property->primary_image) }}"
                            placeholder="https://images.unsplash.com/photo-..."
                            oninput="previewUrl(this.value)">
                    </div>
                    @if($property->primary_image)
                    <div class="col-12">
                        <div style="margin-bottom:4px; display:flex; align-items:center; gap:12px; padding:6px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                            <img src="{{ media_url($property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80') }}"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80';"
                                 style="height:55px; width:75px; border-radius:4px; border:1px solid #cbd5e1; object-fit:cover;" alt="Current Image">
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

                    {{-- Interactive Current Photo Gallery & Category Manager --}}
                    @if(!empty($structuredGallery) && count($structuredGallery) > 0)
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm" style="background:#f8fafc; border:1px solid #cbd5e1 !important; border-radius:6px;">
                            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <span class="fw-bold text-dark" style="font-size:13px;">
                                        <i class="fa-solid fa-images text-primary me-1.5"></i> Manage Current Hotel Photos ({{ count($structuredGallery) }} items)
                                    </span>
                                    <small class="text-muted d-block" style="font-size:11.5px;">Change category, edit caption, replace file, set cover, or delete</small>
                                </div>
                                {{-- Agoda-style category filter tabs for fast photo management --}}
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-primary fw-bold admin-gal-filter-btn" data-cat="all" onclick="filterAdminGalleryCards('all', this)" style="font-size:11px; padding:2px 8px;">All ({{ count($structuredGallery) }})</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold admin-gal-filter-btn" data-cat="property_views" onclick="filterAdminGalleryCards('property_views', this)" style="font-size:11px; padding:2px 8px;">Property views</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold admin-gal-filter-btn" data-cat="rooms" onclick="filterAdminGalleryCards('rooms', this)" style="font-size:11px; padding:2px 8px;">Rooms</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold admin-gal-filter-btn" data-cat="facilities" onclick="filterAdminGalleryCards('facilities', this)" style="font-size:11px; padding:2px 8px;">Facilities</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold admin-gal-filter-btn" data-cat="dining" onclick="filterAdminGalleryCards('dining', this)" style="font-size:11px; padding:2px 8px;">Dining</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold admin-gal-filter-btn" data-cat="other" onclick="filterAdminGalleryCards('other', this)" style="font-size:11px; padding:2px 8px;">Other</button>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3" style="max-height: 480px; overflow-y: auto;">
                                    @php
                                        $currentPrimaryClean = ltrim(str_replace('/storage/', '', $property->primary_image ?? ''), '/');
                                    @endphp
                                    @foreach($structuredGallery as $gIdx => $gPhoto)
                                    @php
                                        $thisPhotoClean = ltrim(str_replace('/storage/', '', $gPhoto['url']), '/');
                                        $isCover = ($gPhoto['url'] === $property->primary_image || $thisPhotoClean === $currentPrimaryClean);
                                    @endphp
                                    <div class="col-md-4 col-sm-6 admin-photo-card" data-card-cat="{{ $gPhoto['category'] ?? 'property_views' }}">
                                        <div class="p-2 bg-white rounded border h-100 position-relative d-flex flex-column gap-1.5 shadow-xs" style="border-color:{{ $isCover ? '#3b82f6' : '#e2e8f0' }} !important; {{ $isCover ? 'box-shadow: 0 0 0 1px #3b82f6;' : '' }}">
                                            <input type="hidden" name="existing_gallery[{{ $gIdx }}][url]" value="{{ $gPhoto['url'] }}">
                                            <div class="position-relative overflow-hidden rounded" style="height: 110px; background:#0f172a;">
                                                <img src="{{ media_url($gPhoto['url']) }}"
                                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=300&q=80';"
                                                     class="w-100 h-100" style="object-fit: cover;" alt="Photo" loading="lazy">
                                                <span class="position-absolute top-0 start-0 m-1.5 badge bg-dark bg-opacity-75" style="font-size:10px;">#{{ $gIdx + 1 }}</span>
                                                @if($isCover)
                                                    <span class="position-absolute top-0 end-0 m-1.5 badge bg-primary" style="font-size:10px;">
                                                        <i class="fa-solid fa-star me-0.5"></i> Cover
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Set as Cover Radio Option --}}
                                            <div class="d-flex align-items-center justify-content-between pt-1">
                                                <div class="form-check form-check-sm mb-0">
                                                    <input class="form-check-input" type="radio" name="set_primary_image_url" value="{{ $gPhoto['url'] }}" id="setCover_{{ $gIdx }}"
                                                        {{ $isCover ? 'checked' : '' }}
                                                        onchange="document.getElementById('primaryImgUrl').value=this.value; if(window.previewUrl) previewUrl(this.value);">
                                                    <label class="form-check-label fw-bold text-primary" for="setCover_{{ $gIdx }}" style="font-size:11px; cursor:pointer;">
                                                        <i class="fa-regular fa-star me-0.5 text-warning"></i> Set as Cover
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Replace Image File In-Place --}}
                                            <div class="pt-0.5">
                                                <label class="form-label mb-0 text-muted" style="font-size:10.5px;">
                                                    <i class="fa-solid fa-arrow-rotate-right text-info me-0.5"></i> Replace file:
                                                </label>
                                                <input type="file" name="existing_gallery_replace[{{ $gIdx }}]" class="form-control form-control-sm" accept="image/*" style="font-size:11px; padding:2px 6px;">
                                            </div>

                                            <div>
                                                <label class="form-label mb-0.5" style="font-size:11px; font-weight:600; color:#475569;">Category:</label>
                                                <select name="existing_gallery[{{ $gIdx }}][category]" class="form-select form-select-sm fw-semibold" style="font-size:11.5px; padding:3px 8px;" onchange="this.closest('.admin-photo-card').setAttribute('data-card-cat', this.value);">
                                                    <option value="property_views" @selected(($gPhoto['category'] ?? '') === 'property_views')>Property views (Exterior/Lobby)</option>
                                                    <option value="facilities" @selected(($gPhoto['category'] ?? '') === 'facilities')>Facilities (Pool/Gym/Spa/Hall)</option>
                                                    <option value="dining" @selected(($gPhoto['category'] ?? '') === 'dining')>Dining (Restaurant/Cafe/Buffet)</option>
                                                    <option value="rooms" @selected(($gPhoto['category'] ?? '') === 'rooms')>Rooms &amp; Suites</option>
                                                    <option value="other" @selected(($gPhoto['category'] ?? '') === 'other')>Other</option>
                                                </select>
                                            </div>
                                                    <option value="property_views" @selected(($gPhoto['category'] ?? '') === 'property_views')>Property views (Exterior/Lobby)</option>
                                                    <option value="facilities" @selected(($gPhoto['category'] ?? '') === 'facilities')>Facilities (Pool/Gym/Spa/Hall)</option>
                                                    <option value="dining" @selected(($gPhoto['category'] ?? '') === 'dining')>Dining (Restaurant/Cafe/Buffet)</option>
                                                    <option value="rooms" @selected(($gPhoto['category'] ?? '') === 'rooms')>Rooms &amp; Suites</option>
                                                    <option value="other" @selected(($gPhoto['category'] ?? '') === 'other')>Other</option>
                                                </select>
                                            </div>
                                            <div>
                                                <input type="text" name="existing_gallery[{{ $gIdx }}][title]" value="{{ $gPhoto['title'] ?? '' }}" placeholder="Caption / Title" class="form-control form-control-sm" style="font-size:11.5px; padding:3px 8px;">
                                            </div>
                                            <div class="mt-auto pt-1 d-flex align-items-center justify-content-between border-top">
                                                <div class="form-check form-check-sm mb-0">
                                                    <input class="form-check-input" type="checkbox" name="existing_gallery[{{ $gIdx }}][delete]" value="1" id="delPhoto_{{ $gIdx }}">
                                                    <label class="form-check-label text-danger fw-semibold" for="delPhoto_{{ $gIdx }}" style="font-size:11px; cursor:pointer;">
                                                        <i class="fa-solid fa-trash-can me-1"></i> Delete photo
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Upload Category Selector for New Uploads --}}
                    <div class="col-12 mb-1">
                        <div class="rounded border d-flex flex-wrap align-items-center justify-content-between gap-2" style="padding:12px 14px; border-radius:4px; border-color:#bfdbfe !important; background:#eff6ff !important;">
                            <div>
                                <label class="form-label fw-bold text-dark mb-0" style="font-size:12.5px;">
                                    <i class="fa-solid fa-tag text-primary me-1"></i> Category for New Photos Below:
                                </label>
                                <span class="d-block text-muted" style="font-size:11px;">Select the category to automatically assign to files or URLs added below</span>
                            </div>
                            <div>
                                <select name="new_gallery_category" class="form-select form-select-sm fw-bold border-primary" style="min-width:230px; font-size:12px;">
                                    <option value="property_views" selected>Property views (Exterior, Lobby, Building)</option>
                                    <option value="facilities">Facilities (Pool, Gym, Spa, Ballroom)</option>
                                    <option value="dining">Dining (Restaurant, Cafe, Buffet)</option>
                                    <option value="rooms">Rooms &amp; Suites</option>
                                    <option value="other">Other / General</option>
                                </select>
                            </div>
                        </div>
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

                    <div class="col-12">
                        <label class="form-label">Gallery Image URLs (one per line)</label>
                        <textarea name="gallery_images" class="form-control" rows="2"
                            placeholder="https://cdn.example.com/image1.jpg&#10;https://cdn.example.com/image2.jpg">{{ old('gallery_images', $galleryText) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 3.5: Hotel Video Tour & Virtual Walkthrough --}}
            <div class="form-card mb-3">
                <div class="form-section-title d-flex align-items-center justify-content-between">
                    <span><i class="fa-solid fa-film text-danger me-1"></i> Hotel Video Tour &amp; Virtual Walkthrough</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size:11px;">Interactive Hero Media</span>
                </div>

                @if(!empty($property->video_url))
                <div class="mb-3 p-3 bg-light rounded border" style="border-color:#cbd5e1 !important; border-radius:4px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" style="font-size:13px;">
                            <i class="fa-solid fa-circle-play text-danger me-1"></i> Current Active Video Tour
                        </span>
                        <div class="form-check form-check-sm mb-0">
                            <input class="form-check-input" type="checkbox" name="remove_video" value="1" id="removeAdminVideo" style="cursor:pointer;">
                            <label class="form-check-label text-danger fw-bold" for="removeAdminVideo" style="font-size:12px; cursor:pointer; margin-left:4px;">
                                <i class="fa-solid fa-trash-can me-1"></i> Remove Current Video
                            </label>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            @php
                                $vUrl = $property->video_url;
                                $isYt = preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $vUrl, $ytm);
                                $ytId = $isYt ? $ytm[1] : null;
                            @endphp
                            @if($ytId)
                                <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm" style="max-height:220px; border-radius:4px;">
                                    <iframe src="https://www.youtube.com/embed/{{ $ytId }}" title="Video Tour" allowfullscreen></iframe>
                                </div>
                            @else
                                <video src="{{ media_url($vUrl) }}" controls class="w-100 rounded shadow-sm" style="max-height:220px; background:#000; border-radius:4px;" preload="metadata"></video>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div style="padding:16px 18px; background:#ffffff; border:1px solid #cbd5e1; border-radius:4px; font-size:12px;">
                                <div style="color:#64748b; font-weight:600; font-size:12px; margin-bottom:8px;">Active Video Source:</div>
                                <div style="padding:10px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; font-family:SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size:11.5px; color:#1e293b; word-break:break-all; line-height:1.5;">
                                    {{ $property->video_url }}
                                </div>
                                <div style="display:flex; align-items:center; gap:8px; color:#64748b; margin-top:10px; font-size:11.5px; line-height:1.4;">
                                    <i class="fa-solid fa-circle-info text-info" style="font-size:13px; flex-shrink:0;"></i>
                                    <span>This video plays with sound controls in the hotel hero gallery on both desktop and mobile.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">
                            <i class="fa-solid fa-file-video text-danger me-1"></i> Upload New Video Tour File (Device / Local)
                        </label>
                        <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                        <small class="text-muted" style="font-size:11px;">Upload MP4/WebM walkthrough (Max 100MB). Overrides current video.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">
                            <i class="fa-brands fa-youtube text-danger me-1"></i> OR Video Tour URL (YouTube / Embed / Stream)
                        </label>
                        <input type="text" name="video_url" id="adminVideoUrlInput" class="form-control"
                            value="{{ old('video_url', $property->video_url) }}"
                            placeholder="e.g. https://www.youtube.com/watch?v=... or direct MP4 link"
                            oninput="previewAdminVideoUrl(this.value)">
                        <small class="text-muted" style="font-size:11px;">Paste any YouTube link, Vimeo embed, or Cloudflare / direct MP4 URL.</small>
                    </div>
                    <div class="col-12" id="adminVideoUrlPreviewWrap" style="display:none;">
                        <div style="padding:14px 16px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:4px;">
                            <span class="d-block text-muted mb-2" style="font-size:11.5px; font-weight:600;"><i class="fa-solid fa-eye me-1 text-primary"></i> Live New Video URL Preview:</span>
                            <div id="adminVideoUrlPreviewFrame"></div>
                        </div>
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
                        <div style="padding:8px 10px; border:1px solid {{ in_array($am[0], $currentAmenities) ? '#1890ff' : '#e8e8e8' }}; border-radius:4px; display:flex; align-items:center; gap:8px; cursor:pointer; background:{{ in_array($am[0], $currentAmenities) ? '#e6f7ff' : '#fafafa' }}; transition:all 0.15s;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" id="eam_{{ $am[0] }}"
                                {{ in_array($am[0], $currentAmenities) ? 'checked' : '' }}
                                style="cursor:pointer; margin:0; width:15px; height:15px; flex-shrink:0; accent-color:#1890ff;">
                            <label for="eam_{{ $am[0] }}" style="font-size:12px; color:#334155; cursor:pointer; margin:0; display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid {{ $am[1] }}" style="color:var(--primary); width:14px; flex-shrink:0;"></i>
                                <span>{{ $am[2] }}</span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Dynamic Custom Amenity Tag Builder --}}
                <div class="p-3 border rounded bg-light mt-3" style="border-radius:4px;">
                    <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center justify-content-between" style="font-size:12.5px;">
                        <span><i class="fa-solid fa-plus-circle text-primary me-1"></i> + Add Custom Hotel Facility / Amenity</span>
                        <small class="text-muted">Type any custom facility and click + Add</small>
                    </label>
                    <div class="input-group input-group-sm mb-2" style="max-width:400px;">
                        <input type="text" id="adminEditCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi, Boat Safari..." style="font-size:12.5px; height:36px; border-radius:4px 0 0 4px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addAdminEditCustomAmenity();}">
                        <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12.5px; border-radius:0 4px 4px 0;" onclick="addAdminEditCustomAmenity()">
                            + Add
                        </button>
                    </div>
                    <div id="adminEditCustomAmenitiesContainer" style="display:flex; flex-wrap:wrap; gap:8px;">
                        @php
                            $standardList = ['wifi','pool','parking','ac','restaurant','breakfast','gym','spa','bar','beachfront','pet','transfer','laundry','elevator'];
                        @endphp
                        @if(is_array($currentAmenities))
                            @foreach($currentAmenities as $cAm)
                                @if(!in_array($cAm, $standardList))
                                <span class="custom-amenity-pill" style="display:inline-flex; align-items:center; gap:8px; padding:6px 12px; background:#ffffff; border:1px solid #cbd5e1; border-radius:4px; font-size:12px; font-weight:600; color:#1e293b; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                                    <i class="fa-solid fa-circle-check text-success" style="font-size:13px; flex-shrink:0; margin:0;"></i>
                                    <span>{{ $cAm }}</span>
                                    <input type="hidden" name="amenities[]" value="{{ $cAm }}">
                                    <button type="button" class="btn-close" style="font-size:8px; margin-left:4px; cursor:pointer;" onclick="this.parentElement.remove()" title="Remove"></button>
                                </span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- DRAWER STICKY BOTTOM ACTION BAR --}}
        <div class="edit-drawer-footer">
            <div class="edit-drawer-footer-hint">
                <i class="fa-solid fa-shield-halved text-success me-1.5" style="font-size:13px;"></i>
                <span>Live Real-Time Hotel Editor</span>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('admin.properties.index') }}" class="btn-header-action" style="padding:0 18px; height:36px; font-size:13px; border-radius:4px;">
                    <i class="fa-solid fa-xmark"></i> <span>Cancel</span>
                </a>
                <button type="submit" class="btn-add-primary" style="padding:0 24px; height:36px; font-size:13px; font-weight:600; border-radius:4px;">
                    <i class="fa-solid fa-check"></i> <span>Save Changes</span>
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
    const select = document.getElementById('adminEditPropCitySelect') || document.getElementById('adminPropCitySelect');
    const custom = prompt("Enter new Destination City or Region (e.g. Saint Martin Island, Kaptai Lake, Jaflong, Bangkok, Dubai):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "📍 " + custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function previewAdminVideoUrl(url) {
    const wrap = document.getElementById('adminVideoUrlPreviewWrap');
    const frame = document.getElementById('adminVideoUrlPreviewFrame');
    if (!wrap || !frame) return;
    const trimmed = (url || '').trim();
    if (!trimmed) {
        wrap.style.display = 'none';
        frame.innerHTML = '';
        return;
    }
    const ytMatch = trimmed.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i);
    if (ytMatch && ytMatch[1]) {
        wrap.style.display = 'block';
        frame.innerHTML = `<div class="ratio ratio-16x9 rounded overflow-hidden" style="max-height:220px;"><iframe src="https://www.youtube.com/embed/${ytMatch[1]}" frameborder="0" allowfullscreen></iframe></div>`;
    } else if (trimmed.match(/\.(mp4|webm|ogg)($|\?)/i) || trimmed.startsWith('/storage/')) {
        wrap.style.display = 'block';
        frame.innerHTML = `<video src="${trimmed}" controls class="w-100 rounded" style="max-height:220px; background:#000;"></video>`;
    } else {
        wrap.style.display = 'none';
    }
}

function addAdminEditCustomAmenity() {
    const input = document.getElementById('adminEditCustomAmenityInput');
    const val = input.value.trim();
    if (!val) return;
    const container = document.getElementById('adminEditCustomAmenitiesContainer');
    const pill = document.createElement('span');
    pill.className = 'custom-amenity-pill';
    pill.style.cssText = 'display:inline-flex; align-items:center; gap:8px; padding:6px 12px; background:#ffffff; border:1px solid #cbd5e1; border-radius:4px; font-size:12px; font-weight:600; color:#1e293b; box-shadow:0 1px 2px rgba(0,0,0,0.04);';
    pill.innerHTML = `<i class="fa-solid fa-circle-check text-success" style="font-size:13px; flex-shrink:0; margin:0;"></i> <span>${val}</span> <input type="hidden" name="amenities[]" value="${val}"> <button type="button" class="btn-close" style="font-size:8px; margin-left:4px; cursor:pointer;" onclick="this.parentElement.remove()" title="Remove"></button>`;
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

function filterAdminGalleryCards(cat, btn) {
    document.querySelectorAll('.admin-gal-filter-btn').forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-primary');

    document.querySelectorAll('.admin-photo-card').forEach(card => {
        if (cat === 'all' || card.getAttribute('data-card-cat') === cat) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
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

// ── Close 2/3 Drawer on ESC key ──
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.location.href = "{{ route('admin.properties.index') }}";
    }
});

// ── Invalidate Leaflet Map Size after drawer slide-in animation ──
setTimeout(function() {
    if (adminEditPropertyMap) {
        adminEditPropertyMap.invalidateSize();
    }
}, 350);
</script>
@endsection
