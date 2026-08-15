@extends('layouts.vendor')
@section('title', 'Add New Property Listing — Vendor Partner Portal')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card mb-3">
    <div class="page-breadcrumb mb-1">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('vendor.properties.index') }}">My Properties</a>
        <span class="sep">-</span><strong style="color:#333;">Add New Property</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h1 class="page-title m-0">List a New Property / Hotel / Ship</h1>
        <a href="{{ route('vendor.properties.index') }}" class="btn-export-csv" style="border-color:#cbd5e1; color:#475569; font-weight:600; text-decoration:none;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Properties
        </a>
    </div>
</div>

{{-- CONTENT AREA --}}
<div class="page-content-area">
    <div style="max-width:980px; margin:0 auto;">

        @if(session('success'))
            <div class="admin-alert success mb-3">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="admin-alert error mb-3">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <strong>Please review the input errors below:</strong>
                <span class="ms-2">{{ implode(', ', $errors->all()) }}</span>
            </div>
        @endif



        <form action="{{ route('vendor.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- SINGLE MASTER CARD CONTAINER (Enterprise Best Practice) --}}
            <div class="form-card" style="border-radius:6px; background:#ffffff; border:1px solid #e2e8f0; padding:28px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">

                {{-- SECTION 1: BASIC INFO --}}
                <div class="border-bottom pb-2.5 mb-3">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:14.5px;">
                        <i class="fa-solid fa-hotel text-primary me-2" style="font-size:15px; width:18px;"></i>
                        <span>1. Property Details &amp; Geographic Location</span>
                    </h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Property Full Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}"
                            placeholder="e.g. Royal Ocean Resort &amp; Spa" required style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark m-0" style="font-size:12.5px;">Property Type <span style="color:#ff4d4f;">*</span></label>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptVendorCreateCategory()">
                                + Add Custom Type
                            </button>
                        </div>
                        <select name="type" id="vendorCreatePropTypeSelect" class="form-select form-select-sm" required style="font-size:13px; height:38px;">
                            <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>Hotel &amp; Resort</option>
                            <option value="resort" {{ old('type') == 'resort' ? 'selected' : '' }}>Beach Resort &amp; Spa</option>
                            <option value="houseboat" {{ old('type') == 'houseboat' ? 'selected' : '' }}>Ship &amp; Houseboat</option>
                            <option value="homestay" {{ old('type') == 'homestay' ? 'selected' : '' }}>Eco Cottage &amp; Homestay</option>
                            <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Serviced Apartment / Suite</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark m-0" style="font-size:12.5px;">City / Destination <span style="color:#ff4d4f;">*</span></label>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" style="font-size:11px;" onclick="promptVendorCreateCity()">
                                + Add New City/Area
                            </button>
                        </div>
                        <select name="city" id="vendorCreatePropCitySelect" class="form-select form-select-sm" required style="font-size:13px; height:38px;" onchange="onVendorCityChanged(this)">
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
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Star Rating <span style="color:#ff4d4f;">*</span></label>
                        <select name="star_rating" class="form-select form-select-sm" required style="font-size:13px; height:38px;">
                            <option value="5" {{ old('star_rating') == '5' ? 'selected' : '' }}>5 Stars — Luxury Property</option>
                            <option value="4" {{ old('star_rating') == '4' ? 'selected' : '' }}>4 Stars — Premium Property</option>
                            <option value="3" {{ old('star_rating') == '3' ? 'selected' : '' }}>3 Stars — Standard Property</option>
                            <option value="2" {{ old('star_rating') == '2' ? 'selected' : '' }}>2 Stars — Economy Property</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Full Property Physical Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address') }}"
                            placeholder="e.g. Plot 14, Main Marine Drive, Kalatoli, Cox's Bazar" required style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Nearest Landmark / Area Highlight</label>
                        <input type="text" name="nearest_landmark" class="form-control form-control-sm" value="{{ old('nearest_landmark') }}"
                            placeholder="e.g. Kolatoli Beach Point (150m)" style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Real-Time GPS Coordinates (Lat, Long)</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="latitude" class="form-control form-control-sm" value="{{ old('latitude') }}" placeholder="Lat: 21.4272" style="font-size:13px; height:38px;">
                            <input type="text" name="longitude" class="form-control form-control-sm" value="{{ old('longitude') }}" placeholder="Long: 91.9702" style="font-size:13px; height:38px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Google Maps Embed / Location Link</label>
                        <input type="url" name="map_embed_url" class="form-control form-control-sm" value="{{ old('map_embed_url') }}"
                            placeholder="https://maps.google.com/..." style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Check-in / Check-out Hours</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="checkin_time" class="form-control form-control-sm" value="{{ old('checkin_time', '14:00') }}" placeholder="Checkin: 14:00" style="font-size:13px; height:38px;">
                            <input type="text" name="checkout_time" class="form-control form-control-sm" value="{{ old('checkout_time', '12:00') }}" placeholder="Checkout: 12:00" style="font-size:13px; height:38px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Hotel Contact Phone &amp; Email</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" name="contact_phone" class="form-control form-control-sm" value="{{ old('contact_phone') }}" placeholder="+8801700000000" style="font-size:13px; height:38px;">
                            <input type="email" name="contact_email" class="form-control form-control-sm" value="{{ old('contact_email') }}" placeholder="info@hotel.com" style="font-size:13px; height:38px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Building Floors / Levels</label>
                        <input type="number" name="total_floors" class="form-control form-control-sm" value="{{ old('total_floors') }}" placeholder="e.g. 12 Floors" min="1" style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Total Hotel Rooms</label>
                        <input type="number" name="total_rooms_count" class="form-control form-control-sm" value="{{ old('total_rooms_count') }}" placeholder="e.g. 150 Rooms" min="1" style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Year Built / Renovated</label>
                        <input type="number" name="year_built" class="form-control form-control-sm" value="{{ old('year_built') }}" placeholder="e.g. 2023" min="1950" max="2030" style="font-size:13px; height:38px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Front Desk Languages Spoken</label>
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
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Pets Policy</label>
                        <select name="pets_policy" class="form-select form-select-sm" style="font-size:13px; height:38px;">
                            <option value="Pets Not Allowed" selected>Pets Not Allowed</option>
                            <option value="Pets Allowed">Pets Allowed (Free / Fee)</option>
                            <option value="Pets Allowed on Request">Pets Allowed on Request</option>
                        </select>
                    </div>
                </div>

                {{-- SECTION 2: PRICING --}}
                <div class="border-bottom pb-2.5 mb-3 mt-4">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:14.5px;">
                        <i class="fa-solid fa-bangladeshi-taka-sign text-success me-2" style="font-size:15px; width:18px;"></i>
                        <span>2. Nightly Base Pricing &amp; MRP Discount</span>
                    </h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Standard Base Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                            <input type="number" name="price_per_night" class="form-control" value="{{ old('price_per_night') }}" placeholder="e.g. 8500" required style="font-size:13px; height:38px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Original / Regular MRP Price (BDT ৳) — Optional</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text fw-bold bg-light">৳ BDT</span>
                            <input type="number" name="original_price" class="form-control" value="{{ old('original_price') }}" placeholder="e.g. 12000 (Generates discount badge)" style="font-size:13px; height:38px;">
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: MEDIA ASSETS --}}
                <div class="border-bottom pb-2.5 mb-3 mt-4">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:14.5px;">
                        <i class="fa-solid fa-images text-purple me-2" style="font-size:15px; width:18px; color:#7367f0;"></i>
                        <span>3. High-Res Photos &amp; Video Tour</span>
                    </h5>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">
                            <i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Upload Thumbnail Photo (Device)
                        </label>
                        <input type="file" name="primary_image_file" class="form-control form-control-sm" accept="image/*" onchange="previewFile(this)" style="font-size:12.5px; height:38px; padding:4px 12px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">
                            <i class="fa-solid fa-link text-primary me-1"></i> OR Paste External Thumbnail Image URL
                        </label>
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control form-control-sm"
                            value="{{ old('primary_image') }}" placeholder="https://images.unsplash.com/photo-..."
                            oninput="previewUrl(this.value)" style="font-size:12.5px; height:38px;">
                    </div>
                    <div class="col-12">
                        <div id="imgPreviewWrap" class="p-2 border rounded bg-light" style="display:none; max-width:260px;">
                            <span class="text-secondary d-block mb-1" style="font-size:11px; font-weight:700;">LIVE PHOTO PREVIEW:</span>
                            <img id="imgPreview" src="" style="width:100%; height:130px; object-fit:cover; border-radius:4px;" alt="Thumbnail Preview">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Upload Multiple Gallery Photos (Device)</label>
                        <input type="file" name="gallery_image_files[]" class="form-control form-control-sm" multiple accept="image/*" style="font-size:12.5px; height:38px; padding:4px 12px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">YouTube Video Tour URL (Optional)</label>
                        <input type="url" name="video_url" class="form-control form-control-sm" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=..." style="font-size:12.5px; height:38px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark mb-1" style="font-size:12.5px;">Additional Gallery URLs (One URL per line — Optional)</label>
                        <textarea name="gallery_images" class="form-control form-control-sm" rows="2"
                            placeholder="https://your-cdn.com/room1.jpg&#10;https://your-cdn.com/room2.jpg" style="font-size:12.5px;">{{ old('gallery_images') }}</textarea>
                    </div>
                </div>

                {{-- SECTION 4: OVERVIEW & DESCRIPTION --}}
                <div class="border-bottom pb-2.5 mb-3 mt-4">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:14.5px;">
                        <i class="fa-solid fa-align-left text-primary me-2" style="font-size:15px; width:18px;"></i>
                        <span>4. Property Overview &amp; Description</span>
                    </h5>
                </div>
                <div class="mb-4">
                    <textarea name="description" class="form-control" rows="4" required style="font-size:13px;"
                        placeholder="Describe your property — luxury features, sea view balconies, complimentary breakfast, nearby tourist spots, check-in/out policies...">{{ old('description') }}</textarea>
                </div>

                {{-- SECTION 5: AMENITIES & FACILITIES --}}
                <div class="border-bottom pb-2.5 mb-3 mt-4">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size:14.5px;">
                        <i class="fa-solid fa-list-check text-info me-2" style="font-size:15px; width:18px;"></i>
                        <span>5. Included Amenities &amp; Guest Services</span>
                    </h5>
                </div>
                <div class="row g-2.5 mb-4">
                    @foreach([
                        ['wifi','fa-wifi','Free High-Speed Wi-Fi'],
                        ['pool','fa-person-swimming','Swimming Pool & Splash Zone'],
                        ['parking','fa-car','Free Secure Parking'],
                        ['ac','fa-snowflake','Central Air Conditioning'],
                        ['restaurant','fa-utensils','Multi-Cuisine Restaurant'],
                        ['breakfast','fa-mug-hot','Free Buffet Breakfast'],
                        ['gym','fa-dumbbell','24/7 Fitness Center'],
                        ['spa','fa-spa','Wellness Spa & Sauna'],
                        ['bar','fa-wine-glass','Rooftop Bar & Lounge'],
                        ['beachfront','fa-water','Private Beachfront / Sea View'],
                        ['pet','fa-paw','Pet-Friendly Policy'],
                        ['transfer','fa-van-shuttle','Airport Shuttle Service'],
                        ['laundry','fa-shirt','Express Laundry Service'],
                        ['elevator','fa-elevator','Elevator / Lift Access'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <label style="padding:10px 12px; border:1px solid #e2e8f0; border-radius:4px; display:flex; align-items:center; gap:10px; cursor:pointer; background:#f8fafc; transition:all 0.15s; width:100%;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" class="form-check-input mt-0"
                                {{ in_array($am[0], old('amenities', [])) ? 'checked' : '' }}>
                            <span style="font-size:12px; font-weight:600; color:#334155; display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid {{ $am[1] }}" style="color:#2067e1; width:16px;"></i> {{ $am[2] }}
                            </span>
                        </label>
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
                        <input type="text" id="vendorCreateCustomAmenityInput" class="form-control" placeholder="e.g. Heli-pad, EV Charger, Private Jacuzzi, Boat Safari..." style="font-size:12.5px; height:36px; border-radius:4px 0 0 4px;" onkeydown="if(event.key==='Enter'){event.preventDefault();addVendorCreateCustomAmenity();}">
                        <button type="button" class="btn btn-primary fw-bold" style="background:#2067e1; font-size:12.5px; border-radius:0 4px 4px 0;" onclick="addVendorCreateCustomAmenity()">
                            + Add
                        </button>
                    </div>
                    <div id="vendorCreateCustomAmenitiesContainer" class="d-flex flex-wrap gap-2"></div>
                </div>

                {{-- SUBMIT FOOTER --}}
                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('vendor.properties.index') }}" class="btn btn-light border text-secondary fw-bold px-4 py-2" style="font-size:13px; border-radius:4px;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary fw-bold px-5 py-2" style="background-color: #2067e1; font-size:13px; border-radius:4px; border:none;">
                        Submit Property Listing <i class="fa-solid fa-paper-plane ms-1"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function onVendorCityChanged(select) {
    const selected = select.options[select.selectedIndex];
    if (!selected) return;
    const lat = selected.getAttribute('data-lat');
    const lng = selected.getAttribute('data-lng');
    const latInput = document.querySelector('input[name="latitude"]');
    const lngInput = document.querySelector('input[name="longitude"]');
    if (lat && latInput && (!latInput.value || latInput.value === '')) {
        latInput.value = parseFloat(lat).toFixed(4);
    }
    if (lng && lngInput && (!lngInput.value || lngInput.value === '')) {
        lngInput.value = parseFloat(lng).toFixed(4);
    }
}

function promptVendorCreateCategory() {
    const select = document.getElementById('vendorCreatePropTypeSelect');
    const custom = prompt("Enter new Property Category (e.g. Glamping Tent, Floating Resort, Luxury Villa, Heritage Palace):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function promptVendorCreateCity() {
    const select = document.getElementById('vendorCreatePropCitySelect');
    const custom = prompt("Enter new Destination City or Region (e.g. Saint Martin Island, Kaptai Lake, Jaflong, Bangkok, Dubai):");
    if (custom && custom.trim() !== "") {
        const opt = document.createElement('option');
        opt.value = custom.trim();
        opt.textContent = custom.trim();
        opt.selected = true;
        select.appendChild(opt);
    }
}

function addVendorCreateCustomAmenity() {
    const input = document.getElementById('vendorCreateCustomAmenityInput');
    const val = input.value.trim();
    if (!val) return;
    const container = document.getElementById('vendorCreateCustomAmenitiesContainer');
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
document.querySelectorAll('input[name="amenities[]"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const label = this.closest('label');
        if (this.checked) {
            label.style.borderColor = '#2067e1';
            label.style.background  = '#f0f7ff';
        } else {
            label.style.borderColor = '#e2e8f0';
            label.style.background  = '#f8fafc';
        }
    });
});
</script>
@endsection
