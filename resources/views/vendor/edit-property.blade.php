@extends('layouts.vendor')
@section('title', 'Edit: ' . $property->name . ' | Vendor Portal')

@section('content')

<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1280px; margin: 0 auto;">

    {{-- Breadcrumbs & Top Action Bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 12.5px;">
                    <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendor.properties.index') }}" class="text-decoration-none text-muted">My Properties</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">#PROP-{{ str_pad($property->id, 4, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h4 fw-bold text-dark m-0" style="letter-spacing: -0.02em;">
                    <i class="fa-solid fa-hotel text-primary me-2"></i>Editing: {{ $property->name }}
                </h1>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1" style="font-size: 12px;">
                    #PROP-{{ str_pad($property->id, 4, '0', STR_PAD_LEFT) }}
                </span>
                <span class="badge bg-{{ ($property->status ?? 'active') === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ ($property->status ?? 'active') === 'active' ? 'success' : 'secondary' }} border border-{{ ($property->status ?? 'active') === 'active' ? 'success' : 'secondary' }} border-opacity-25 px-2.5 py-1 text-capitalize" style="font-size: 12px;">
                    <i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> {{ $property->status ?? 'active' }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('vendor.rooms.index', $property->id) }}" class="btn btn-outline-primary fw-semibold shadow-xs" style="font-size: 13px; height: 38px; display: inline-flex; align-items: center; gap: 7px; border-radius: 6px;">
                <i class="fa-solid fa-bed"></i>
                <span>Manage Rooms ({{ $property->rooms->count() }})</span>
            </a>

            <a href="{{ route('hotels.preview', $property->id) }}" target="_blank" class="btn btn-primary fw-semibold shadow-xs" style="font-size: 13px; height: 38px; display: inline-flex; align-items: center; gap: 7px; border-radius: 6px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Preview on Web</span>
            </a>

            <a href="{{ route('vendor.dashboard') }}" class="btn btn-light border fw-semibold text-muted" style="font-size: 13px; height: 38px; display: inline-flex; align-items: center; gap: 7px; border-radius: 6px;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>

    {{-- Main Form --}}
    <form id="vendorEditPropertyForm" action="{{ route('vendor.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="vendor-edit-property-body">

            {{-- Step 1 --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-hotel me-1"></i> Basic Info
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Property Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $property->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label m-0">Type</label>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptVendorEditCategory()">
                                + Add Type
                            </button>
                        </div>
                        <select name="type" id="vendorEditPropTypeSelect" class="form-select">
                            @foreach(['hotel' => 'Hotel & Resort', 'houseboat' => 'Ship & Houseboat', 'homestay' => 'Home Stay & Cottage', 'apartment' => 'Apartment / Suite', 'resort' => 'Beach Resort'] as $v => $l)
                                <option value="{{ $v }}" {{ old('type', $property->type) == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                            @if(!in_array($property->type, ['hotel', 'houseboat', 'homestay', 'apartment', 'resort']))
                                <option value="{{ $property->type }}" selected>{{ ucfirst($property->type) }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label m-0">City / Destination</label>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptVendorEditCity()">
                                + Add City
                            </button>
                        </div>
                        @php
                            $bdLocationGroups = app(\App\Services\Geo\BangladeshLocationService::class)->getGroupedDropdownOptions();
                            $currentCity = old('city', $property->city);
                        @endphp
                        <select name="city" id="vendorEditPropCitySelect" class="form-select fw-semibold" onchange="onVendorCityChanged(this)">
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
                    <div class="col-md-6">
                        <label class="form-label">Star Rating</label>
                        <select name="star_rating" class="form-select">
                            <option value="5" {{ old('star_rating', $property->star_rating) == 5 ? 'selected' : '' }}>5 Stars — Luxury Property</option>
                            <option value="4" {{ old('star_rating', $property->star_rating) == 4 ? 'selected' : '' }}>4 Stars — Premium Property</option>
                            <option value="3" {{ old('star_rating', $property->star_rating) == 3 ? 'selected' : '' }}>3 Stars — Standard Property</option>
                            <option value="2" {{ old('star_rating', $property->star_rating) == 2 ? 'selected' : '' }}>2 Stars — Economy Property</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $property->address) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nearest Landmark / Distance</label>
                        <input type="text" name="nearest_landmark" class="form-control" value="{{ old('nearest_landmark', $property->nearest_landmark) }}" placeholder="e.g. 2 mins walk from Beach Point">
                    </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">What's Nearby (shown on the hotel page &amp; map)</label>
                    <textarea name="nearby_places_text" class="form-control" rows="5" style="font-size:13px; font-family:monospace;"
                        placeholder="One place per line:  Name | distance | category&#10;Kolatoli Beach Point | 200 m | beach&#10;Cox's Bazar Airport (CXB) | 3.8 km | airport&#10;Sugandha Market | 1.1 | shopping">{{ old('nearby_places_text', $property->nearby_places_text) }}</textarea>
                    <small class="text-muted" style="font-size:11.5px;">Distance in km or m. Category (optional): {{ implode(', ', array_keys(\App\Models\Property::NEARBY_CATEGORIES)) }}.</small>
                    @error('nearby_places_text')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                    <div class="col-md-6">
                        <label class="form-label">Real-Time GPS Coordinates (Lat, Long)</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="latitude" id="vendorEditLatitudeInput" class="form-control" value="{{ old('latitude', $property->latitude) }}" placeholder="Lat: 21.4272" onchange="updateEditMarkerFromInputs()">
                            <input type="text" name="longitude" id="vendorEditLongitudeInput" class="form-control" value="{{ old('longitude', $property->longitude) }}" placeholder="Long: 91.9702" onchange="updateEditMarkerFromInputs()">
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
                            <div id="vendorEditMapPicker" style="height: 220px; width: 100%; border-radius: 4px; border: 1px solid #cbd5e1; z-index: 1;"></div>
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
                            <option value="Pets Not Allowed" {{ old('pets_policy', $property->pets_policy) == 'Pets Not Allowed' ? 'selected' : '' }}>Pets Not Allowed</option>
                            <option value="Pets Allowed" {{ old('pets_policy', $property->pets_policy) == 'Pets Allowed' ? 'selected' : '' }}>Pets Allowed (Free / Fee)</option>
                            <option value="Pets Allowed on Request" {{ old('pets_policy', $property->pets_policy) == 'Pets Allowed on Request' ? 'selected' : '' }}>Pets Allowed on Request</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Cancellation &amp; Refund Policy
                        </label>
                        <input type="text" name="cancellation_policy" class="form-control" value="{{ old('cancellation_policy', $property->cancellation_policy) }}" placeholder="e.g. Free cancellation up to 48 hours before check-in. Non-refundable within 48 hours.">
                    </div>
                    <div class="col-12">
                        <label class="form-label">
                            <i class="fa-solid fa-clipboard-check text-primary me-1"></i> House Rules &amp; Deposit Policies
                        </label>
                        <textarea name="house_rules" class="form-control" rows="2" placeholder="e.g. Valid national NID / passport required at check-in. Minimum check-in age is 18. Marriage certificate or proof of relationship required for couples.">{{ old('house_rules', $property->house_rules) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Pricing
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="price_per_night" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('price_per_night', $property->price_per_night) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Original Price (for Discount Badge)</label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="original_price" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('original_price', $property->original_price) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prime Booking Search Card Highlights & Marketing Badges --}}
            <div class="form-card mb-3" style="border-left: 4px solid #2067e1;">
                <div class="form-section-title" style="color: #2067e1;">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Prime Booking Card Highlights &amp; Marketing Badges
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Card Feature Snippet / Key Highlights (Card Bottom Highlight)</label>
                        <input type="text" name="feature_snippet" class="form-control" 
                               value="{{ old('feature_snippet', $property->feature_snippet) }}" 
                               placeholder="e.g. Panoramic Sea Views from 12th Floor, Five-Minute Walk to Kolatoly Beach">
                        <small class="text-muted" style="font-size:11.5px;">
                            Appears at the bottom of the hotel card with a sparkle icon (Feature highlight snippet).
                        </small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input type="hidden" name="free_cancellation" value="0">
                            <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="vendorFreeCancelCheck"
                                {{ old('free_cancellation', $property->free_cancellation ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" for="vendorFreeCancelCheck" style="font-size:12.5px;">
                                <i class="fa-solid fa-check-circle"></i> + FREE CANCELLATION
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input type="hidden" name="no_credit_card_required" value="0">
                            <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" id="vendorNoCreditCheck"
                                {{ old('no_credit_card_required', $property->no_credit_card_required ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="vendorNoCreditCheck" style="font-size:12.5px;">
                                <i class="fa-regular fa-credit-card"></i> Book without a credit card
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-2">
                            <input type="hidden" name="is_sold_out" value="0">
                            <input class="form-check-input" type="checkbox" name="is_sold_out" value="1" id="vendorIsSoldOutCheck"
                                {{ old('is_sold_out', $property->is_sold_out ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="vendorIsSoldOutCheck" style="font-size:12.5px;">
                                <i class="fa-solid fa-ban"></i> Mark as "Sold out on your dates!"
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Photos, Gallery & Video Media --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-images me-1"></i> Photos, Gallery &amp; Visual Media
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cover Photo (Device Upload)</label>
                        <input type="file" name="primary_image_file" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size:11px;">Upload new JPG/PNG from your computer or phone.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">OR Primary Image Direct URL</label>
                        <input type="text" name="primary_image" id="vendorPrimaryImgUrl" class="form-control"
                            value="{{ old('primary_image', $property->primary_image) }}"
                            placeholder="https://images.unsplash.com/...">
                    </div>
                    @if($property->primary_image)
                    <div class="col-12">
                        <div style="padding:8px 12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; display:flex; align-items:center; gap:12px;">
                            <img src="{{ media_url($property->primary_image, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=120&h=80&fit=crop') }}"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=120&h=80&fit=crop';"
                                 style="height:60px; width:80px; border-radius:6px; border:1px solid #cbd5e1; object-fit:cover;">
                            <div>
                                <strong style="font-size:12px; color:#1e293b;">Current Active Cover Photo</strong>
                                <div style="font-size:11px; color:#64748b; word-break:break-all;">{{ $property->primary_image }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Enterprise Hotel Photo Gallery & Unified Manager --}}
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm" style="background:#ffffff; border:1px solid #cbd5e1 !important; border-radius:8px;">
                            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <span class="fw-bold text-dark" style="font-size:13.5px;">
                                        <i class="fa-solid fa-images text-primary me-1.5"></i> Hotel Photos Gallery (<span id="vendorGalleryCountBadge">{{ count($structuredGallery ?? []) }}</span>)
                                    </span>
                                    <small class="text-muted d-block" style="font-size:11.5px;">
                                        <i class="fa-solid fa-arrows-up-down-left-right text-primary me-1"></i> Drag cards to reorder &bull; #1 is Cover &bull; Click + Add Hotel Photos or drop images to add
                                    </small>
                                </div>
                                {{-- Category Filter Pills --}}
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-primary fw-bold vendor-gal-filter-btn" data-cat="all" onclick="filterVendorGalleryCards('all', this)" style="font-size:11px; padding:3px 10px; border-radius:4px;">All (<span id="vendorFilterCountAll">{{ count($structuredGallery ?? []) }}</span>)</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold vendor-gal-filter-btn" data-cat="property_views" onclick="filterVendorGalleryCards('property_views', this)" style="font-size:11px; padding:3px 10px; border-radius:4px;">Property views</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold vendor-gal-filter-btn" data-cat="rooms" onclick="filterVendorGalleryCards('rooms', this)" style="font-size:11px; padding:3px 10px; border-radius:4px;">Rooms</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold vendor-gal-filter-btn" data-cat="facilities" onclick="filterVendorGalleryCards('facilities', this)" style="font-size:11px; padding:3px 10px; border-radius:4px;">Facilities</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold vendor-gal-filter-btn" data-cat="dining" onclick="filterVendorGalleryCards('dining', this)" style="font-size:11px; padding:3px 10px; border-radius:4px;">Dining</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary fw-semibold vendor-gal-filter-btn" data-cat="other" onclick="filterVendorGalleryCards('other', this)" style="font-size:11px; padding:3px 10px; border-radius:4px;">Other</button>
                                </div>
                            </div>

                            <div class="card-body p-3">
                                {{-- Photos Grid: Contains existing cards + new preview cards + '+ Add Hotel Photos' tile at end --}}
                                <div class="row g-3" id="vendorHotelGalleryGrid">
                                    @php
                                        $currentPrimaryClean = ltrim(str_replace('/storage/', '', $property->primary_image ?? ''), '/');
                                    @endphp
                                    @foreach($structuredGallery ?? [] as $gIdx => $gPhoto)
                                    @php
                                        $thisPhotoClean = ltrim(str_replace('/storage/', '', $gPhoto['url']), '/');
                                        $isCover = ($gPhoto['url'] === $property->primary_image || $thisPhotoClean === $currentPrimaryClean || ($gIdx === 0 && empty($property->primary_image)));
                                    @endphp
                                    <div class="col-lg-3 col-md-4 col-sm-6 hotel-photo-card-item" data-card-cat="{{ $gPhoto['category'] ?? 'property_views' }}" data-photo-idx="{{ $gIdx }}" draggable="true">
                                        <div class="p-2 bg-white rounded border h-100 position-relative d-flex flex-column gap-1.5 hotel-photo-box {{ $isCover ? 'is-cover-card' : '' }}" style="border-color:{{ $isCover ? '#3b82f6' : '#cbd5e1' }} !important; cursor:grab; transition:all 0.2s ease;">
                                            <input type="hidden" name="existing_gallery[{{ $gIdx }}][url]" value="{{ $gPhoto['url'] }}" class="photo-url-input">

                                            {{-- Order Badge + Cover Indicator --}}
                                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 5;">
                                                <span class="badge photo-order-badge {{ $isCover ? 'bg-primary' : 'bg-dark bg-opacity-75' }}" style="font-size:10.5px; font-weight:700; padding:3px 7px;">
                                                    @if($isCover)
                                                        <i class="fa-solid fa-star text-warning me-0.5"></i> #1 (Cover)
                                                    @else
                                                        #{{ $gIdx + 1 }}
                                                    @endif
                                                </span>
                                            </div>

                                            {{-- Drag Grip Handle Hint --}}
                                            <div class="position-absolute top-0 end-0 m-2 px-1.5 py-0.5 rounded bg-white bg-opacity-90 text-secondary hotel-drag-handle" style="font-size:11px; z-index:5; pointer-events:none; box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                                                <i class="fa-solid fa-grip-vertical"></i>
                                            </div>

                                            {{-- Image Thumbnail Container --}}
                                            <div class="position-relative overflow-hidden rounded" style="height: 120px; background:#0f172a;">
                                                <img src="{{ media_url($gPhoto['url']) }}"
                                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=300&q=80';"
                                                     class="w-100 h-100" style="object-fit: cover;" alt="Photo" loading="lazy">

                                                {{-- Will Delete Overlay --}}
                                                <div class="position-absolute inset-0 photo-delete-overlay d-none flex-column align-items-center justify-content-center text-white" style="top:0;left:0;right:0;bottom:0; background:rgba(239, 68, 68, 0.85); font-weight:700; font-size:12px; z-index:4;">
                                                    <i class="fa-solid fa-trash-can fs-5 mb-1"></i>
                                                    <span>Will Be Deleted</span>
                                                </div>
                                            </div>

                                            {{-- Set as Cover Radio Option --}}
                                            <div class="d-flex align-items-center justify-content-between pt-1">
                                                <div class="form-check form-check-sm mb-0">
                                                    <input class="form-check-input hotel-cover-radio" type="radio" name="set_primary_image_url" value="{{ $gPhoto['url'] }}" id="vendorSetCover_{{ $gIdx }}"
                                                        {{ $isCover ? 'checked' : '' }}
                                                        onchange="onSelectVendorHotelCoverPhoto(this, '{{ $gPhoto['url'] }}')">
                                                    <label class="form-check-label fw-bold text-primary" for="vendorSetCover_{{ $gIdx }}" style="font-size:11px; cursor:pointer;">
                                                        <i class="fa-regular fa-star me-0.5 text-warning"></i> Set as Cover
                                                    </label>
                                                </div>
                                                <span class="text-muted small" style="font-size:10px; cursor:grab;">
                                                    <i class="fa-solid fa-arrows-up-down-left-right me-0.5"></i> Drag
                                                </span>
                                            </div>

                                            {{-- Category Selector --}}
                                            <div class="pt-0.5">
                                                <label class="form-label mb-0 text-muted" style="font-size:10.5px; font-weight:600;">Category:</label>
                                                <select name="existing_gallery[{{ $gIdx }}][category]" class="form-select form-select-sm fw-semibold photo-category-select" style="font-size:11px; padding:3px 7px;" onchange="this.closest('.hotel-photo-card-item').setAttribute('data-card-cat', this.value);">
                                                    <option value="property_views" @selected(($gPhoto['category'] ?? '') === 'property_views')>Property views (Exterior/Lobby)</option>
                                                    <option value="facilities" @selected(($gPhoto['category'] ?? '') === 'facilities')>Facilities (Pool/Gym/Spa/Hall)</option>
                                                    <option value="dining" @selected(($gPhoto['category'] ?? '') === 'dining')>Dining (Restaurant/Cafe/Buffet)</option>
                                                    <option value="rooms" @selected(($gPhoto['category'] ?? '') === 'rooms')>Rooms &amp; Suites</option>
                                                    <option value="other" @selected(($gPhoto['category'] ?? '') === 'other')>Other</option>
                                                </select>
                                            </div>

                                            {{-- Caption / Title --}}
                                            <div>
                                                <input type="text" name="existing_gallery[{{ $gIdx }}][title]" value="{{ $gPhoto['title'] ?? '' }}" placeholder="Caption / Title" class="form-control form-control-sm photo-title-input" style="font-size:11px; padding:3px 7px;">
                                            </div>

                                            {{-- In-Place Replace File (Optional) --}}
                                            <div class="pt-0.5">
                                                <label class="form-label mb-0 text-muted" style="font-size:10px;">
                                                    <i class="fa-solid fa-arrow-rotate-right text-info me-0.5"></i> Replace file:
                                                </label>
                                                <input type="file" name="existing_gallery_replace[{{ $gIdx }}]" class="form-control form-control-sm photo-replace-input" accept="image/*" style="font-size:10.5px; padding:2px 5px;">
                                            </div>

                                            {{-- Delete Checkbox Footer --}}
                                            <div class="mt-auto pt-1 d-flex align-items-center justify-content-between border-top">
                                                <div class="form-check form-check-sm mb-0">
                                                    <input class="form-check-input photo-delete-check" type="checkbox" name="existing_gallery[{{ $gIdx }}][delete]" value="1" id="vendorDelPhoto_{{ $gIdx }}" onchange="handleHotelPhotoDeleteToggle(this)">
                                                    <label class="form-check-label text-danger fw-semibold" for="vendorDelPhoto_{{ $gIdx }}" style="font-size:11px; cursor:pointer;">
                                                        <i class="fa-solid fa-trash-can me-1"></i> Delete photo
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    {{-- The "+ Add Hotel Photos" Tile (Always matches photo card dimensions at the end of the grid) --}}
                                    <div class="col-lg-3 col-md-4 col-sm-6 hotel-photo-add-tile-col" id="vendorHotelPhotoAddTileCol">
                                        <div class="p-3 rounded border-2 border-dashed h-100 d-flex flex-column align-items-center justify-content-center text-center hotel-photo-add-tile"
                                             onclick="document.getElementById('vendorHotelGalleryFileInput').click()"
                                             ondragover="handleHotelPhotoTileDragOver(event, this)"
                                             ondragleave="handleHotelPhotoTileDragLeave(event, this)"
                                             ondrop="handleVendorHotelPhotoTileDrop(event, this)"
                                             style="border-color:#3b82f6 !important; background:#eff6ff; min-height:280px; cursor:pointer; transition:all 0.2s ease;">
                                            
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width:48px; height:48px; font-size:20px;">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                            <span class="fw-bold text-primary" style="font-size:13.5px; line-height:1.2;">+ Add Hotel Photos</span>
                                            <small class="text-muted mt-1" style="font-size:11px;">Click to browse or<br>drag &amp; drop photos here</small>
                                            <span class="badge bg-primary bg-opacity-15 text-primary mt-2.5 px-2 py-1" style="font-size:10px; font-weight:600;">
                                                <i class="fa-solid fa-layer-group me-1"></i> Multi-Upload
                                            </span>

                                            {{-- Hidden Multi-File Input --}}
                                            <input type="file" id="vendorHotelGalleryFileInput" name="gallery_image_files[]" multiple accept="image/*" class="d-none" onchange="handleVendorHotelGalleryFileSelect(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted" style="font-size:12px;">Gallery URLs (one URL per line fallback)</label>
                        <textarea name="gallery_images" class="form-control" rows="2" style="font-size:12px;" placeholder="https://... (one per line)">{{ old('gallery_images', $galleryText ?? (is_array($property->images) ? implode("\n", $property->images) : '')) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Hotel Video Tour & Walkthrough --}}
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
                            <input class="form-check-input" type="checkbox" name="remove_video" value="1" id="removeVendorVideo" style="cursor:pointer;">
                            <label class="form-check-label text-danger fw-bold" for="removeVendorVideo" style="font-size:12px; cursor:pointer; margin-left:4px;">
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
                                    <span>This video plays with sound controls in the hotel hero gallery and full-screen view.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">
                            <i class="fa-solid fa-file-video text-danger me-1"></i> Upload Video Tour (MP4 / WebM)
                        </label>
                        <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                        <small class="text-muted" style="font-size:11px;">Max 100MB MP4 hotel walkthrough video. Overrides existing video.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size:12.5px;">
                            <i class="fa-brands fa-youtube text-danger me-1"></i> OR Video Tour URL (YouTube / Embed / MP4)
                        </label>
                        <input type="text" name="video_url" id="vendorVideoUrlInput" class="form-control"
                            value="{{ old('video_url', $property->video_url) }}"
                            placeholder="https://www.youtube.com/watch?v=..."
                            oninput="previewVendorVideoUrl(this.value)">
                        <small class="text-muted" style="font-size:11px;">Paste any YouTube link, Vimeo embed, or Cloudflare / direct MP4 URL.</small>
                    </div>
                    <div class="col-12" id="vendorVideoUrlPreviewWrap" style="display:none;">
                        <div style="padding:14px 16px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:4px;">
                            <span class="d-block text-muted mb-2" style="font-size:11.5px; font-weight:600;"><i class="fa-solid fa-eye me-1 text-primary"></i> Live New Video URL Preview:</span>
                            <div id="vendorVideoUrlPreviewFrame"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-align-left me-1"></i> Description
                </div>
                <textarea name="description" class="form-control" rows="5" required>{{ old('description', $property->description) }}</textarea>
            </div>

            {{-- Amenities --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-list-check me-1"></i> Amenities
                </div>
                <div class="row g-2">
                    @php $currentAmenities = old('amenities', $property->amenities ?? []); @endphp
                    @foreach([
                        ['wifi','fa-wifi','Free WiFi'],['pool','fa-person-swimming','Swimming Pool'],
                        ['parking','fa-car','Free Parking'],['ac','fa-snowflake','Air Conditioning'],
                        ['restaurant','fa-utensils','Restaurant'],['breakfast','fa-mug-hot','Breakfast'],
                        ['gym','fa-dumbbell','Fitness Center'],['spa','fa-spa','Spa & Wellness'],
                        ['bar','fa-wine-glass','Bar & Lounge'],['beachfront','fa-water','Beachfront'],
                        ['pet','fa-paw','Pet-Friendly'],['transfer','fa-van-shuttle','Airport Transfer'],
                        ['laundry','fa-shirt','Laundry'],['elevator','fa-elevator','Elevator'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <label style="padding:8px 10px; border:1px solid {{ in_array($am[0], $currentAmenities) ? '#1890ff' : '#e8e8e8' }}; border-radius:4px; display:flex; align-items:center; gap:8px; cursor:pointer; background:{{ in_array($am[0], $currentAmenities) ? '#e6f7ff' : '#fafafa' }}; transition:all .15s; width:100%;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}"
                                {{ in_array($am[0], $currentAmenities) ? 'checked' : '' }}
                                style="margin:0; width:15px; height:15px; flex-shrink:0; cursor:pointer; accent-color:#1890ff;">
                            <span style="font-size:12px; display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid {{ $am[1] }}" style="color:#1890ff; width:14px; flex-shrink:0;"></i>
                                <span>{{ $am[2] }}</span>
                            </span>
                        </label>
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
                        <input type="text" id="vendorEditCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi, Boat Safari..." style="font-size:12.5px; height:36px; border-radius:4px 0 0 4px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addVendorEditCustomAmenity();}">
                        <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12.5px; border-radius:0 4px 4px 0;" onclick="addVendorEditCustomAmenity()">
                            + Add
                        </button>
                    </div>
                    <div id="vendorEditCustomAmenitiesContainer" style="display:flex; flex-wrap:wrap; gap:8px;">
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

            {{-- Delete zone --}}
            <div class="form-card mb-3" style="border-color:#ffccc7; background:#fff8f8;">
                <div class="form-section-title" style="color:#ff4d4f; background:linear-gradient(135deg,#fff1f0,#fff8f8); border-color:#ffccc7;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Danger Zone — Delete Property
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:4px 0;">
                    <div>
                        <p style="margin:0; font-size:13px; font-weight:600; color:#cf1322;">Delete this listing permanently</p>
                        <p style="margin:4px 0 0; font-size:12px; color:#8c8c8c;">This will remove "{{ $property->name }}" and all associated data. Cannot be undone.</p>
                    </div>
                    <button type="submit" form="vendorDeletePropertyForm" class="btn btn-outline-danger fw-semibold" style="padding:8px 18px; font-size:13px; border-radius:6px;" onclick="return confirm('Are you sure? This cannot be undone!')">
                        <i class="fa-solid fa-trash me-1"></i> <span>Delete Permanently</span>
                    </button>
                </div>
            </div>

        </div>

        {{-- Enterprise Sticky Action Bar at Bottom --}}
        <div class="property-edit-sticky-footer">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-shield-halved text-success"></i>
                <span class="text-muted fw-semibold" style="font-size: 13px;">Vendor Portal Publisher &bull; Editing #PROP-{{ str_pad($property->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-light border px-4 fw-semibold" style="height: 38px; font-size: 13px; border-radius: 6px;">
                    <i class="fa-solid fa-xmark me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" style="height: 38px; font-size: 13.5px; border-radius: 6px;">
                    <i class="fa-solid fa-check me-1.5"></i> Save Changes
                </button>
            </div>
        </div>

    </form>

    <form id="vendorDeletePropertyForm" action="{{ route('vendor.properties.destroy', $property->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

</div>

@endsection

@section('scripts')
{{-- Leaflet Maps CDN & SortableJS CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<style>
/* Enterprise Property Edit Page & Unified Hotel Photo Manager */
.property-edit-sticky-footer {
    position: sticky;
    bottom: 0;
    z-index: 1020;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 12px 24px;
    margin: 32px -15px -15px -15px;
    box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.hotel-photo-box {
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.2s ease, opacity 0.2s ease;
}
.hotel-photo-box:hover {
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12) !important;
    transform: translateY(-2px);
}
.hotel-photo-box.is-cover-card {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 1.5px #3b82f6 !important;
}
.hotel-photo-box.photo-marked-delete {
    opacity: 0.45;
    border-color: #ef4444 !important;
    background: #fef2f2 !important;
}
.hotel-photo-add-tile:hover {
    background: #dbeafe !important;
    border-color: #1d4ed8 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.15) !important;
}
.hotel-sortable-ghost {
    opacity: 0.35 !important;
    background: #eff6ff !important;
    border: 2px dashed #3b82f6 !important;
}
.hotel-sortable-chosen {
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.25) !important;
    transform: scale(1.02);
}
.hotel-sortable-drag {
    opacity: 0.9 !important;
    cursor: grabbing !important;
}
</style>

<script>
// ── Geolocation Interactive Map Picker (Leaflet + OpenStreetMap) ──
let editPropertyMap = null;
let editPropertyMarker = null;

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
    const latInput = document.getElementById('vendorEditLatitudeInput');
    const lngInput = document.getElementById('vendorEditLongitudeInput');
    
    let defaultLat = parseFloat(latInput?.value) || 21.4272;
    let defaultLng = parseFloat(lngInput?.value) || 91.9702;

    const mapEl = document.getElementById('vendorEditMapPicker');
    if (mapEl) {
        editPropertyMap = L.map('vendorEditMapPicker').setView([defaultLat, defaultLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(editPropertyMap);

        editPropertyMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(editPropertyMap);

        editPropertyMarker.on('dragend', function (e) {
            const position = editPropertyMarker.getLatLng();
            if (latInput) latInput.value = position.lat.toFixed(6);
            if (lngInput) lngInput.value = position.lng.toFixed(6);
        });

        editPropertyMap.on('click', function (e) {
            editPropertyMarker.setLatLng(e.latlng);
            if (latInput) latInput.value = e.latlng.lat.toFixed(6);
            if (lngInput) lngInput.value = e.latlng.lng.toFixed(6);
        });

        setTimeout(function() {
            editPropertyMap.invalidateSize();
        }, 300);
    }

    initVendorHotelGallerySortable();
});

function updateEditMarkerFromInputs() {
    const lat = parseFloat(document.getElementById('vendorEditLatitudeInput')?.value);
    const lng = parseFloat(document.getElementById('vendorEditLongitudeInput')?.value);
    if (!isNaN(lat) && !isNaN(lng) && editPropertyMap && editPropertyMarker) {
        editPropertyMarker.setLatLng([lat, lng]);
        editPropertyMap.panTo([lat, lng]);
    }
}

function onVendorCityChanged(select) {
    const selected = select.options[select.selectedIndex];
    if (!selected) return;
    const lat = selected.getAttribute('data-lat');
    const lng = selected.getAttribute('data-lng');
    const cityName = selected.value;

    let targetLat = lat ? parseFloat(lat) : (cityCoordinates[cityName] ? cityCoordinates[cityName][0] : null);
    let targetLng = lng ? parseFloat(lng) : (cityCoordinates[cityName] ? cityCoordinates[cityName][1] : null);

    if (targetLat && targetLng) {
        const latInput = document.getElementById('vendorEditLatitudeInput');
        const lngInput = document.getElementById('vendorEditLongitudeInput');
        if (latInput) latInput.value = targetLat.toFixed(6);
        if (lngInput) lngInput.value = targetLng.toFixed(6);
        if (editPropertyMap && editPropertyMarker) {
            editPropertyMarker.setLatLng([targetLat, targetLng]);
            editPropertyMap.setView([targetLat, targetLng], 13);
        }
    }
}

// ── Unified Vendor Hotel Photo Manager & Drag-Drop Sorting ──
let vendorHotelSortableInstance = null;
let vendorHotelDataTransfer = new DataTransfer();

function initVendorHotelGallerySortable() {
    const grid = document.getElementById('vendorHotelGalleryGrid');
    if (!grid) return;

    if (typeof Sortable !== 'undefined') {
        vendorHotelSortableInstance = new Sortable(grid, {
            draggable: '.hotel-photo-card-item',
            filter: '.hotel-photo-add-tile-col, .hotel-photo-new-preview',
            preventOnFilter: false,
            animation: 200,
            ghostClass: 'hotel-sortable-ghost',
            chosenClass: 'hotel-sortable-chosen',
            dragClass: 'hotel-sortable-drag',
            onEnd: function() {
                updateVendorHotelGalleryOrder();
            }
        });
    } else {
        bindNativeVendorHotelDragEvents(grid);
    }
}

function updateVendorHotelGalleryOrder() {
    const grid = document.getElementById('vendorHotelGalleryGrid');
    if (!grid) return;

    const cards = grid.querySelectorAll('.hotel-photo-card-item');
    let hasExplicitCover = false;

    cards.forEach(card => {
        const radio = card.querySelector('.hotel-cover-radio');
        if (radio && radio.checked) hasExplicitCover = true;
    });

    cards.forEach((card, idx) => {
        card.setAttribute('data-photo-idx', idx);

        const badge = card.querySelector('.photo-order-badge');
        const radio = card.querySelector('.hotel-cover-radio');
        const box = card.querySelector('.hotel-photo-box');

        const isCover = (!hasExplicitCover && idx === 0) || (radio && radio.checked);

        if (badge) {
            if (isCover) {
                badge.className = 'badge photo-order-badge bg-primary';
                badge.innerHTML = '<i class="fa-solid fa-star text-warning me-0.5"></i> #1 (Cover)';
                if (box) box.classList.add('is-cover-card');
                if (radio && !hasExplicitCover && idx === 0) {
                    radio.checked = true;
                    const primInput = document.getElementById('vendorPrimaryImgUrl');
                    if (primInput && radio.value) primInput.value = radio.value;
                }
            } else {
                badge.className = 'badge photo-order-badge bg-dark bg-opacity-75';
                badge.textContent = '#' + (idx + 1);
                if (box && (!radio || !radio.checked)) box.classList.remove('is-cover-card');
            }
        }

        // Re-index form input names
        card.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.name && el.name.startsWith('existing_gallery[')) {
                el.name = el.name.replace(/existing_gallery\[\d+\]/, `existing_gallery[${idx}]`);
            }
            if (el.name && el.name.startsWith('existing_gallery_replace[')) {
                el.name = el.name.replace(/existing_gallery_replace\[\d+\]/, `existing_gallery_replace[${idx}]`);
            }
        });
    });
}

function onSelectVendorHotelCoverPhoto(radio, url) {
    const primInput = document.getElementById('vendorPrimaryImgUrl');
    if (primInput) primInput.value = url;

    document.querySelectorAll('.hotel-cover-radio').forEach(r => {
        if (r !== radio) r.checked = false;
    });

    updateVendorHotelGalleryOrder();
}

function handleHotelPhotoDeleteToggle(checkbox) {
    const card = checkbox.closest('.hotel-photo-card-item');
    if (!card) return;
    const box = card.querySelector('.hotel-photo-box');
    const overlay = card.querySelector('.photo-delete-overlay');

    if (checkbox.checked) {
        if (box) box.classList.add('photo-marked-delete');
        if (overlay) {
            overlay.classList.remove('d-none');
            overlay.classList.add('d-flex');
        }
    } else {
        if (box) box.classList.remove('photo-marked-delete');
        if (overlay) {
            overlay.classList.remove('d-flex');
            overlay.classList.add('d-none');
        }
    }
}

function bindNativeVendorHotelDragEvents(grid) {
    let draggedItem = null;

    grid.querySelectorAll('.hotel-photo-card-item').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('hotel-sortable-drag');
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('hotel-sortable-drag');
            draggedItem = null;
            updateVendorHotelGalleryOrder();
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!draggedItem || draggedItem === this) return;
            const bounding = this.getBoundingClientRect();
            const offset = e.clientX - bounding.left;
            if (offset > bounding.width / 2) {
                this.parentNode.insertBefore(draggedItem, this.nextSibling);
            } else {
                this.parentNode.insertBefore(draggedItem, this);
            }
        });
    });
}

function handleHotelPhotoTileDragOver(e, tile) {
    e.preventDefault();
    e.stopPropagation();
    tile.style.borderColor = '#1d4ed8';
    tile.style.background = '#dbeafe';
}

function handleHotelPhotoTileDragLeave(e, tile) {
    e.preventDefault();
    e.stopPropagation();
    tile.style.borderColor = '#3b82f6';
    tile.style.background = '#eff6ff';
}

function handleVendorHotelPhotoTileDrop(e, tile) {
    e.preventDefault();
    e.stopPropagation();
    tile.style.borderColor = '#3b82f6';
    tile.style.background = '#eff6ff';

    const files = e.dataTransfer.files;
    if (files && files.length > 0) {
        processVendorNewHotelFiles(files);
    }
}

function handleVendorEditGalleryFileSelect(input) {
    if (input.files && input.files.length > 0) {
        processVendorNewHotelFiles(input.files);
    }
}

function processVendorNewHotelFiles(fileList) {
    const grid = document.getElementById('vendorHotelGalleryGrid');
    const addTileCol = document.getElementById('vendorHotelPhotoAddTileCol');
    const fileInput = document.getElementById('vendorHotelGalleryFileInput');
    if (!grid || !addTileCol) return;

    Array.from(fileList).forEach(file => {
        if (!file.type.startsWith('image/')) return;

        vendorHotelDataTransfer.items.add(file);
        const fileIdx = vendorHotelDataTransfer.items.length - 1;

        const previewCol = document.createElement('div');
        previewCol.className = 'col-lg-3 col-md-4 col-sm-6 hotel-photo-new-preview';
        previewCol.setAttribute('data-file-idx', fileIdx);

        previewCol.innerHTML = `
            <div class="p-2 bg-white rounded border h-100 position-relative d-flex flex-column gap-1.5 hotel-photo-box" style="border-color:#10b981 !important; box-shadow:0 0 0 1px #10b981;">
                <div class="position-absolute top-0 start-0 m-2" style="z-index: 5;">
                    <span class="badge bg-success" style="font-size:10.5px; font-weight:700; padding:3px 7px;">
                        <i class="fa-solid fa-sparkles me-0.5"></i> New Photo
                    </span>
                </div>
                <div class="position-relative overflow-hidden rounded" style="height: 120px; background:#0f172a;">
                    <img src="" class="w-100 h-100 preview-img" style="object-fit: cover;" alt="New Photo">
                    <span class="position-absolute bottom-0 start-0 m-1.5 badge bg-dark bg-opacity-75" style="font-size:9.5px;">${(file.size / 1024).toFixed(0)} KB</span>
                </div>
                <div class="pt-0.5">
                    <label class="form-label mb-0 text-muted" style="font-size:10.5px; font-weight:600;">Category:</label>
                    <select name="gallery_image_categories[]" class="form-select form-select-sm fw-semibold" style="font-size:11px; padding:3px 7px;">
                        <option value="property_views" selected>Property views (Exterior/Lobby)</option>
                        <option value="facilities">Facilities (Pool/Gym/Spa/Hall)</option>
                        <option value="dining">Dining (Restaurant/Cafe/Buffet)</option>
                        <option value="rooms">Rooms &amp; Suites</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <input type="text" name="gallery_image_captions[]" value="${file.name.replace(/\.[^/.]+$/, "")}" placeholder="Photo caption / title" class="form-control form-control-sm" style="font-size:11px; padding:3px 7px;">
                </div>
                <div class="mt-auto pt-1 d-flex align-items-center justify-content-between border-top">
                    <span class="text-truncate text-muted small" style="max-width:120px; font-size:10px;" title="${file.name}">${file.name}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" style="font-size:11px;" onclick="removePendingVendorHotelPhoto(this, ${fileIdx})">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </button>
                </div>
            </div>
        `;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = previewCol.querySelector('.preview-img');
            if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(file);

        grid.insertBefore(previewCol, addTileCol);
    });

    if (fileInput) {
        fileInput.files = vendorHotelDataTransfer.files;
    }

    updateVendorTotalPhotoCounts();
}

function removePendingVendorHotelPhoto(btn, fileIdx) {
    const card = btn.closest('.hotel-photo-new-preview');
    if (card) card.remove();

    const newDt = new DataTransfer();
    Array.from(vendorHotelDataTransfer.files).forEach((f, idx) => {
        if (idx !== fileIdx) {
            newDt.items.add(f);
        }
    });
    vendorHotelDataTransfer = newDt;

    const fileInput = document.getElementById('vendorHotelGalleryFileInput');
    if (fileInput) fileInput.files = vendorHotelDataTransfer.files;

    updateVendorTotalPhotoCounts();
}

function updateVendorTotalPhotoCounts() {
    const grid = document.getElementById('vendorHotelGalleryGrid');
    if (!grid) return;
    const totalExisting = grid.querySelectorAll('.hotel-photo-card-item').length;
    const totalNew = grid.querySelectorAll('.hotel-photo-new-preview').length;
    const grandTotal = totalExisting + totalNew;

    const badge = document.getElementById('vendorGalleryCountBadge');
    const filterAll = document.getElementById('vendorFilterCountAll');
    if (badge) badge.textContent = grandTotal;
    if (filterAll) filterAll.textContent = grandTotal;
}

function filterVendorGalleryCards(cat, btn) {
    document.querySelectorAll('.vendor-gal-filter-btn').forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-primary');

    document.querySelectorAll('.hotel-photo-card-item').forEach(card => {
        if (cat === 'all' || card.getAttribute('data-card-cat') === cat) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function promptVendorEditCategory() {
    const select = document.getElementById('vendorEditPropTypeSelect');
    const custom = prompt("Enter new Property Category (e.g. Glamping Tent, Floating Cottage, Luxury Villa, Heritage Palace):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function promptVendorEditCity() {
    const select = document.getElementById('vendorEditPropCitySelect');
    const custom = prompt("Enter new Destination City or Region (e.g. Saint Martin Island, Kaptai Lake, Jaflong, Bangkok, Dubai):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function addVendorEditCustomAmenity() {
    const input = document.getElementById('vendorEditCustomAmenityInput');
    const val = input.value.trim();
    if (!val) return;
    const container = document.getElementById('vendorEditCustomAmenitiesContainer');
    const pill = document.createElement('span');
    pill.className = 'custom-amenity-pill';
    pill.style.cssText = 'display:inline-flex; align-items:center; gap:8px; padding:6px 12px; background:#ffffff; border:1px solid #cbd5e1; border-radius:4px; font-size:12px; font-weight:600; color:#1e293b; box-shadow:0 1px 2px rgba(0,0,0,0.04);';
    pill.innerHTML = `<i class="fa-solid fa-circle-check text-success" style="font-size:13px; flex-shrink:0; margin:0;"></i> <span>${val}</span> <input type="hidden" name="amenities[]" value="${val}"> <button type="button" class="btn-close" style="font-size:8px; margin-left:4px; cursor:pointer;" onclick="this.parentElement.remove()" title="Remove"></button>`;
    container.appendChild(pill);
    input.value = '';
}

function previewVendorVideoUrl(url) {
    const wrap = document.getElementById('vendorVideoUrlPreviewWrap');
    const frame = document.getElementById('vendorVideoUrlPreviewFrame');
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

document.querySelectorAll('input[name="amenities[]"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const label = this.closest('label');
        if (this.checked) {
            label.style.borderColor = '#1890ff';
            label.style.background  = '#e6f7ff';
        } else {
            label.style.borderColor = '#e8e8e8';
            label.style.background  = '#fafafa';
        }
    });
});
</script>
@endsection
