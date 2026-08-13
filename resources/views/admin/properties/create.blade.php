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
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title m-0">Add New Property Listing</h1>
        <a href="{{ route('admin.properties.index') }}" class="btn-table-action" style="font-size:13px; padding:6px 14px; border-radius:4px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">
    <form action="{{ route('admin.properties.store') }}" method="POST">
        @csrf

            @if ($errors->any())
                <div class="admin-alert error mb-3" style="border-radius:4px; padding:10px 14px;">
                    <i class="fa-solid fa-circle-xmark me-2"></i>
                    <strong>Please review errors:</strong> {{ implode(', ', $errors->all()) }}
                </div>
            @endif

            {{-- SINGLE UNIFIED COMPACT ENTERPRISE CARD --}}
            <div class="form-card" style="border-radius:4px; background:#ffffff; border:1px solid #e2e8f0; padding:22px;">
                
                {{-- SECTION 1: Basic Specifications --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14px;">
                        <i class="fa-solid fa-hotel text-primary"></i> 1. Basic Specifications &amp; Classification
                    </h6>
                    <span class="badge bg-light text-secondary border" style="font-size:10.5px; border-radius:4px;">Required *</span>
                </div>
                <div class="row g-2.5 mb-3">
                    <div class="col-md-7">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Property / Hotel / Ship Full Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}"
                            placeholder="e.g. Royal Tulip Sea Pearl Beach Resort & Spa" required style="font-size:12.5px; border-radius:4px; height:34px;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Property Type <span style="color:#ff4d4f;">*</span></label>
                        <select name="type" class="form-select form-select-sm" required style="font-size:12.5px; border-radius:4px; height:34px;">
                            <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>🏨 Hotel &amp; Resort</option>
                            <option value="resort" {{ old('type') == 'resort' ? 'selected' : '' }}>🏖️ Beach Resort &amp; Spa</option>
                            <option value="houseboat" {{ old('type') == 'houseboat' ? 'selected' : '' }}>🚢 Sundarban Ship &amp; Houseboat</option>
                            <option value="homestay" {{ old('type') == 'homestay' ? 'selected' : '' }}>🪵 Home Stay &amp; Eco Cottage</option>
                            <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>🏢 Serviced Apartment</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">City / Destination <span style="color:#ff4d4f;">*</span></label>
                        <select name="city" class="form-select form-select-sm" required style="font-size:12.5px; border-radius:4px; height:34px;">
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
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Star Rating <span style="color:#ff4d4f;">*</span></label>
                        <select name="star_rating" class="form-select form-select-sm" required style="font-size:12.5px; border-radius:4px; height:34px;">
                            <option value="5" {{ old('star_rating') == '5' ? 'selected' : '' }}>★★★★★ — 5 Star Luxury</option>
                            <option value="4" {{ old('star_rating') == '4' ? 'selected' : '' }}>★★★★ — 4 Star Premium</option>
                            <option value="3" {{ old('star_rating') == '3' ? 'selected' : '' }}>★★★ — 3 Star Standard</option>
                            <option value="2" {{ old('star_rating') == '2' ? 'selected' : '' }}>★★ — 2 Star Economy</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Assign Vendor Account</label>
                        <select name="vendor_id" class="form-select form-select-sm" style="font-size:12.5px; border-radius:4px; height:34px;">
                            <option value="">Admin Listed (Prime Booking)</option>
                            @if(isset($vendors))
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Full Physical Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address') }}"
                            placeholder="e.g. Inani Beach, Marine Drive Road, Kolatoli, Cox's Bazar 4700" required style="font-size:12.5px; border-radius:4px; height:34px;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Nearest Landmark / Distance</label>
                        <input type="text" name="nearest_landmark" class="form-control form-control-sm" value="{{ old('nearest_landmark') }}"
                            placeholder="e.g. 2 mins walk from Kolatoli Beach Point" style="font-size:12.5px; border-radius:4px; height:34px;">
                    </div>
                </div>

                {{-- SECTION 2: Pricing & Policies --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3 mt-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14px;">
                        <i class="fa-solid fa-bangladeshi-taka-sign text-success"></i> 2. Pricing &amp; Booking Policies
                    </h6>
                    <span class="text-secondary" style="font-size:11px;">BDT (৳)</span>
                </div>
                <div class="row g-2.5 mb-3">
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Base Price Per Night <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <span class="input-group-text bg-light text-dark fw-bold" style="font-size:11.5px; border-radius:4px 0 0 4px; padding:0 8px; height:34px;">৳ BDT</span>
                            <input type="number" name="price_per_night" class="form-control form-control-sm"
                                value="{{ old('price_per_night') }}" placeholder="8500" required style="font-size:12.5px; border-radius:0 4px 4px 0; height:34px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Crossed-Out Original Rate</label>
                        <div style="display:flex;">
                            <span class="input-group-text bg-light text-muted" style="font-size:11.5px; border-radius:4px 0 0 4px; padding:0 8px; height:34px;">৳ BDT</span>
                            <input type="number" name="original_price" class="form-control form-control-sm"
                                value="{{ old('original_price') }}" placeholder="11000" style="font-size:12.5px; border-radius:0 4px 4px 0; height:34px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Visibility Status</label>
                        <select name="status" class="form-select form-select-sm" style="font-size:12.5px; border-radius:4px; height:34px;">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active — Published Live</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive — Draft Mode</option>
                        </select>
                    </div>
                    <div class="col-12 mt-1">
                        <div class="p-2.5 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius:4px;">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                    {{ old('is_featured') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-dark ms-1" for="isFeatured" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-star text-warning me-1"></i> Featured Property
                                </label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="free_cancellation" value="1" id="freeCancel"
                                    {{ old('free_cancellation', '1') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-success ms-1" for="freeCancel" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Free Cancellation
                                </label>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="no_credit_card_required" value="1" id="noCC"
                                    {{ old('no_credit_card_required', '1') ? 'checked' : '' }} style="cursor:pointer;">
                                <label class="form-check-label fw-bold text-primary ms-1" for="noCC" style="font-size:12px; cursor:pointer;">
                                    <i class="fa-solid fa-credit-card me-1"></i> Pay at Hotel / Cash on Arrival
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: Media & Video Tour --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3 mt-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14px;">
                        <i class="fa-solid fa-images text-purple" style="color:#7367f0;"></i> 3. Property Media &amp; Video Tour
                    </h6>
                    <span class="text-secondary" style="font-size:11px;">Image CDN URLs</span>
                </div>
                <div class="row g-2.5 mb-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Primary Thumbnail Image URL <span style="color:#ff4d4f;">*</span></label>
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control form-control-sm"
                            value="{{ old('primary_image') }}" placeholder="https://images.unsplash.com/photo-..."
                            oninput="previewImage(this.value)" style="font-size:12.5px; border-radius:4px; height:34px;">
                        <div id="imgPreviewWrap" class="mt-2" style="display:none;">
                            <img id="imgPreview" src="" style="height:65px; border-radius:4px; border:1px solid #cbd5e1; object-fit:cover;" alt="Preview">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Video Property Tour URL (YouTube Embed / MP4)</label>
                        <input type="url" name="video_url" class="form-control form-control-sm"
                            value="{{ old('video_url') }}" placeholder="https://www.youtube.com/embed/..." style="font-size:12.5px; border-radius:4px; height:34px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Gallery Image URLs (one per line, max 10)</label>
                        <textarea name="gallery_images" class="form-control" rows="2"
                            placeholder="https://images.unsplash.com/photo-1571896349842...&#10;https://images.unsplash.com/photo-1582719478250..." style="font-size:12px; border-radius:4px;">{{ old('gallery_images') }}</textarea>
                    </div>
                </div>

                {{-- SECTION 4: Description --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3 mt-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14px;">
                        <i class="fa-solid fa-align-left text-warning"></i> 4. Description &amp; Guest Overview
                    </h6>
                </div>
                <div class="row g-2.5 mb-3">
                    <div class="col-12">
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Detail location highlights, room types, sea view, check-in policy, complimentary breakfast..." required style="font-size:12.5px; border-radius:4px;">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- SECTION 5: Amenities Grid --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3 mt-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14px;">
                        <i class="fa-solid fa-list-check text-primary"></i> 5. Property Amenities &amp; Facilities
                    </h6>
                </div>
                <div class="row g-2 mb-3">
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
                        ['beachfront', 'fa-water',             'Beachfront / Sea View'],
                        ['pet',        'fa-paw',               'Pet-Friendly'],
                        ['transfer',   'fa-van-shuttle',       'Airport Shuttle'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <div class="p-1.5 px-2 border bg-light d-flex align-items-center gap-2" style="border-radius:4px; cursor:pointer;">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" id="am_{{ $am[0] }}"
                                {{ in_array($am[0], old('amenities', [])) ? 'checked' : '' }} style="cursor:pointer;">
                            <label for="am_{{ $am[0] }}" class="mb-0 text-dark fw-semibold d-flex align-items-center gap-1.5" style="cursor:pointer; font-size:11.5px;">
                                <i class="fa-solid {{ $am[1] }} text-primary"></i> {!! $am[2] !!}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- SECTION 6: Room Setup --}}
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3 mt-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size:14px;">
                        <i class="fa-solid fa-bed text-success"></i> 6. Initial Room Categories Setup (Optional)
                    </h6>
                </div>
                <div class="row g-2.5 mb-2">
                    @foreach([
                        ['Standard Deluxe Room', 8500, 10, 2],
                        ['Executive Sea View Suite', 14500, 5, 2],
                        ['Presidential Family Suite', 24500, 2, 4],
                    ] as $r)
                    <div class="col-md-4">
                        <div class="p-2.5 border bg-light" style="border-radius:4px;">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:12px;">{{ $r[0] }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-secondary mb-0" style="font-size:10.5px;">Price/Night (BDT)</label>
                                    <input type="number" name="room_price[]" class="form-control form-control-sm" placeholder="{{ $r[1] }}" style="font-size:11.5px; border-radius:4px; height:30px;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-secondary mb-0" style="font-size:10.5px;">Qty Available</label>
                                    <input type="number" name="room_qty[]" class="form-control form-control-sm" placeholder="{{ $r[2] }}" style="font-size:11.5px; border-radius:4px; height:30px;">
                                </div>
                                <input type="hidden" name="room_type[]" value="{{ $r[0] }}">
                                <input type="hidden" name="room_beds[]" value="{{ $r[3] }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- FORM ACTIONS --}}
                <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top mt-3">
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-light text-secondary border fw-bold px-3 py-1.5" style="border-radius:4px; font-size:12.5px;">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary fw-bold px-3 py-1.5" style="border-radius:4px; font-size:12.5px;">
                        Save as Draft <i class="fa-solid fa-floppy-disk ms-1"></i>
                    </button>
                    <button type="submit" name="action" value="publish" class="btn btn-primary text-white fw-bold px-4 py-1.5" style="background-color:var(--primary); border-radius:4px; font-size:12.5px; border:none;">
                        Publish Listing Live <i class="fa-solid fa-rocket ms-1"></i>
                    </button>
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
