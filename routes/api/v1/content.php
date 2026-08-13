<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dynamic Content, Promos, Packages & Transfers REST API v1 Routes
| Endpoint: /api/v1/promotions /api/v1/packages ...
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
