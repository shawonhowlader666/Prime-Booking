@extends('layouts.main', ['activePage' => 'vendor'])

@section('title', 'Create Promo Code | Vendor Partner')

@section('content')
<div class="py-4" style="background-color: #f8fafc; min-height: 85vh;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 15px;">
        
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size: 22px;">
                    <i class="fa-solid fa-plus-circle text-primary me-2"></i> {{ __('Create Promo Code / Special Offer') }}
                </h3>
                <p class="text-secondary small mb-0">Publish custom discount coupons for guests booking your hotel or resort stays.</p>
            </div>
            <a href="{{ route('vendor.promotions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                ← Back to Promotions
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <form action="{{ route('vendor.promotions.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase fw-bold rounded-3" placeholder="e.g. EID2026, COXBEACH" required style="letter-spacing: 1px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Discount Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="percent">Percentage Off (%)</option>
                            <option value="fixed">Fixed Amount Off (BDT ৳)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" name="value" class="form-control rounded-3" placeholder="e.g. 15 for 15% or 1000 for BDT 1000" required step="0.01">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Minimum Booking Amount (BDT ৳)</label>
                        <input type="number" name="min_spend" class="form-control rounded-3" placeholder="e.g. 3000" value="0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Expiration Date <span class="text-danger">*</span></label>
                        <input type="date" name="expires_at" class="form-control rounded-3" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Apply to Specific Property (Optional)</label>
                        <select name="property_id" class="form-select rounded-3">
                            <option value="">All My Properties & Resorts</option>
                            @foreach($properties as $prop)
                                <option value="{{ $prop->id }}">{{ $prop->name }} ({{ $prop->city }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 pt-3">
                        <button type="submit" class="btn text-white fw-bold px-5 py-2.5 rounded-pill shadow-sm" style="background-color: #2067e1;">
                            <i class="fa-solid fa-check me-1"></i> PUBLISH PROMO CODE NOW
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
