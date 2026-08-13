@extends('layouts.admin')
@section('title', 'Add New Property Listing — PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><strong style="color:#333;">Add New Listing</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:6px;">
        <div>
            <h1 class="page-title">Onboard New Property Listing</h1>
            <span style="font-size:12px; color:#8c8c8c;">Create and publish a new hotel, resort, ship, or eco-cottage to Prime Booking OTA</span>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-light btn-sm text-secondary border fw-bold px-3 py-1.5" style="font-size:12.5px; border-radius:6px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>
</div>

{{-- PAGE CONTENT AREA --}}
<div class="page-content-area">
    <div style="max-width:960px; margin:0 auto;">
        <form action="{{ route('admin.properties.store') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="admin-alert error mb-4 rounded-3">
                    <i class="fa-solid fa-circle-xmark me-2"></i>
                    <strong>Please review the following input errors:</strong>
                    <ul class="mb-0 mt-1 ps-3" style="font-size:12.5px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SECTION 1: Basic Specifications --}}
            <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border-radius:10px !important;">
                <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:15px;">
                        <span style="width:28px; height:28px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;">1</span>
                        Property Classification &amp; Basic Specs
                    </h6>
                    <span class="badge bg-light text-secondary border" style="font-size:11px;">Required Fields *</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Official Property Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-2" value="{{ old('name') }}"
                            placeholder="e.g. Royal Tulip Sea Pearl Beach Resort & Spa" required style="font-size:13px;">
                        <span class="text-muted" style="font-size:11px;">Enter the exact registered commercial name of the hotel or vessel.</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Property Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-2" required style="font-size:13px;">
                            <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>🏨 Hotel &amp; Commercial Suite</option>
                            <option value="resort" {{ old('type') == 'resort' ? 'selected' : '' }}>🏖️ Beach Resort &amp; Spa</option>
                            <option value="houseboat" {{ old('type') == 'houseboat' ? 'selected' : '' }}>🚢 Cruise Ship &amp; Houseboat</option>
                            <option value="homestay" {{ old('type') == 'homestay' ? 'selected' : '' }}>🪵 Eco Cottage &amp; Homestay</option>
                            <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>🏢 Serviced Apartment</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">City / Region Destination <span class="text-danger">*</span></label>
                        <select name="city" class="form-select rounded-2" required style="font-size:13px;">
                            <option value="Cox's Bazar Sea Beach" {{ old('city') == "Cox's Bazar Sea Beach" ? 'selected' : '' }}>Cox's Bazar Sea Beach</option>
                            <option value="Dhaka City" {{ old('city') == 'Dhaka City' ? 'selected' : '' }}>Dhaka City &amp; Metropolitan</option>
                            <option value="Sylhet & Sreemangal" {{ old('city') == 'Sylhet & Sreemangal' ? 'selected' : '' }}>Sylhet &amp; Sreemangal Tea Gardens</option>
                            <option value="Sajek Valley & Rangamati" {{ old('city') == 'Sajek Valley & Rangamati' ? 'selected' : '' }}>Sajek Valley &amp; Rangamati Hill Tracts</option>
                            <option value="Sundarbans & Mongla" {{ old('city') == 'Sundarbans & Mongla' ? 'selected' : '' }}>Sundarbans National Forest &amp; Mongla</option>
                            <option value="Kuakata Sunset Beach" {{ old('city') == 'Kuakata Sunset Beach' ? 'selected' : '' }}>Kuakata Sunset Sea Beach</option>
                            <option value="Chittagong City" {{ old('city') == 'Chittagong City' ? 'selected' : '' }}>Chittagong Commercial Port City</option>
                            <option value="Bandarban Hill District" {{ old('city') == 'Bandarban Hill District' ? 'selected' : '' }}>Bandarban Hill District</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Star Classification <span class="text-danger">*</span></label>
                        <select name="star_rating" class="form-select rounded-2" required style="font-size:13px;">
                            <option value="5" {{ old('star_rating') == '5' ? 'selected' : '' }}>★★★★★ — 5 Star Luxury Rating</option>
                            <option value="4" {{ old('star_rating') == '4' ? 'selected' : '' }}>★★★★ — 4 Star Premium Rating</option>
                            <option value="3" {{ old('star_rating') == '3' ? 'selected' : '' }}>★★★ — 3 Star Standard Rating</option>
                            <option value="2" {{ old('star_rating') == '2' ? 'selected' : '' }}>★★ — 2 Star Economy Rating</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">Vendor Account</label>
                        <select name="vendor_id" class="form-select rounded-2" style="font-size:13px;">
                            <option value="">Admin Managed (Prime Booking)</option>
                            @if(isset($vendors))
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->email }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-dark mb-1">Full Physical Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control rounded-2" value="{{ old('address') }}"
                            placeholder="e.g. Inani Beach, Marine Drive Road, Kolatoli, Cox's Bazar 4700, Bangladesh" required style="font-size:13px;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Nearest Landmark / Distance</label>
                        <input type="text" name="nearest_landmark" class="form-control rounded-2" value="{{ old('nearest_landmark') }}"
                            placeholder="e.g. 2 mins walk from Kolatoli Beach Point" style="font-size:13px;">
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Pricing & Rates --}}
            <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border-radius:10px !important;">
                <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:15px;">
                        <span style="width:28px; height:28px; border-radius:50%; background:#f6ffed; color:#28c76f; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;">2</span>
                        Financial Rates &amp; Guest Booking Options
                    </h6>
                    <span class="text-muted" style="font-size:11.5px;">Currency: BDT (৳)</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Base Price Per Night (BDT ৳) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-dark fw-bold" style="font-size:12px;">৳ BDT</span>
                            <input type="number" name="price_per_night" class="form-control ps-2"
                                value="{{ old('price_per_night') }}" placeholder="8500" required style="font-size:13px;">
                        </div>
                        <span class="text-muted" style="font-size:11px;">Starting rack rate per night per room.</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Crossed-Out Original Rate (Optional)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted" style="font-size:12px;">৳ BDT</span>
                            <input type="number" name="original_price" class="form-control ps-2"
                                value="{{ old('original_price') }}" placeholder="11000" style="font-size:13px;">
                        </div>
                        <span class="text-muted" style="font-size:11px;">Displays crossed-out rate (e.g. <s>BDT 11,000</s>).</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Listing Visibility Status</label>
                        <select name="status" class="form-select rounded-2" style="font-size:13px;">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active — Published &amp; Searchable Live</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive — Draft Mode / Hidden</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="form-check form-switch mb-0 me-3">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                    {{ old('is_featured') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-dark ms-1" for="isFeatured" style="font-size:12.5px; cursor:pointer;">
                                    <i class="fa-solid fa-star text-warning me-1"></i> Featured Property
                                </label>
                            </div>
                            <div class="form-check form-switch mb-0 me-3">
                                <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="freeCancel"
                                    {{ old('free_cancellation', '1') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-success ms-1" for="freeCancel" style="font-size:12.5px; cursor:pointer;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Free Cancellation Allowed
                                </label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" id="noCC"
                                    {{ old('no_credit_card_required', '1') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-primary ms-1" for="noCC" style="font-size:12.5px; cursor:pointer;">
                                    <i class="fa-solid fa-credit-card me-1"></i> Pay at Hotel / Cash on Arrival
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Media Assets --}}
            <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border-radius:10px !important;">
                <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:15px;">
                        <span style="width:28px; height:28px; border-radius:50%; background:#f0eefc; color:#7367f0; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;">3</span>
                        Media Assets &amp; Video Property Tour
                    </h6>
                    <span class="text-muted" style="font-size:11.5px;">CDN &amp; Direct Image Links</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Primary Thumbnail Image URL <span class="text-danger">*</span></label>
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control rounded-2"
                            value="{{ old('primary_image') }}" placeholder="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80"
                            oninput="previewImage(this.value)" style="font-size:13px;">
                        <div id="imgPreviewWrap" class="mt-2" style="display:none;">
                            <img id="imgPreview" src="" style="height:90px; border-radius:8px; border:1px solid #e2e8f0; object-fit:cover;" alt="Preview">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Official Video Property Tour URL (YouTube Embed / MP4 Link)</label>
                        <input type="url" name="video_url" class="form-control rounded-2"
                            value="{{ old('video_url') }}" placeholder="e.g. https://www.youtube.com/embed/dQw4w9WgXcQ" style="font-size:13px;">
                        <span class="text-muted" style="font-size:11px;">Enables the 360 Video Tour button on search results and hotel detail pages.</span>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Property Gallery Image URLs (One URL per line, max 10)</label>
                        <textarea name="gallery_images" class="form-control rounded-2" rows="3"
                            placeholder="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1000&q=80&#10;https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80" style="font-size:13px;">{{ old('gallery_images') }}</textarea>
                        <span class="text-muted" style="font-size:11px;">Paste image links line by line. These will render in the photo lightbox carousel.</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Description & Guest Guidelines --}}
            <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border-radius:10px !important;">
                <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:15px;">
                        <span style="width:28px; height:28px; border-radius:50%; background:#fff7e6; color:#ff9f43; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;">4</span>
                        Property Overview &amp; Guest Guidelines
                    </h6>
                    <span class="badge bg-light text-secondary border" style="font-size:11px;">SEO Content</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Comprehensive Description &amp; Policies <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control rounded-2" rows="5"
                            placeholder="Detail location advantages, check-in policies, room features, complimentary breakfast details, sea view highlights, and nearby landmarks..." required style="font-size:13px;">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 5: Amenities & Facilities --}}
            <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border-radius:10px !important;">
                <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:15px;">
                        <span style="width:28px; height:28px; border-radius:50%; background:#e6f7ff; color:#1890ff; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;">5</span>
                        Property Amenities &amp; On-Site Facilities
                    </h6>
                    <span class="text-muted" style="font-size:11.5px;">Select all active amenities</span>
                </div>
                <div class="row g-2">
                    @foreach([
                        ['wifi',       'fa-wifi',              'Free High-Speed WiFi'],
                        ['pool',       'fa-person-swimming',   'Swimming Pool'],
                        ['parking',    'fa-car',               'Free Parking'],
                        ['ac',         'fa-snowflake',         'Air Conditioning'],
                        ['restaurant', 'fa-utensils',          'On-site Restaurant'],
                        ['breakfast',  'fa-mug-hot',           'Complimentary Breakfast'],
                        ['gym',        'fa-dumbbell',          'Fitness Center / Gym'],
                        ['spa',        'fa-spa',               'Spa &amp; Wellness Center'],
                        ['bar',        'fa-wine-glass',        'Rooftop Bar &amp; Lounge'],
                        ['beachfront', 'fa-water',             'Beachfront / Ocean View'],
                        ['pet',        'fa-paw',               'Pet-Friendly Policy'],
                        ['transfer',   'fa-van-shuttle',       'Airport Transfer Service'],
                        ['laundry',    'fa-shirt',             'Express Laundry Service'],
                        ['elevator',   'fa-elevator',          'Elevator / Lift Access'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2" style="cursor:pointer; font-size:12.5px;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" id="am_{{ $am[0] }}"
                                {{ in_array($am[0], old('amenities', [])) ? 'checked' : '' }} style="cursor:pointer;">
                            <label for="am_{{ $am[0] }}" class="mb-0 text-dark fw-semibold d-flex align-items-center gap-1.5" style="cursor:pointer; font-size:12px;">
                                <i class="fa-solid {{ $am[1] }} text-primary"></i> {!! $am[2] !!}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SECTION 6: Room Types Setup --}}
            <div class="card border border-gray-200 rounded-3 p-4 mb-4 bg-white shadow-xs" style="border-radius:10px !important;">
                <div class="border-bottom pb-3 mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:15px;">
                        <span style="width:28px; height:28px; border-radius:50%; background:#f6ffed; color:#28c76f; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800;">6</span>
                        Initial Inventory &amp; Room Category Setup
                    </h6>
                    <span class="text-muted" style="font-size:11.5px;">Auto-creates room types</span>
                </div>
                <div class="row g-3">
                    @foreach([
                        ['Standard Deluxe Room', 8500, 10, 2],
                        ['Executive Sea View Suite', 14500, 5, 2],
                        ['Presidential Family Suite', 24500, 2, 4],
                    ] as $r)
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size:13px;">{{ $r[0] }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1" style="font-size:11px;">Rate/Night (BDT)</label>
                                    <input type="number" name="room_price[]" class="form-control form-control-sm" placeholder="{{ $r[1] }}" style="font-size:12px;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1" style="font-size:11px;">Available Qty</label>
                                    <input type="number" name="room_qty[]" class="form-control form-control-sm" placeholder="{{ $r[2] }}" style="font-size:12px;">
                                </div>
                                <input type="hidden" name="room_type[]" value="{{ $r[0] }}">
                                <input type="hidden" name="room_beds[]" value="{{ $r[3] }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <span class="text-muted mt-2 d-block" style="font-size:11px;">Note: You can add unlimited custom room categories anytime from the <strong>Manage Rooms</strong> panel.</span>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="d-flex align-items-center justify-content-end gap-2 py-3">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-light text-secondary border fw-bold px-4 py-2" style="border-radius:6px; font-size:13.5px;">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary fw-bold px-4 py-2" style="border-radius:6px; font-size:13.5px;">
                    Save as Draft <i class="fa-solid fa-floppy-disk ms-1"></i>
                </button>
                <button type="submit" name="action" value="publish" class="btn btn-primary text-white fw-bold px-4 py-2" style="background-color:#2067e1; border-radius:6px; font-size:13.5px;">
                    Publish Listing Live <i class="fa-solid fa-rocket ms-1"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
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
