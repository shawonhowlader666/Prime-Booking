@extends('layouts.main', ['activePage' => 'vendor'])

@section('title', 'Create Tour Package | Vendor Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 22px;">
                    <i class="fa-solid fa-plus-circle text-primary me-2"></i> {{ __('Create New Tour Package') }}
                </h3>
                <p class="text-secondary small mb-0">Fill in the package details to publish your holiday trip on Prime Booking.</p>
            </div>
            <a href="{{ route('vendor.packages.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                ← Back to Packages
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <form action="{{ route('vendor.packages.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Package Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Cox's Bazar 3D2N Luxury Beach Resort Package" required value="{{ old('title') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Destination City/Region <span class="text-danger">*</span></label>
                        <input type="text" name="destination" class="form-control rounded-3" placeholder="e.g. Cox's Bazar, Sylhet, Sundarbans, Sajek" required value="{{ old('destination') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Duration Days <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" class="form-control rounded-3" value="3" min="1" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Duration Nights <span class="text-danger">*</span></label>
                        <input type="number" name="duration_nights" class="form-control rounded-3" value="2" min="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Price Per Person (BDT ৳) <span class="text-danger">*</span></label>
                        <input type="number" name="price_per_person" class="form-control rounded-3" placeholder="e.g. 7500" required step="0.01" value="{{ old('price_per_person') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Regular Price (Optional for Discount Badge)</label>
                        <input type="number" name="discount_price" class="form-control rounded-3" placeholder="e.g. 9500" step="0.01" value="{{ old('discount_price') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Featured Cover Image URL <span class="text-danger">*</span></label>
                        <input type="url" name="featured_image" class="form-control rounded-3" placeholder="https://images.unsplash.com/photo-..." required value="{{ old('featured_image', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">What's Included (1 per line)</label>
                        <textarea name="inclusions" class="form-control rounded-3" rows="4" placeholder="5-Star Hotel Stay&#10;AC Bus Transport&#10;Daily Breakfast&#10;Tour Guide">{{ old('inclusions') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Tour Highlights (1 per line)</label>
                        <textarea name="highlights" class="form-control rounded-3" rows="4" placeholder="120km longest sea beach walk&#10;Inani Coral Beach Sunset&#10;Himchari Waterfall Tour">{{ old('highlights') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Maximum Seats / Group Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="max_seats" class="form-control rounded-3" value="20" min="1" required>
                    </div>

                    <div class="col-12 pt-3">
                        <button type="submit" class="btn text-white fw-bold px-5 py-2.5 rounded-pill shadow-sm" style="background-color: #2067e1;">
                            <i class="fa-solid fa-paper-plane me-1"></i> PUBLISH TOUR PACKAGE NOW
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
