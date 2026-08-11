<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AdminDestinationController extends Controller
{
    /**
     * Display list of destinations with real property counts and banner controls.
     * GET /admin/destinations
     */
    public function index(): View
    {
        $destinations = Destination::withCount('properties')
            ->orderBy('sort_order', 'asc')
            ->paginate(15);

        return view('admin.destinations.index', compact('destinations'));
    }

    /**
     * Store a newly created destination banner with image/video.
     * POST /admin/destinations
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'tagline'    => 'nullable|string|max:255',
            'image_url'  => 'required|string|max:1000',
            'video_url'  => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer',
        ]);

        Destination::create([
            'name'       => trim($validated['name']),
            'slug'       => Str::slug($validated['name']),
            'tagline'    => $validated['tagline'] ?? null,
            'image_url'  => $validated['image_url'],
            'video_url'  => $validated['video_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination banner card created successfully!');
    }

    /**
     * Update an existing destination banner image or video.
     * PUT /admin/destinations/{id}
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $dest = Destination::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'tagline'    => 'nullable|string|max:255',
            'image_url'  => 'required|string|max:1000',
            'video_url'  => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $dest->update([
            'name'       => trim($validated['name']),
            'slug'       => Str::slug($validated['name']),
            'tagline'    => $validated['tagline'] ?? null,
            'image_url'  => $validated['image_url'],
            'video_url'  => $validated['video_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $request->has('is_active'),
        ]);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination banner updated successfully!');
    }

    /**
     * Delete a destination banner.
     * DELETE /admin/destinations/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $dest = Destination::findOrFail($id);
        $dest->delete();

        return redirect()->route('admin.destinations.index')->with('success', 'Destination banner deleted!');
    }
}
