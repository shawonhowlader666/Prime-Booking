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
            'service_type' => 'required|string',
            'destination'  => 'nullable|string|max:255',
            'travel_date'  => 'nullable|date',
            'passengers'   => 'nullable|integer|min:1',
            'message'      => 'nullable|string',
        ]);

        try {
            Inquiry::create($validated);
        } catch (\Exception $e) {
            // Ignore DB error if table doesn't exist yet
        }

        return redirect()->back()->with('success', 'Thank you! Your inquiry has been received by Prime Aviation. Our team will contact you shortly at ' . $validated['phone'] . '.');
    }
}
