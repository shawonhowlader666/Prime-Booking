<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Guest Inquiries & Support Messages REST API v1 Routes
| Endpoint: /api/v1/inquiries ...
|--------------------------------------------------------------------------
*/

Route::post('/inquiries', function (Request $request) {
    $validated = $request->validate([
        'name'         => 'required|string|max:255',
        'phone'        => 'required|string|max:50',
        'email'        => 'nullable|email|max:255',
        'service_type' => 'nullable|string',
        'subject'      => 'nullable|string',
        'destination'  => 'nullable|string|max:255',
        'travel_date'  => 'nullable|string',
        'passengers'   => 'nullable|integer|min:1',
        'message'      => 'nullable|string',
    ]);

    $inq = \App\Models\Inquiry::create([
        'name'         => $validated['name'],
        'phone'        => $validated['phone'],
        'email'        => $validated['email'] ?? null,
        'service_type' => $validated['service_type'] ?? $validated['subject'] ?? 'General Support',
        'destination'  => $validated['destination'] ?? null,
        'travel_date'  => $validated['travel_date'] ?? null,
        'passengers'   => $validated['passengers'] ?? 1,
        'message'      => $validated['message'] ?? null,
        'status'       => 'pending',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Inquiry submitted successfully!',
        'data'    => $inq,
    ], 201);
});
