<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
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

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();
        return redirect()->route('admin.amenities.index')->with('success', 'Amenity removed!');
    }
}
