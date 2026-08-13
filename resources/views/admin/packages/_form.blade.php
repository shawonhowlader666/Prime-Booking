@if($errors->any())
<div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="form-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px;">
            <div class="border-bottom pb-2 mb-3" style="font-weight:700; font-size:14.5px; color:#0f172a;">
                <i class="fa-solid fa-suitcase-rolling me-2 text-primary"></i> Package Information
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $package?->title) }}" placeholder="e.g. Bangkok & Phuket Fantasy" required style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Days / Duration <span class="text-danger">*</span></label>
                    <input type="text" name="days" class="form-control" value="{{ old('days', $package?->days) }}" placeholder="e.g. 5D/4N or 14 Days" required style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Price (BDT ৳) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $package?->price) }}" placeholder="e.g. 45000" step="0.01" required style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Badge Text</label>
                    <input type="text" name="badge" class="form-control" value="{{ old('badge', $package?->badge) }}" placeholder="e.g. Popular, Best Seller, Honeymoon" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Image URL</label>
                    <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $package?->image_url) }}" placeholder="https://images.unsplash.com/photo-..." style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Includes List (One item per line)</label>
                    <textarea name="includes" class="form-control" rows="3" placeholder="Flight&#10;4 Star Hotel&#10;Breakfast&#10;City Tour" style="font-size:13px; border-radius:4px; padding:10px 12px;">{{ old('includes', is_array($package?->includes) ? implode("\n", $package->includes) : '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Full package description" style="font-size:13px; border-radius:4px; padding:10px 12px;">{{ old('description', $package?->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px;">
            <div class="border-bottom pb-2 mb-3" style="font-weight:700; font-size:14.5px; color:#0f172a;">
                <i class="fa-solid fa-sliders me-2 text-warning"></i> Settings
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $package?->sort_order ?? 0) }}" min="0" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
            </div>
            <div class="p-3 bg-light border d-flex flex-column gap-2.5 mt-3" style="border-radius:4px;">
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $package?->is_active ?? true) ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                    <label class="form-check-label fw-bold text-dark mb-0" for="is_active" style="font-size:12.5px; cursor:pointer;">
                        <i class="fa-solid fa-toggle-on me-1 text-success"></i> Active (visible on website)
                    </label>
                </div>
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $package?->is_featured ?? false) ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                    <label class="form-check-label fw-bold text-warning mb-0" for="is_featured" style="font-size:12.5px; cursor:pointer;">
                        <i class="fa-solid fa-star me-1" style="color:#f5c518;"></i> Featured (on homepage)
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
