<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
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

        $serviceType = $validated['service_type'] ?? $validated['subject'] ?? 'General Support';

        Inquiry::create([
            'name'         => $validated['name'],
            'phone'        => $validated['phone'],
            'email'        => $validated['email'] ?? null,
            'service_type' => $serviceType,
            'destination'  => $validated['destination'] ?? null,
            'travel_date'  => $validated['travel_date'] ?? null,
            'passengers'   => $validated['passengers'] ?? 1,
            'message'      => $validated['message'] ?? null,
            'status'       => 'pending',
        ]);

        return redirect()->back()->with('success', 'Thank you! Your message has been received by PRIME BOOKING. Our support team will contact you shortly at ' . $validated['phone'] . '.');
    }
}


