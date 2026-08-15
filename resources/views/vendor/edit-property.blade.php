@extends('layouts.vendor')
@section('title', 'Edit: ' . $property->name . ' | Vendor Portal')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><strong style="color:#333;">Edit Listing</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title" style="font-size:16px;">Editing: {{ $property->name }}</h1>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('hotels.show', $property->id) }}" target="_blank" class="btn-table-action primary" style="padding:6px 14px;">
                View Live <i class="fa-solid fa-external-link ms-1"></i>
            </a>
            <a href="{{ route('vendor.dashboard') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:6px 14px;">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="page-content-area">
    <div style="max-width:900px; margin:0 auto;">

        @if(session('success'))
            <div class="admin-alert success mb-3">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="admin-alert error mb-3">
                <i class="fa-solid fa-circle-xmark me-1"></i> {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <form action="{{ route('vendor.properties.update', $property->id) }}" method="POST">
            @csrf
            @method('PUT')

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
                                <option value="{{ $property->type }}" selected>✨ {{ ucfirst($property->type) }}</option>
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
                        <select name="city" id="vendorEditPropCitySelect" class="form-select">
                            @foreach(["Cox's Bazar Sea Beach","Dhaka City","Sylhet & Sreemangal","Sajek Valley & Rangamati","Sundarbans & Mongla","Kuakata Sunset Beach","Chittagong City","Bandarban Hill District"] as $c)
                                <option value="{{ $c }}" {{ old('city', $property->city) == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                            @if(!empty($property->city) && !in_array($property->city, ["Cox's Bazar Sea Beach","Dhaka City","Sylhet & Sreemangal","Sajek Valley & Rangamati","Sundarbans & Mongla","Kuakata Sunset Beach","Chittagong City","Bandarban Hill District"]))
                                <option value="{{ $property->city }}" selected>📍 {{ $property->city }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Star Rating</label>
                        <select name="star_rating" class="form-select">
                            <option value="5" {{ old('star_rating', $property->star_rating) == 5 ? 'selected' : '' }}>★★★★★ 5 Star Luxury</option>
                            <option value="4" {{ old('star_rating', $property->star_rating) == 4 ? 'selected' : '' }}>★★★★ 4 Star Premium</option>
                            <option value="3" {{ old('star_rating', $property->star_rating) == 3 ? 'selected' : '' }}>★★★ 3 Star Standard</option>
                            <option value="2" {{ old('star_rating', $property->star_rating) == 2 ? 'selected' : '' }}>★★ 2 Star Budget</option>
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
                    <div class="col-md-6">
                        <label class="form-label">Real-Time GPS Coordinates (Lat, Long)</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $property->latitude) }}" placeholder="Lat: 21.4272">
                            <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $property->longitude) }}" placeholder="Long: 91.9702">
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

            {{-- Images --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-images me-1"></i> Images
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Primary Image URL</label>
                        @if($property->primary_image)
                            <div style="margin-bottom:8px; display:flex; align-items:center; gap:10px;">
                                <img src="{{ $property->primary_image }}" style="height:60px; border-radius:6px; border:1px solid #e8e8e8; object-fit:cover;">
                                <span style="font-size:11px; color:#8c8c8c;">Current primary image</span>
                            </div>
                        @endif
                        <input type="url" name="primary_image" class="form-control"
                            value="{{ old('primary_image', $property->primary_image) }}"
                            placeholder="https://...">
                    </div>
                    <div class="col-12">
                        <label class="form-label"><i class="fa-solid fa-video text-danger me-1"></i> Hotel Video Tour URL (YouTube / Embed / MP4)</label>
                        <input type="url" name="video_url" class="form-control"
                            value="{{ old('video_url', $property->video_url) }}"
                            placeholder="https://www.youtube.com/embed/...">
                        <small style="font-size:11px; color:#8c8c8c;">Attach a video tour link to showcase your property to prospective guests.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gallery Images (one URL per line)</label>
                        <textarea name="gallery_images" class="form-control" rows="3">{{ old('gallery_images', $galleryText) }}</textarea>
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
                        <label style="padding:8px 10px; border:1px solid {{ in_array($am[0], $currentAmenities) ? '#1890ff' : '#e8e8e8' }}; border-radius:6px; display:flex; align-items:center; gap:8px; cursor:pointer; background:{{ in_array($am[0], $currentAmenities) ? '#e6f7ff' : '#fafafa' }}; transition:all .15s; width:100%;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}"
                                {{ in_array($am[0], $currentAmenities) ? 'checked' : '' }}>
                            <span style="font-size:12px; display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid {{ $am[1] }}" style="color:#1890ff; width:14px;"></i> {{ $am[2] }}
                            </span>
                        </label>
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
                        <input type="text" id="vendorEditCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi, Boat Safari..." style="font-size:12.5px; height:36px; border-radius:4px 0 0 4px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addVendorEditCustomAmenity();}">
                        <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12.5px; border-radius:0 4px 4px 0;" onclick="addVendorEditCustomAmenity()">
                            + Add
                        </button>
                    </div>
                    <div id="vendorEditCustomAmenitiesContainer" class="d-flex flex-wrap gap-2">
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
                    <form action="{{ route('vendor.properties.destroy', $property->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure? This cannot be undone!')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-table-action danger" style="padding:8px 18px; font-size:13px;">
                            Delete Permanently <i class="fa-solid fa-trash ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; justify-content:flex-end; gap:10px; padding:12px 0; flex-wrap:wrap;">
                <a href="{{ route('vendor.dashboard') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:8px 20px;">Cancel</a>
                <button type="submit" class="btn-add-primary" style="padding:8px 28px;">
                    Save Changes <i class="fa-solid fa-check ms-1"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function promptVendorEditCategory() {
    const select = document.getElementById('vendorEditPropTypeSelect');
    const custom = prompt("Enter new Property Category (e.g. Glamping Tent, Floating Cottage, Luxury Villa, Heritage Palace):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = "✨ " + custom.trim();
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
        opt.textContent = "📍 " + custom.trim();
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
    pill.className = 'badge bg-white text-dark border d-inline-flex align-items-center gap-1.5 p-2 shadow-xs';
    pill.style.fontSize = '12px';
    pill.innerHTML = `<i class="fa-solid fa-circle-check text-success"></i> ${val} <input type="hidden" name="amenities[]" value="${val}"> <button type="button" class="btn-close ms-1" style="font-size:8px;" onclick="this.parentElement.remove()" title="Remove"></button>`;
    container.appendChild(pill);
    input.value = '';
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
