@extends('layouts.vendor')
@section('title', 'Edit Tour Package | Vendor Partner Portal')

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Tour Package: {{ $package->title }}
        </h1>
        <a href="{{ route('vendor.packages.index') }}" class="btn btn-light text-secondary border fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius: 4px; font-size: 13px; height: 36px; padding: 0 16px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Packages
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span>
        <a href="{{ route('vendor.packages.index') }}">Tour Packages</a>
        <span class="sep">-</span><strong style="color:#333;">Edit Package</strong>
    </div>
</div>

<div class="page-content-area">
    @if($errors->any())
    <div class="alert alert-danger p-2.5 small mb-3" style="border-radius: 4px;">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="card p-4 bg-white" style="max-width: 900px; border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('vendor.packages.update', $package->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Package Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $package->title) }}" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Destination City/Region <span class="text-danger">*</span></label>
                    <input type="text" name="destination" list="bd_vendor_pkg_edit_locations_datalist" class="form-control" placeholder="e.g. Cox's Bazar, Sylhet" required value="{{ old('destination', $package->destination) }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                    <datalist id="bd_vendor_pkg_edit_locations_datalist">
                        @foreach(app(\App\Services\Geo\BangladeshLocationService::class)->getFlatSearchableList() as $item)
                            <option value="{{ $item['name'] }}">{{ $item['display_name'] }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Duration Days <span class="text-danger">*</span></label>
                    <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $package->duration_days ?? $package->days ?? 3) }}" min="1" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Duration Nights <span class="text-danger">*</span></label>
                    <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', $package->duration_nights ?? 2) }}" min="0" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Price Per Person (BDT ৳) <span class="text-danger">*</span></label>
                    <input type="number" name="price_per_person" class="form-control" value="{{ old('price_per_person', $package->price_per_person ?? $package->price) }}" step="0.01" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Discount Price (Optional)</label>
                    <input type="number" name="discount_price" class="form-control" value="{{ old('discount_price', $package->discount_price) }}" step="0.01" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Max Seats / Group Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="max_seats" class="form-control" value="{{ old('max_seats', $package->max_seats ?? 20) }}" min="1" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Featured Image URL</label>
                    <input type="url" name="featured_image" class="form-control" value="{{ old('featured_image', $package->featured_image ?? $package->image_url) }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Inclusions (One per line)</label>
                    <textarea name="inclusions" class="form-control" rows="4" style="border-radius: 4px; font-size: 13px;">{{ old('inclusions', is_array($package->inclusions ?? $package->includes) ? implode("\n", $package->inclusions ?? $package->includes) : ($package->inclusions ?? $package->includes)) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Tour Highlights (One per line)</label>
                    <textarea name="highlights" class="form-control" rows="4" style="border-radius: 4px; font-size: 13px;">{{ old('highlights', is_array($package->highlights) ? implode("\n", $package->highlights) : ($package->highlights ?? $package->description)) }}</textarea>
                </div>

                <div class="col-12 pt-3 border-top mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('vendor.packages.index') }}" class="btn btn-light border fw-bold text-secondary px-3" style="border-radius: 4px; font-size: 13px; height: 38px;">Cancel</a>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; height: 38px; border: none;">
                        <i class="fa-solid fa-save me-1"></i> Update Package
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
