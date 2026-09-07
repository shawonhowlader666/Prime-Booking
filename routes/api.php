<?php

use App\Http\Controllers\Api\V1\AmenityApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FolioController;
use App\Http\Controllers\Api\V1\GuestController;
use App\Http\Controllers\Api\V1\HousekeepingController;
use App\Http\Controllers\Api\V1\HrmsApiController;
use App\Http\Controllers\Api\V1\InventoryApiController;
use App\Http\Controllers\Api\V1\ReservationController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Prime Cloud PMS RESTful API v1 (Mobile & Microservice Ready)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public System Health & Telemetry Probes
    Route::get('/health', \App\Http\Controllers\Api\V1\HealthCheckController::class);

    // Public Auth Endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Inbound Webhook & Remote Control from Prime Booking OTA engine (Protected by HMAC signature)
    Route::post('/sync/prime-booking/webhook', [SyncController::class, 'handleIncomingBooking']);
    Route::post('/sync/prime-booking/subscription-control', [SyncController::class, 'subscriptionControl']);
    Route::get('/sync/prime-booking/telemetry', [SyncController::class, 'telemetry']);
    Route::post('/sync/prime-booking/provision-tenant', [SyncController::class, 'provisionTenant']);
    Route::delete('/sync/prime-booking/tenants/{code}', [SyncController::class, 'deleteTenant']);

    // Authenticated PMS Endpoints (Supports Sanctum Bearer Token and Web Session)
    Route::middleware('auth:sanctum,web')->group(function () {

        // Auth & Identity
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Dashboard & Analytics
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // Rooms & Inventory Units
        Route::get('/rooms', [RoomController::class, 'index']);
        Route::get('/rooms/timeline-matrix', \App\Http\Controllers\Api\V1\TimelineMatrixController::class);
        Route::get('/room-types', [RoomController::class, 'roomTypes']);
        Route::patch('/rooms/{id}/status', [RoomController::class, 'updateStatus']);

        // Reservations & Front Desk
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations/matrix', [ReservationController::class, 'matrix']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);
        Route::post('/reservations/{id}/check-in', [ReservationController::class, 'checkIn']);
        Route::post('/reservations/{id}/check-out', [ReservationController::class, 'checkOut']);

        // Folio & Billing / POS
        Route::get('/folios/{id}', [FolioController::class, 'show']);
        Route::post('/folios/{id}/items', [FolioController::class, 'addItem']);
        Route::post('/folios/{id}/payments', [FolioController::class, 'recordPayment']);
        Route::post('/pos/charge-room', [\App\Http\Controllers\Api\V1\PosBillingController::class, 'chargeRoom']);

        // Dynamic Pricing & Revenue Management
        Route::get('/pricing/dynamic-rate', function (\Illuminate\Http\Request $request, \App\Services\DynamicPricingService $pricingService) {
            $request->validate(['room_type_id' => 'required|exists:room_types,id']);
            $roomType = \App\Models\RoomType::findOrFail($request->input('room_type_id'));
            $checkIn = $request->input('check_in_date', now()->format('Y-m-d'));
            $nights = (int) $request->input('nights', 1);
            return response()->json($pricingService->calculateDynamicRate($roomType, $checkIn, $nights));
        });

        // Guests / CRM
        Route::get('/guests', [GuestController::class, 'index']);
        Route::get('/guests/{id}', [GuestController::class, 'show']);

        // Housekeeping
        Route::get('/housekeeping/tasks', [HousekeepingController::class, 'tasks']);
        Route::post('/housekeeping/tasks/{id}/assign', [HousekeepingController::class, 'assignTask']);
        Route::post('/housekeeping/tasks/{id}/complete', [HousekeepingController::class, 'completeTask']);

        // HRMS, Attendance & Staff
        Route::get('/hrms/employees', [HrmsApiController::class, 'employees']);
        Route::get('/hrms/departments', [HrmsApiController::class, 'departments']);
        Route::post('/hrms/attendance/clock-in', [HrmsApiController::class, 'clockIn']);
        Route::post('/hrms/attendance/clock-out', [HrmsApiController::class, 'clockOut']);
        Route::get('/hrms/leaves', [HrmsApiController::class, 'leaves']);
        Route::post('/hrms/leaves', [HrmsApiController::class, 'applyLeave']);

        // Inventory, Stock & Procurement
        Route::get('/inventory/items', [InventoryApiController::class, 'items']);
        Route::post('/inventory/purchase', [InventoryApiController::class, 'recordPurchase']);
        Route::post('/inventory/damage', [InventoryApiController::class, 'recordDamage']);
        Route::get('/inventory/valuation', [InventoryApiController::class, 'valuation']);

        // Personalised Amenities & Extra Services
        Route::get('/amenities/services', [AmenityApiController::class, 'services']);
        Route::post('/amenities/book', [AmenityApiController::class, 'book']);

        // Cab & Fleet Transport Facility
        Route::get('/cabs', [\App\Http\Controllers\Api\V1\CabApiController::class, 'cabs']);
        Route::get('/cabs/bookings', [\App\Http\Controllers\Api\V1\CabApiController::class, 'bookings']);
        Route::post('/cabs/book', [\App\Http\Controllers\Api\V1\CabApiController::class, 'book']);

        // Prime Booking Sync Engine
        Route::post('/sync/prime-booking/push-inventory', [SyncController::class, 'pushInventory']);
    });
});
