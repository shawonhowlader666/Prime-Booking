@if($errors->any())
<div class="admin-alert error mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">
    <div class="col-lg-8">
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly"><i class="fa-solid fa-tag me-1"></i> Deal Details</div>
            <div class="row g-2 mt-1">
                <div class="col-12">
                    <label class="form-label-sm">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $deal?->title) }}" placeholder="e.g. Flash Sale: Cox's Bazar Luxury Resorts" required>
                </div>
                <div class="col-12">
                    <label class="form-label-sm">Subtitle / Tagline</label>
                    <input type="text" name="subtitle" class="form-control form-control-sm" value="{{ old('subtitle', $deal?->subtitle) }}" placeholder="e.g. Exclusive beach resort discount for Prime members">
                </div>
                <div class="col-md-4">
                    <label class="form-label-sm">Discount %</label>
                    <input type="number" name="discount_pct" class="form-control form-control-sm" value="{{ old('discount_pct', $deal?->discount_pct) }}" placeholder="e.g. 25" step="0.1" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label-sm">Original Price (৳)</label>
                    <input type="number" name="original_price" class="form-control form-control-sm" value="{{ old('original_price', $deal?->original_price) }}" placeholder="e.g. 10000" step="0.01">
                </div>
                <div class="col-md-4">
                    <label class="form-label-sm">Sale Price (৳)</label>
                    <input type="number" name="sale_price" class="form-control form-control-sm" value="{{ old('sale_price', $deal?->sale_price) }}" placeholder="e.g. 7500" step="0.01">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Valid Until (Expiry Date)</label>
                    <input type="datetime-local" name="valid_until" class="form-control form-control-sm" value="{{ old('valid_until', $deal?->valid_until ? $deal->valid_until->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control form-control-sm" value="{{ old('badge_text', $deal?->badge_text) }}" placeholder="e.g. 25% OFF or FLASH SALE">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Image URL</label>
                    <input type="url" name="image_url" class="form-control form-control-sm" value="{{ old('image_url', $deal?->image_url) }}" placeholder="https://images.unsplash.com/photo-...">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Target Link URL</label>
                    <input type="text" name="link_url" class="form-control form-control-sm" value="{{ old('link_url', $deal?->link_url) }}" placeholder="/search?destination=Cox%27s+Bazar">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly"><i class="fa-solid fa-sliders me-1"></i> Settings</div>
            <div class="mt-2">
                <label class="form-label-sm">Deal Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select form-select-sm" required>
                    <option value="hotel" @selected(old('type', $deal?->type) === 'hotel')>Hotel</option>
                    <option value="flight" @selected(old('type', $deal?->type) === 'flight')>Flight</option>
                    <option value="package" @selected(old('type', $deal?->type) === 'package')>Package</option>
                    <option value="activity" @selected(old('type', $deal?->type) === 'activity')>Activity</option>
                </select>
            </div>
            <div class="mt-2">
                <label class="form-label-sm">Sort Order</label>
                <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ old('sort_order', $deal?->sort_order ?? 0) }}" min="0">
            </div>
            <div class="mt-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $deal?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (visible on website)</label>
                </div>
            </div>
        </div>
    </div>
</div>
