<?php

use App\Http\Controllers\Api\V1\Property\PropertyController as ApiPropertyController;
use App\Http\Controllers\Api\V1\Search\SearchController as ApiSearchController;
use App\Http\Controllers\Api\V1\Search\SuggestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Properties & Hotel Search REST API v1 Routes
| Endpoint: /api/v1/properties /api/v1/search ...
|--------------------------------------------------------------------------
*/

Route::get('/properties',          [ApiPropertyController::class, 'index'])->name('api.v1.properties');
Route::get('/properties/{id}',     [ApiPropertyController::class, 'show'])->name('api.v1.properties.show');

Route::middleware('throttle:search')->group(function () {
    Route::get('/search',                  [ApiSearchController::class, 'search'])->name('api.v1.search');
    Route::get('/search/filter-metadata',  [ApiSearchController::class, 'filterMetadata'])->name('api.v1.search.filters');
    Route::get('/suggestions',             [SuggestionController::class, 'index'])->name('api.v1.suggestions');
});

Route::get('/search/suggestions',          [SuggestionController::class, 'suggestions']);

