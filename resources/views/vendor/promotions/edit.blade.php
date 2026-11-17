@extends('layouts.vendor')
@section('title', 'Edit Promo Code | Vendor Partner Portal')

@section('content')
<div class="page-header-card">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="page-title m-0">
            <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Promo Code
        </h1>
        <a href="{{ route('vendor.promotions.index') }}" class="btn btn-light text-secondary border fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius: 4px; font-size: 13px; height: 36px; padding: 0 16px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Promotions
        </a>
    </div>
    <div class="page-breadcrumb mt-2">
        <a href="{{ route('vendor.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="sep">-</span>
        <a href="{{ route('vendor.promotions.index') }}">Promotions</a>
        <span class="sep">-</span><strong style="color:#333;">Edit {{ $coupon->code }}</strong>
    </div>
</div>

<div class="page-content-area">
    <div class="card p-4 bg-white" style="max-width: 800px; border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('vendor.promotions.update', $coupon->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger py-2" style="font-size:13px;">{{ $errors->first() }}</div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code) }}" class="form-control text-uppercase fw-bold" placeholder="e.g. EID2026, COXBEACH" required style="letter-spacing: 1px; border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Discount Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required style="border-radius: 4px; font-size: 13px; height: 38px;">
                        <option value="percent" @selected(in_array(old('type', $coupon->type), ['percent', 'percentage']))>Percentage Off (%)</option>
                        <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed Amount Off (BDT ৳)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Discount Value <span class="text-danger">*</span></label>
                    <input type="number" name="value" value="{{ old('value', (float) $coupon->amount) }}" class="form-control" placeholder="e.g. 15 for 15% or 1000 for BDT 1000" required step="0.01" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Minimum Booking Amount (BDT ৳)</label>
                    <input type="number" name="min_spend" class="form-control" placeholder="e.g. 3000" value="{{ old('min_spend', (float) $coupon->min_spend) }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Expiration Date <span class="text-danger">*</span></label>
                    <input type="date" name="expires_at" class="form-control" required value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d')) }}" style="border-radius: 4px; font-size: 13px; height: 38px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Apply to Specific Property (Optional)</label>
                    <select name="property_id" class="form-select" style="border-radius: 4px; font-size: 13px; height: 38px;">
                        <option value="">All My Properties &amp; Resorts</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}" @selected((int) old('property_id', $coupon->property_id) === (int) $prop->id)>{{ $prop->name }} ({{ $prop->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 pt-3 border-top mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('vendor.promotions.index') }}" class="btn btn-light border fw-bold text-secondary px-3" style="border-radius: 4px; font-size: 13px; height: 38px;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4" style="background-color: var(--primary); border-radius: 4px; font-size: 13px; height: 38px; border: none;">
                        <i class="fa-solid fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
