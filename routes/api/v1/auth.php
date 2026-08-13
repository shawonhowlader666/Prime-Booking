<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication REST API v1 Routes
| Endpoint: /api/v1/auth/...
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',             [AuthController::class, 'me']);
        Route::put('/profile',        [AuthController::class, 'updateProfile']);
        Route::post('/logout',        [AuthController::class, 'logout']);
    });
});
