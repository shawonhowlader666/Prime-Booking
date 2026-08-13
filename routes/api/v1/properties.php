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

Route::get('/properties',          [ApiPropertyController::class, 'index']);
Route::get('/properties/{id}',     [ApiPropertyController::class, 'show']);

Route::middleware('throttle:search')->group(function () {
    Route::get('/search',          [ApiSearchController::class, 'search']);
    Route::get('/suggestions',     [SuggestionController::class, 'index']);
});

Route::get('/search/suggestions',  [SuggestionController::class, 'suggestions']);
