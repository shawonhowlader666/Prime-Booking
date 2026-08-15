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
            <a href="{{ route('hotels.show', $property->id) }}" target="_blank" class="btn-table-action primary" style="padding:6px 14px;">
                View Live <i class="fa-solid fa-external-link ms-1"></i>
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

        <form action="{{ route('admin.properties.update', $property->id) }}" method="POST">
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
                        <label class="form-label">Property Type <span style="color:#ff4d4f;">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach(['hotel' => 'Hotel & Resort', 'houseboat' => 'Sundarban Ship & Houseboat', 'homestay' => 'Home Stay & Eco Cottage', 'apartment' => 'Apartment / Suite', 'resort' => 'Beach Resort'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $property->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">City / Tourist Destination <span style="color:#ff4d4f;">*</span></label>
                        <select name="city" class="form-select" required>
                            @foreach(["Cox's Bazar Sea Beach", "Dhaka City", "Sylhet & Sreemangal", "Sajek Valley & Rangamati", "Sundarbans & Mongla", "Kuakata Sunset Beach", "Chittagong City", "Bandarban Hill District"] as $c)
                                <option value="{{ $c }}" {{ old('city', $property->city) == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
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

            {{-- STEP 2: Pricing --}}
            <div class="form-card mb-3">
                <div class="form-section-title">
                    <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i> Pricing &amp; Discount Setup
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
                        <label class="form-label">Original Price (Crossed-out BDT)</label>
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
                    <i class="fa-solid fa-images me-1"></i> Property Images
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Primary / Thumbnail Image URL</label>
                        @if($property->primary_image)
                            <div style="margin-bottom:8px; display:flex; align-items:center; gap:12px;">
                                <img src="{{ $property->primary_image }}" style="height:64px; border-radius:6px; border:1px solid #e8e8e8; object-fit:cover;" alt="Current Image">
                                <span style="font-size:11.5px; color:#8c8c8c;">Current primary image</span>
                            </div>
                        @endif
                        <input type="url" name="primary_image" id="primaryImgUrl" class="form-control"
                            value="{{ old('primary_image', $property->primary_image) }}"
                            placeholder="https://images.unsplash.com/photo-..."
                            oninput="previewImage(this.value)">
                        <div id="imgPreviewWrap" style="margin-top:8px; display:none;">
                            <img id="imgPreview" src="" style="height:80px; border-radius:6px; border:1px solid #e8e8e8;" alt="Preview">
                        </div>
                    <div class="col-12">
                        <label class="form-label">Official Video Tour URL (YouTube Embed / Vimeo Link)</label>
                        <input type="url" name="video_url" class="form-control"
                            value="{{ old('video_url', $property->video_url) }}" placeholder="e.g. https://www.youtube.com/embed/dQw4w9WgXcQ">
                        <span style="font-size:11px; color:#8c8c8c; margin-top:4px; display:block;">Enter a YouTube embed URL (or Vimeo/MP4 video link). This enables the "VIDEO TOUR" button on search results & detail pages.</span>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gallery Image URLs (one per line)</label>
                        <textarea name="gallery_images" class="form-control" rows="3"
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
<script>
function previewImage(url) {
    const wrap = document.getElementById('imgPreviewWrap');
    const img  = document.getElementById('imgPreview');
    if (url && url.startsWith('http')) {
        img.src = url;
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}
</script>
@endsection

