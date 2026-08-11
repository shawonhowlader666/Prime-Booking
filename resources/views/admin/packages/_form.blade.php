@if($errors->any())
<div class="admin-alert error mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">
    <div class="col-lg-8">
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly"><i class="fa-solid fa-suitcase-rolling me-1"></i> Package Information</div>
            <div class="row g-2 mt-1">
                <div class="col-12">
                    <label class="form-label-sm">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $package?->title) }}" placeholder="e.g. Bangkok & Phuket Fantasy" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Days / Duration <span class="text-danger">*</span></label>
                    <input type="text" name="days" class="form-control form-control-sm" value="{{ old('days', $package?->days) }}" placeholder="e.g. 5D/4N or 14 Days" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Price (BDT ৳) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control form-control-sm" value="{{ old('price', $package?->price) }}" placeholder="e.g. 45000" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Badge Text</label>
                    <input type="text" name="badge" class="form-control form-control-sm" value="{{ old('badge', $package?->badge) }}" placeholder="e.g. Popular, Best Seller, Honeymoon">
                </div>
                <div class="col-md-6">
                    <label class="form-label-sm">Image URL</label>
                    <input type="url" name="image_url" class="form-control form-control-sm" value="{{ old('image_url', $package?->image_url) }}" placeholder="https://images.unsplash.com/photo-...">
                </div>
                <div class="col-12">
                    <label class="form-label-sm">Includes List (One item per line)</label>
                    <textarea name="includes" class="form-control form-control-sm" rows="3" placeholder="Flight&#10;4 Star Hotel&#10;Breakfast&#10;City Tour">{{ old('includes', is_array($package?->includes) ? implode("\n", $package->includes) : '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label-sm">Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Full package description">{{ old('description', $package?->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stockifly-card mb-3">
            <div class="card-header-stockifly"><i class="fa-solid fa-sliders me-1"></i> Settings</div>
            <div class="mt-2">
                <label class="form-label-sm">Sort Order</label>
                <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ old('sort_order', $package?->sort_order ?? 0) }}" min="0">
            </div>
            <div class="mt-3 d-flex flex-column gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $package?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (visible on website)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $package?->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured (on homepage)</label>
                </div>
            </div>
        </div>
    </div>
</div>
