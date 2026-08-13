@if($errors->any())
<div class="admin-alert error mb-4" style="border-radius:4px; padding:12px 16px;">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="form-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px; padding:20px;">
            <div class="border-bottom pb-2 mb-3" style="font-weight:700; font-size:14.5px; color:#0f172a;">
                <i class="fa-solid fa-tag me-2 text-primary"></i> Special Deal Information
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $deal?->title) }}" placeholder="e.g. Flash Sale: Cox's Bazar Luxury Resorts" required style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Subtitle / Tagline</label>
                    <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $deal?->subtitle) }}" placeholder="e.g. Exclusive beach resort discount for Prime members" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Discount %</label>
                    <input type="number" name="discount_pct" class="form-control" value="{{ old('discount_pct', $deal?->discount_pct) }}" placeholder="e.g. 25" step="0.1" min="0" max="100" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Original Price (৳)</label>
                    <input type="number" name="original_price" class="form-control" value="{{ old('original_price', $deal?->original_price) }}" placeholder="e.g. 10000" step="0.01" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Sale Price (৳)</label>
                    <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $deal?->sale_price) }}" placeholder="e.g. 7500" step="0.01" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Valid Until (Expiry Date)</label>
                    <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until', $deal?->valid_until ? $deal->valid_until->format('Y-m-d\TH:i') : '') }}" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', $deal?->badge_text) }}" placeholder="e.g. 25% OFF or FLASH SALE" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Image URL</label>
                    <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $deal?->image_url) }}" placeholder="https://images.unsplash.com/photo-..." style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Target Link URL</label>
                    <input type="text" name="link_url" class="form-control" value="{{ old('link_url', $deal?->link_url) }}" placeholder="/search?destination=Cox%27s+Bazar" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
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
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Deal Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
                    <option value="hotel" @selected(old('type', $deal?->type) === 'hotel')>Hotel</option>
                    <option value="flight" @selected(old('type', $deal?->type) === 'flight')>Flight</option>
                    <option value="package" @selected(old('type', $deal?->type) === 'package')>Package</option>
                    <option value="activity" @selected(old('type', $deal?->type) === 'activity')>Activity</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:5px;">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $deal?->sort_order ?? 0) }}" min="0" style="font-size:13px; border-radius:4px; height:38px; padding:0 12px;">
            </div>
            <div class="p-3 bg-light border d-flex flex-column gap-2.5 mt-3" style="border-radius:4px;">
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $deal?->is_active ?? true) ? 'checked' : '' }} style="cursor:pointer; margin-top:0;">
                    <label class="form-check-label fw-bold text-dark mb-0" for="is_active" style="font-size:12.5px; cursor:pointer;">
                        <i class="fa-solid fa-toggle-on me-1 text-success"></i> Active (visible on website)
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
