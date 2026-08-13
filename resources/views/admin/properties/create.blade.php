@extends('layouts.admin')
@section('title', 'Add New Property Listing | PRIME BOOKING Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><strong style="color:#333;">Add New Listing</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:6px;">
        <h1 class="page-title m-0">Add New Property Listing</h1>
        <a href="{{ route('admin.properties.index') }}" class="btn-table-action" style="font-size:13px; height:36px; padding:0 16px; border-radius:4px; display:inline-flex; align-items:center;">
            <i class="fa-solid fa-arrow-left me-1.5"></i> Back to Inventory
        </a>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">
    <form action="{{ route('admin.properties.store') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <strong>Please fix the errors below:</strong> {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        {{-- MAIN FORM CONTAINER CARD --}}
        <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:24px;">

            {{-- PANEL 1: Basic Specifications --}}
            <div class="p-3 mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                        <i class="fa-solid fa-hotel text-primary"></i> Step 1 — Basic Info &amp; Category
                    </h6>
                    <span class="badge bg-light text-secondary border" style="font-size:11px; border-radius:4px; padding:4px 8px;">Required Fields *</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Property / Hotel / Ship Full Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="e.g. Royal Tulip Sea Pearl Beach Resort & Spa" required style="font-size:13px; border-radius:4px; height:38px;">
                        <span class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Registered commercial name of the hotel, resort, or ship.</span>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Property Type <span style="color:#ff4d4f;">*</span></label>
                        <select name="type" class="form-select" required style="font-size:13px; border-radius:4px; height:38px;">
                            <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>🏨 Hotel &amp; Resort</option>
                            <option value="resort" {{ old('type') == 'resort' ? 'selected' : '' }}>🏖️ Beach Resort &amp; Spa</option>
                            <option value="houseboat" {{ old('type') == 'houseboat' ? 'selected' : '' }}>🚢 Sundarban Ship &amp; Houseboat</option>
                            <option value="homestay" {{ old('type') == 'homestay' ? 'selected' : '' }}>🪵 Home Stay &amp; Eco Cottage</option>
                            <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>🏢 Serviced Apartment</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">City / Region Destination <span style="color:#ff4d4f;">*</span></label>
                        <select name="city" class="form-select" required style="font-size:13px; border-radius:4px; height:38px;">
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
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Star Rating <span style="color:#ff4d4f;">*</span></label>
                        <select name="star_rating" class="form-select" required style="font-size:13px; border-radius:4px; height:38px;">
                            <option value="5" {{ old('star_rating') == '5' ? 'selected' : '' }}>★★★★★ — 5 Star Luxury</option>
                            <option value="4" {{ old('star_rating') == '4' ? 'selected' : '' }}>★★★★ — 4 Star Premium</option>
                            <option value="3" {{ old('star_rating') == '3' ? 'selected' : '' }}>★★★ — 3 Star Standard</option>
                            <option value="2" {{ old('star_rating') == '2' ? 'selected' : '' }}>★★ — 2 Star Economy</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Assign to Vendor Account</label>
                        <select name="vendor_id" class="form-select" style="font-size:13px; border-radius:4px; height:38px;">
                            <option value="">Admin Listed (Prime Booking)</option>
                            @if(isset($vendors))
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Full Physical Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                            placeholder="e.g. Inani Beach, Marine Drive Road, Kolatoli, Cox's Bazar 4700, Bangladesh" required style="font-size:13px; border-radius:4px; height:38px;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Nearest Landmark / Distance</label>
                        <input type="text" name="nearest_landmark" class="form-control" value="{{ old('nearest_landmark') }}"
                            placeholder="e.g. 2 mins walk from Kolatoli Beach Point" style="font-size:13px; border-radius:4px; height:38px;">
                    </div>
                </div>
            </div>

            {{-- PANEL 2: Pricing & Policies --}}
            <div class="p-3 mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                        <i class="fa-solid fa-bangladeshi-taka-sign text-success"></i> Step 2 — Pricing &amp; Discount Setup
                    </h6>
                    <span class="text-secondary" style="font-size:11.5px;">Currency: BDT (৳)</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Base Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <span class="input-group-text bg-light text-dark fw-bold" style="font-size:12.5px; border-radius:4px 0 0 4px; padding:0 12px; height:38px;">৳ BDT</span>
                            <input type="number" name="price_per_night" class="form-control"
                                value="{{ old('price_per_night') }}" placeholder="8500" required style="font-size:13px; border-radius:0 4px 4px 0; height:38px;">
                        </div>
                        <span class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Lowest available room price.</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Original Crossed-Out Rate (BDT)</label>
                        <div style="display:flex;">
                            <span class="input-group-text bg-light text-muted" style="font-size:12.5px; border-radius:4px 0 0 4px; padding:0 12px; height:38px;">৳ BDT</span>
                            <input type="number" name="original_price" class="form-control"
                                value="{{ old('original_price') }}" placeholder="11000" style="font-size:13px; border-radius:0 4px 4px 0; height:38px;">
                        </div>
                        <span class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Shows as <s>BDT 11,000</s> with discount badge.</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Listing Visibility Status</label>
                        <select name="status" class="form-select" style="font-size:13px; border-radius:4px; height:38px;">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active — Published &amp; Searchable</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive — Draft / Hidden</option>
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="p-3 bg-white border d-flex align-items-center justify-content-between flex-wrap gap-3" style="border-radius:4px;">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                    {{ old('is_featured') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-dark ms-1.5" for="isFeatured" style="font-size:12.5px; cursor:pointer;">
                                    <i class="fa-solid fa-star text-warning me-1"></i> Featured Property (Homepage Carousel)
                                </label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="freeCancel"
                                    {{ old('free_cancellation', '1') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-success ms-1.5" for="freeCancel" style="font-size:12.5px; cursor:pointer;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Free Cancellation Allowed
                                </label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" id="noCC"
                                    {{ old('no_credit_card_required', '1') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-primary ms-1.5" for="noCC" style="font-size:12.5px; cursor:pointer;">
                                    <i class="fa-solid fa-credit-card me-1"></i> Pay at Hotel / Cash on Arrival
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL 3: Media & Video Tour --}}
            <div class="p-3 mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                        <i class="fa-solid fa-images text-purple" style="color:#7367f0;"></i> Step 3 — Property Images &amp; Video Tour
                    </h6>
                    <span class="text-secondary" style="font-size:11.5px;">CDN Image URLs</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Primary Thumbnail Image URL <span style="color:#ff4d4f;">*</span></label>
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control"
                            value="{{ old('primary_image') }}" placeholder="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80"
                            oninput="previewImage(this.value)" style="font-size:13px; border-radius:4px; height:38px;">
                        <div id="imgPreviewWrap" class="mt-2" style="display:none;">
                            <img id="imgPreview" src="" style="height:80px; border-radius:4px; border:1px solid #cbd5e1; object-fit:cover;" alt="Preview">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Video Property Tour URL (YouTube Embed / MP4)</label>
                        <input type="url" name="video_url" class="form-control"
                            value="{{ old('video_url') }}" placeholder="e.g. https://www.youtube.com/embed/dQw4w9WgXcQ" style="font-size:13px; border-radius:4px; height:38px;">
                        <span class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Enables 360 Video Tour button on search results &amp; detail pages.</span>
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Gallery Image URLs (one per line, max 10)</label>
                        <textarea name="gallery_images" class="form-control" rows="3"
                            placeholder="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1000&q=80&#10;https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80" style="font-size:13px; border-radius:4px;">{{ old('gallery_images') }}</textarea>
                        <span class="text-secondary" style="font-size:11px; margin-top:4px; display:block;">Enter each image URL on a new line for gallery slider.</span>
                    </div>
                </div>
            </div>

            {{-- PANEL 4: Description & Guest Guidelines --}}
            <div class="p-3 mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                        <i class="fa-solid fa-align-left text-warning"></i> Step 4 — Description &amp; Guest Guidelines
                    </h6>
                    <span class="badge bg-light text-secondary border" style="font-size:11px; border-radius:4px; padding:4px 8px;">SEO Text</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Full Property Description <span style="color:#ff4d4f;">*</span></label>
                        <textarea name="description" class="form-control" rows="4"
                            placeholder="Describe location highlights, room types, sea view, check-in policy, complimentary breakfast, nearby attractions..." required style="font-size:13px; border-radius:4px;">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- PANEL 5: Amenities Grid --}}
            <div class="p-3 mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                        <i class="fa-solid fa-list-check text-primary"></i> Step 5 — Property Amenities &amp; Facilities
                    </h6>
                    <span class="text-secondary" style="font-size:11.5px;">Active Amenities</span>
                </div>
                <div class="row g-2.5">
                    @foreach([
                        ['wifi',       'fa-wifi',              'Free High-Speed WiFi'],
                        ['pool',       'fa-person-swimming',   'Swimming Pool'],
                        ['parking',    'fa-car',               'Free Parking'],
                        ['ac',         'fa-snowflake',         'Air Conditioning'],
                        ['restaurant', 'fa-utensils',          'On-site Restaurant'],
                        ['breakfast',  'fa-mug-hot',           'Breakfast Included'],
                        ['gym',        'fa-dumbbell',          'Fitness Center / Gym'],
                        ['spa',        'fa-spa',               'Spa &amp; Wellness'],
                        ['bar',        'fa-wine-glass',        'Rooftop Bar &amp; Lounge'],
                        ['beachfront', 'fa-water',             'Beachfront / Sea View'],
                        ['pet',        'fa-paw',               'Pet-Friendly Policy'],
                        ['transfer',   'fa-van-shuttle',       'Airport Transfer'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <div class="p-2 border bg-white d-flex align-items-center gap-2" style="border-radius:4px; cursor:pointer;">
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

            {{-- PANEL 6: Room Types Setup --}}
            <div class="p-3 mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3" style="border-color:#e2e8f0 !important;">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14.5px;">
                        <i class="fa-solid fa-bed text-success"></i> Step 6 — Room Types &amp; Capacity (Optional)
                    </h6>
                    <span class="text-secondary" style="font-size:11.5px;">Auto-creates room categories</span>
                </div>
                <div class="row g-3">
                    @foreach([
                        ['Standard Deluxe Room', 8500, 10, 2],
                        ['Executive Sea View Suite', 14500, 5, 2],
                        ['Presidential Family Suite', 24500, 2, 4],
                    ] as $r)
                    <div class="col-md-4">
                        <div class="p-3 border bg-white" style="border-radius:4px;">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size:12.5px;">{{ $r[0] }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-secondary mb-1" style="font-size:11px;">Price/Night (BDT)</label>
                                    <input type="number" name="room_price[]" class="form-control form-control-sm" placeholder="{{ $r[1] }}" style="font-size:12px; border-radius:4px; height:32px;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-secondary mb-1" style="font-size:11px;">Available Qty</label>
                                    <input type="number" name="room_qty[]" class="form-control form-control-sm" placeholder="{{ $r[2] }}" style="font-size:12px; border-radius:4px; height:32px;">
                                </div>
                                <input type="hidden" name="room_type[]" value="{{ $r[0] }}">
                                <input type="hidden" name="room_beds[]" value="{{ $r[3] }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <span class="text-secondary mt-2 d-block" style="font-size:11px;">Leave blank if this property has single fixed room pricing. You can also manage rooms anytime from <strong>Manage Rooms</strong> panel.</span>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="d-flex align-items-center justify-content-end gap-2.5 pt-3 border-top mt-4" style="border-color:#e2e8f0 !important;">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-light text-secondary border fw-bold" style="border-radius:4px; font-size:13px; height:38px; padding:0 20px; display:inline-flex; align-items:center;">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary fw-bold" style="border-radius:4px; font-size:13px; height:38px; padding:0 20px; display:inline-flex; align-items:center;">
                    Save as Draft <i class="fa-solid fa-floppy-disk ms-1.5"></i>
                </button>
                <button type="submit" name="action" value="publish" class="btn btn-primary text-white fw-bold" style="background-color:var(--primary); border-radius:4px; font-size:13px; height:38px; padding:0 24px; border:none; display:inline-flex; align-items:center;">
                    Publish Listing Live <i class="fa-solid fa-rocket ms-1.5"></i>
                </button>
            </div>

        </div>

    </form>
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
