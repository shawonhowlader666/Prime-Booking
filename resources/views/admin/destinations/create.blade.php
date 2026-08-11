@extends('layouts.admin')
@section('title', isset($destination) ? 'Edit Destination' : 'Add Destination')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 style="font-size:19px;font-weight:700;margin:0;">
        {{ isset($destination) ? "Edit: {$destination->city}" : 'Add New Destination' }}
    </h1>
</div>

@if(session('success'))<div class="admin-alert success">{{ session('success') }}</div>@endif

@php
    $action = isset($destination)
        ? route('admin.destinations.update', $destination)
        : route('admin.destinations.store');
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if(isset($destination)) @method('PUT') @endif

    @if($errors->any())
    <div class="admin-alert error mb-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="stockifly-card">
                <div class="card-header-stockifly mb-2"><i class="fa-solid fa-map-pin me-1"></i> Destination Info</div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label-sm">City Name <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control form-control-sm @error('city') is-invalid @enderror"
                            value="{{ old('city', $destination?->city) }}" placeholder="Cox's Bazar" required>
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-sm">Country</label>
                        <input type="text" name="country" class="form-control form-control-sm"
                            value="{{ old('country', $destination?->country ?? 'Bangladesh') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label-sm">Image URL <span class="text-danger">*</span></label>
                        <input type="url" name="image_url" class="form-control form-control-sm @error('image_url') is-invalid @enderror"
                            value="{{ old('image_url', $destination?->image_url) }}"
                            placeholder="https://images.unsplash.com/photo-...?w=400"
                            oninput="document.getElementById('imgPreview').src=this.value" required>
                        @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if($destination?->image_url || old('image_url'))
                    <div class="col-12">
                        <img id="imgPreview" src="{{ old('image_url', $destination?->image_url) }}"
                             alt="Preview" style="height:120px;border-radius:8px;object-fit:cover;max-width:250px;">
                    </div>
                    @else
                    <div class="col-12">
                        <img id="imgPreview" src="" alt="" style="height:120px;border-radius:8px;object-fit:cover;max-width:250px;display:none;">
                    </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label-sm">Description / Tagline</label>
                        <input type="text" name="description" class="form-control form-control-sm"
                            value="{{ old('description', $destination?->description) }}"
                            placeholder="World's longest sea beach" maxlength="200">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-sm">Property Count Override</label>
                        <input type="number" name="property_count_override" class="form-control form-control-sm"
                            value="{{ old('property_count_override', $destination?->property_count_override) }}"
                            placeholder="Leave empty = auto" min="0">
                        <small class="text-muted" style="font-size:10px;">Auto = real DB count</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-sm">Min Price Override (৳)</label>
                        <input type="number" name="min_price_override" class="form-control form-control-sm"
                            value="{{ old('min_price_override', $destination?->min_price_override) }}"
                            placeholder="Leave empty = auto" min="0">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-sm">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control form-control-sm"
                            value="{{ old('sort_order', $destination?->sort_order ?? 0) }}" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stockifly-card">
                <div class="card-header-stockifly mb-2"><i class="fa-solid fa-sliders me-1"></i> Visibility</div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $destination?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        <i class="fa-solid fa-eye me-1 text-success"></i> Active (visible on homepage)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                        {{ old('is_featured', $destination?->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">
                        <i class="fa-solid fa-star me-1" style="color:#f5c518;"></i> Featured (pinned to top)
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2 align-items-center">
        <button type="submit" class="btn-stockifly-primary">
            <i class="fa-solid fa-save me-1"></i> {{ isset($destination) ? 'Update Destination' : 'Add Destination' }}
        </button>
        <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary">Cancel</a>

        @if(isset($destination))
        <form action="{{ route('admin.destinations.destroy', $destination) }}" method="POST"
              class="ms-auto" onsubmit="return confirm('Remove {{ $destination->city }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-trash me-1"></i> Delete
            </button>
        </form>
        @endif
    </div>
</form>

@push('scripts')
<script>
// Show image preview as URL is typed
document.querySelector('[name=image_url]').addEventListener('input', function() {
    const img = document.getElementById('imgPreview');
    img.src = this.value;
    img.style.display = this.value ? 'block' : 'none';
});
</script>
@endpush
@endsection
