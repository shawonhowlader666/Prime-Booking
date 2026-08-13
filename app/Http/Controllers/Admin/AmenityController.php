<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        // Seed default hotel amenities if empty
        if (Amenity::count() === 0) {
            $defaultAmenities = [
                ['name' => 'High-Speed Wi-Fi', 'icon' => 'fa-wifi', 'category' => 'general'],
                ['name' => 'Swimming Pool & Resort Deck', 'icon' => 'fa-swimming-pool', 'category' => 'recreation'],
                ['name' => 'Spa & Wellness Massage', 'icon' => 'fa-spa', 'category' => 'recreation'],
                ['name' => 'Fine Dining Restaurant', 'icon' => 'fa-utensils', 'category' => 'dining'],
                ['name' => '24/7 Room Service', 'icon' => 'fa-bell', 'category' => 'services'],
                ['name' => 'Airport Pickup & Shuttle', 'icon' => 'fa-shuttle-van', 'category' => 'services'],
                ['name' => 'Air Conditioning', 'icon' => 'fa-snowflake', 'category' => 'general'],
                ['name' => 'Private Beach Access', 'icon' => 'fa-umbrella-beach', 'category' => 'recreation'],
            ];

            foreach ($defaultAmenities as $a) {
                Amenity::create($a);
            }
        }

        $amenities = Amenity::orderBy('category')->orderBy('name')->get();
        return view('admin.amenities.index', compact('amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'icon'     => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
        ]);

        $validated['category'] = $validated['category'] ?? 'general';

        Amenity::create($validated);
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity added successfully!');
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'icon'     => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
        ]);

        $validated['category'] = $validated['category'] ?? 'general';

        $amenity->update($validated);
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated successfully!');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity removed!');
    }
}
