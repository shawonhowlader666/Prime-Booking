@extends('layouts.vendor')
@section('title', 'Create Tour Package | Vendor Partner Portal')

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-plus-circle text-primary me-2"></i> Create New Tour Package
        </h1>
        <a href="{{ route('vendor.packages.index') }}" class="btn btn-light text-secondary border fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius: 4px; font-size: 13px; height: 36px; padding: 0 16px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Packages
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span>
        <a href="{{ route('vendor.packages.index') }}">Tour Packages</a>
        <span class="sep">-</span><strong style="color:#333;">Create Package</strong>
    </div>
</div>

<div class="page-content-area">
    <div class="card p-4 bg-white" style="max-width: 900px; border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('vendor.packages.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Package Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Cox's Bazar 3D2N Luxury Beach Resort Package" required value="{{ old('title') }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Destination City/Region <span class="text-danger">*</span></label>
                    <input type="text" name="destination" class="form-control" placeholder="e.g. Cox's Bazar, Sylhet, Sundarbans, Sajek" required value="{{ old('destination') }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Duration Days <span class="text-danger">*</span></label>
                    <input type="number" name="duration_days" class="form-control" value="3" min="1" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Duration Nights <span class="text-danger">*</span></label>
                    <input type="number" name="duration_nights" class="form-control" value="2" min="0" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Price Per Person (BDT ৳) <span class="text-danger">*</span></label>
                    <input type="number" name="price_per_person" class="form-control" placeholder="e.g. 7500" required step="0.01" value="{{ old('price_per_person') }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Regular Price (Optional for Discount)</label>
                    <input type="number" name="discount_price" class="form-control" placeholder="e.g. 9500" step="0.01" value="{{ old('discount_price') }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Featured Cover Image URL <span class="text-danger">*</span></label>
                    <input type="url" name="featured_image" class="form-control" placeholder="https://images.unsplash.com/photo-..." required value="{{ old('featured_image', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80') }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">What's Included (1 per line)</label>
                    <textarea name="inclusions" class="form-control" rows="4" placeholder="5-Star Hotel Stay&#10;AC Bus Transport&#10;Daily Breakfast&#10;Tour Guide" style="border-radius: 4px; font-size: 13px;">{{ old('inclusions') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Tour Highlights (1 per line)</label>
                    <textarea name="highlights" class="form-control" rows="4" placeholder="120km longest sea beach walk&#10;Inani Coral Beach Sunset&#10;Himchari Waterfall Tour" style="border-radius: 4px; font-size: 13px;">{{ old('highlights') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Maximum Seats / Group Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="max_seats" class="form-control" value="20" min="1" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-12 pt-3 border-top mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('vendor.packages.index') }}" class="btn btn-light border fw-bold text-secondary px-3" style="border-radius: 4px; font-size: 13px; height: 38px;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; height: 38px; border: none;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Publish Tour Package Now
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
