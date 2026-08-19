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
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Package Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $package->title) }}" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Duration / Days <span class="text-danger">*</span></label>
                    <input type="text" name="days" class="form-control" value="{{ old('days', $package->days ?? $package->duration_days) }}" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Price per Person (৳) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $package->price ?? $package->price_per_person) }}" step="0.01" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Badge Text</label>
                    <input type="text" name="badge" class="form-control" value="{{ old('badge', $package->badge) }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Image URL</label>
                    <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $package->image_url ?? $package->featured_image) }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Inclusions (One per line)</label>
                    <textarea name="includes" class="form-control" rows="3" style="border-radius: 4px; font-size: 13px;">{{ old('includes', is_array($package->includes ?? $package->inclusions) ? implode("\n", $package->includes ?? $package->inclusions) : ($package->includes ?? $package->inclusions)) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Description / Highlights</label>
                    <textarea name="description" class="form-control" rows="3" style="border-radius: 4px; font-size: 13px;">{{ old('description', $package->description) }}</textarea>
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
