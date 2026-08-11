@extends('layouts.admin')
@section('title', 'Add New Property Listing | Prime Aviation Admin')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-card">
    <div class="page-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span><a href="{{ route('admin.properties.index') }}">Inventory</a>
        <span class="sep">-</span><strong style="color:#333;">Add New Listing</strong>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:6px;">
        <h1 class="page-title">Add New Hotel, Ship or Cottage Listing</h1>
        <a href="{{ route('admin.properties.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959;">
            <i class="fa-solid fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

{{-- PAGE CONTENT --}}
<div class="page-content-area">
    <div style="max-width:920px; margin:0 auto;">
        <form action="{{ route('admin.properties.store') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="admin-alert error mb-3">
                    <i class="fa-solid fa-circle-xmark me-1"></i>
                    Please fix the errors below: {{ implode(', ', $errors->all()) }}
                </div>
            @endif

            {{-- STEP 1: Basic Info --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-hotel me-1"></i> Step 1 — Basic Info &amp; Category
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Property / Hotel / Ship Full Name <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="e.g. MV Zabin Sundarban Luxury Cruise Ship" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Property Type <span style="color:#ff4d4f;">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>Hotel &amp; Resort</option>
                            <option value="houseboat" {{ old('type') == 'houseboat' ? 'selected' : '' }}>Sundarban Ship &amp; Houseboat</option>
                            <option value="homestay" {{ old('type') == 'homestay' ? 'selected' : '' }}>Home Stay &amp; Eco Cottage</option>
                            <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Apartment / Suite</option>
                            <option value="resort" {{ old('type') == 'resort' ? 'selected' : '' }}>Beach Resort</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">City / Tourist Destination <span style="color:#ff4d4f;">*</span></label>
                        <select name="city" class="form-select" required>
                            <option value="Cox's Bazar Sea Beach">Cox's Bazar Sea Beach</option>
                            <option value="Dhaka City">Dhaka City</option>
                            <option value="Sylhet & Sreemangal">Sylhet &amp; Sreemangal</option>
                            <option value="Sajek Valley & Rangamati">Sajek Valley &amp; Rangamati</option>
                            <option value="Sundarbans & Mongla">Sundarbans &amp; Mongla</option>
                            <option value="Kuakata Sunset Beach">Kuakata Sunset Beach</option>
                            <option value="Chittagong City">Chittagong City</option>
                            <option value="Bandarban Hill District">Bandarban Hill District</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Star Rating <span style="color:#ff4d4f;">*</span></label>
                        <select name="star_rating" class="form-select" required>
                            <option value="5">★★★★★ — 5 Star Luxury</option>
                            <option value="4">★★★★ — 4 Star Premium</option>
                            <option value="3">★★★ — 3 Star Standard</option>
                            <option value="2">★★ — 2 Star Budget</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Assign to Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">Admin Listed (No Vendor)</option>
                            @if(isset($vendors))
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Property Address <span style="color:#ff4d4f;">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                            placeholder="e.g. Marine Drive, Cox's Bazar 4700, Chittagong Division, Bangladesh" required>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Pricing --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Step 2 — Pricing &amp; Discount Setup
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Base Price Per Night (BDT ৳) <span style="color:#ff4d4f;">*</span></label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳ BDT</span>
                            <input type="number" name="price_per_night" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('price_per_night') }}" placeholder="12500" required>
                        </div>
                        <span style="font-size:11px; color:#8c8c8c; margin-top:4px; display:block;">Lowest available room price</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Original / Crossed-Out Price (BDT)</label>
                        <div style="display:flex;">
                            <span class="input-group-text">৳ BDT</span>
                            <input type="number" name="original_price" class="form-control" style="border-radius:0 6px 6px 0;"
                                value="{{ old('original_price') }}" placeholder="15000">
                        </div>
                        <span style="font-size:11px; color:#8c8c8c; margin-top:4px; display:block;">Shows as <s>BDT 15,000</s> → discount badge</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Listing Status &amp; Visibility</label>
                        <select name="status" class="form-select">
                            <option value="active">Active — Published &amp; Searchable</option>
                            <option value="inactive">Inactive — Draft / Hidden</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                    {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isFeatured" style="font-size:13px;">
                                    <i class="fa-solid fa-star" style="color:#ff9f43;"></i> Mark as Featured Property (Homepage Carousel)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Images --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-images me-1"></i> Step 3 — Property Images
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Primary / Thumbnail Image URL <span style="color:#ff4d4f;">*</span></label>
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control"
                            value="{{ old('primary_image') }}" placeholder="https://images.unsplash.com/photo-... or your CDN URL"
                            oninput="previewImage(this.value)">
                        <div id="imgPreviewWrap" style="margin-top:8px; display:none;">
                            <img id="imgPreview" src="" style="height:80px; border-radius:6px; border:1px solid #e8e8e8;" alt="Preview">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Official Video Tour URL (YouTube Embed / Vimeo Link)</label>
                        <input type="url" name="video_url" class="form-control"
                            value="{{ old('video_url') }}" placeholder="e.g. https://www.youtube.com/embed/dQw4w9WgXcQ">
                        <span style="font-size:11px; color:#8c8c8c; margin-top:4px; display:block;">Enter a YouTube embed URL (or Vimeo/MP4 video link). This enables the "VIDEO TOUR" button on search results & detail pages.</span>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gallery Image URLs (one per line, max 8)</label>
                        <textarea name="gallery_images" class="form-control" rows="3"
                            placeholder="https://cdn.example.com/image1.jpg&#10;https://cdn.example.com/image2.jpg&#10;https://cdn.example.com/image3.jpg">{{ old('gallery_images') }}</textarea>
                        <span style="font-size:11px; color:#8c8c8c; margin-top:4px; display:block;">Enter each image URL on a new line. These will appear in the property gallery slider.</span>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Description --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-align-left me-1"></i> Step 4 — Description &amp; Guest Info
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Full Property Description <span style="color:#ff4d4f;">*</span></label>
                        <textarea name="description" class="form-control" rows="5"
                            placeholder="Describe the property — location highlights, room types, unique features, sea view, check-in policy, breakfast included, nearby attractions..." required>{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 5: Amenities --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-list-check me-1"></i> Step 5 — Amenities &amp; Facilities
                </div>
                <div class="row g-2">
                    @foreach([
                        ['wifi',       'fa-wifi',              'Free WiFi'],
                        ['pool',       'fa-person-swimming',   'Swimming Pool'],
                        ['parking',    'fa-car',               'Free Parking'],
                        ['ac',         'fa-snowflake',         'Air Conditioning'],
                        ['restaurant', 'fa-utensils',          'Restaurant On-site'],
                        ['breakfast',  'fa-mug-hot',           'Breakfast Included'],
                        ['gym',        'fa-dumbbell',          'Fitness Center / Gym'],
                        ['spa',        'fa-spa',               'Spa &amp; Wellness'],
                        ['bar',        'fa-wine-glass',        'Rooftop Bar &amp; Lounge'],
                        ['beachfront', 'fa-water',             'Beachfront / Sea View'],
                        ['pet',        'fa-paw',               'Pet-Friendly'],
                        ['transfer',   'fa-van-shuttle',       'Airport Transfer'],
                        ['laundry',    'fa-shirt',             'Laundry Service'],
                        ['elevator',   'fa-elevator',          'Elevator / Lift'],
                    ] as $am)
                    <div class="col-6 col-md-3">
                        <div style="padding:8px 10px; border:1px solid #e8e8e8; border-radius:6px; display:flex; align-items:center; gap:8px; cursor:pointer; background:#fafafa; transition:all 0.15s;" onmouseover="this.style.borderColor='#1890ff'" onmouseout="this.style.borderColor='#e8e8e8'">
                            <input type="checkbox" name="amenities[]" value="{{ $am[0] }}" id="am_{{ $am[0] }}"
                                {{ in_array($am[0], old('amenities', [])) ? 'checked' : '' }}
                                style="cursor:pointer;">
                            <label for="am_{{ $am[0] }}" style="font-size:12px; color:#334155; cursor:pointer; margin:0; display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid {{ $am[1] }}" style="color:var(--primary); width:14px;"></i> {!! $am[2] !!}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- STEP 6: Room Types --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-bed me-1"></i> Step 6 — Room Types &amp; Capacity (Optional)
                </div>
                <div class="row g-3">
                    @foreach([
                        ['Standard Room', 1, 2],
                        ['Deluxe Room', 2, 3],
                        ['Suite', 3, 4],
                    ] as $r)
                    <div class="col-md-4">
                        <div style="padding:12px; border:1px solid #e8e8e8; border-radius:6px; background:#fafafa;">
                            <label class="form-label fw-bold" style="color:#1e293b; font-size:13px;">{{ $r[0] }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label" style="font-size:11px;">Price/Night (BDT)</label>
                                    <input type="number" name="room_price[]" class="form-control" style="height:32px; font-size:12px;" placeholder="8500">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" style="font-size:11px;">Qty Available</label>
                                    <input type="number" name="room_qty[]" class="form-control" style="height:32px; font-size:12px;" placeholder="10">
                                </div>
                                <input type="hidden" name="room_type[]" value="{{ $r[0] }}">
                                <input type="hidden" name="room_beds[]" value="{{ $r[1] }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p style="font-size:11.5px; color:#8c8c8c; margin:10px 0 0;">Leave blank if this property has a single fixed room pricing.</p>
            </div>

            {{-- FORM ACTIONS --}}
            <div style="display:flex; justify-content:flex-end; gap:10px; padding:16px 0;">
                <a href="{{ route('admin.properties.index') }}" class="btn-export-csv" style="border-color:#d9d9d9; color:#595959; padding:8px 20px;">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" class="btn-export-csv" style="padding:8px 20px;">
                    Save as Draft <i class="fa-solid fa-floppy-disk ms-1"></i>
                </button>
                <button type="submit" name="action" value="publish" class="btn-add-primary" style="padding:8px 28px;">
                    Publish Live Now <i class="fa-solid fa-rocket ms-1"></i>
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
