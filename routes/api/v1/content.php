<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dynamic Content, Promos, Packages, Deals & Coupons REST API v1 Routes
| Endpoint: /api/v1/promotions, /api/v1/packages, /api/v1/deals, /api/v1/coupons/validate ...
|--------------------------------------------------------------------------
*/

Route::get('/promotions', function () {
    return response()->json(['success' => true, 'data' => \App\Models\Promotion::active()->ordered()->get()]);
});

Route::get('/destinations', function () {
    return response()->json(['success' => true, 'data' => \App\Models\FeaturedDestination::active()->get()]);
});

Route::get('/packages', function () {
    return response()->json(['success' => true, 'data' => \App\Models\TourPackage::active()->ordered()->get()]);
});

Route::get('/deals', function () {
    return response()->json(['success' => true, 'data' => \App\Models\Deal::active()->ordered()->get()]);
});

Route::get('/transfers', function () {
    return response()->json(['success' => true, 'data' => \App\Models\AirportTransfer::active()->get()]);
});

// Coupon Validation REST API for Checkout
Route::post('/coupons/validate', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'code'   => 'required|string',
        'amount' => 'nullable|numeric|min:0',
    ]);

    $code   = strtoupper(trim($request->code));
    $coupon = \App\Models\Coupon::where('code', $code)->where('status', 'active')->first();

    if (!$coupon) {
        return response()->json(['success' => false, 'message' => 'Invalid or inactive promo coupon code.'], 404);
    }

    if ($coupon->expires_at && \Carbon\Carbon::parse($coupon->expires_at)->isPast()) {
        return response()->json(['success' => false, 'message' => 'This promo coupon code has expired.'], 400);
    }

    if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
        return response()->json(['success' => false, 'message' => 'This promo coupon has reached its usage limit.'], 400);
    }

    $spend = $request->amount ?? 0;
    if ($coupon->min_spend && $spend < $coupon->min_spend) {
        return response()->json([
            'success' => false,
            'message' => 'Minimum booking amount of BDT ' . number_format($coupon->min_spend) . ' required to use this coupon.'
        ], 400);
    }

    $discount = 0;
    if ($coupon->type === 'percentage') {
        $discount = ($spend * $coupon->amount) / 100;
    } else {
        $discount = $coupon->amount;
    }

    return response()->json([
        'success' => true,
        'message' => 'Coupon code applied successfully!',
        'data'    => [
            'code'                => $coupon->code,
            'type'                => $coupon->type,
            'amount'              => (float)$coupon->amount,
            'discount_calculated' => (float)round($discount, 2),
        ]
    ]);
});
