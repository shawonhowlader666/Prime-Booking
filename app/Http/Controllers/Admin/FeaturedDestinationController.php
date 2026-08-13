<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedDestination;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeaturedDestinationController extends Controller
{
    public function index()
    {
        // Seed default destinations if empty
        if (FeaturedDestination::count() === 0) {
            $defaults = [
                ['city' => 'Cox\'s Bazar', 'country' => 'Bangladesh', 'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800', 'description' => 'World\'s longest natural sea beach with 5-star luxury resorts.', 'is_active' => true, 'is_featured' => true, 'sort_order' => 1],
                ['city' => 'Sajek Valley', 'country' => 'Bangladesh', 'image_url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=800', 'description' => 'Experience serene cloud valley heights and mountain eco-resorts.', 'is_active' => true, 'is_featured' => true, 'sort_order' => 2],
                ['city' => 'Sylhet & Sreemangal', 'country' => 'Bangladesh', 'image_url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800', 'description' => 'Lush green tea gardens, Jaflong rivers, and luxury boutique resorts.', 'is_active' => true, 'is_featured' => true, 'sort_order' => 3],
                ['city' => 'Sundarbans', 'country' => 'Bangladesh', 'image_url' => 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=800', 'description' => 'UNESCO World Heritage mangrove forest & luxury ship cruises.', 'is_active' => true, 'is_featured' => true, 'sort_order' => 4],
                ['city' => 'Kuakata', 'country' => 'Bangladesh', 'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800', 'description' => 'Daughter of the Sea — famous for sunrise and sunset beach views.', 'is_active' => true, 'is_featured' => true, 'sort_order' => 5],
            ];
            foreach ($defaults as $d) {
                FeaturedDestination::create($d);
            }
        }

        $destinations = FeaturedDestination::orderBy('sort_order')->get();
        return view('admin.destinations.index', compact('destinations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city'                    => 'required|string|max:80',
            'country'                 => 'required|string|max:50',
            'image_url'               => 'nullable|string|max:500',
            'image_file'              => 'nullable|image|max:5120',
            'description'             => 'nullable|string|max:250',
            'property_count_override' => 'nullable|integer|min:0',
            'min_price_override'      => 'nullable|numeric|min:0',
            'is_active'               => 'nullable|boolean',
            'is_featured'             => 'nullable|boolean',
            'sort_order'              => 'nullable|integer|min:0',
        ]);

        $imageUrl = $validated['image_url'] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800';

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $path = $request->file('image_file')->store('uploads/destinations', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $dest = FeaturedDestination::create([
            'city'                    => $validated['city'],
            'country'                 => $validated['country'] ?? 'Bangladesh',
            'image_url'               => $imageUrl,
            'description'             => $validated['description'] ?? '',
            'property_count_override' => $validated['property_count_override'] ?? null,
            'min_price_override'      => $validated['min_price_override'] ?? null,
            'is_active'               => $request->has('is_active') ? true : false,
            'is_featured'             => $request->has('is_featured') ? true : false,
            'sort_order'              => $validated['sort_order'] ?? (FeaturedDestination::max('sort_order') + 1),
        ]);

        Cache::forget('featured_destinations');
        $this->log('created', $dest);

        return redirect()->route('destinations.index')
            ->with('success', "\"{$dest->city}\" destination banner added successfully.");
    }

    public function update(Request $request, $id)
    {
        $dest = FeaturedDestination::findOrFail($id);

        $validated = $request->validate([
            'city'                    => 'required|string|max:80',
            'country'                 => 'required|string|max:50',
            'image_url'               => 'nullable|string|max:500',
            'image_file'              => 'nullable|image|max:5120',
            'description'             => 'nullable|string|max:250',
            'property_count_override' => 'nullable|integer|min:0',
            'min_price_override'      => 'nullable|numeric|min:0',
            'sort_order'              => 'nullable|integer|min:0',
        ]);

        $imageUrl = $validated['image_url'] ?: $dest->image_url;

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $path = $request->file('image_file')->store('uploads/destinations', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $dest->update([
            'city'                    => $validated['city'],
            'country'                 => $validated['country'] ?? 'Bangladesh',
            'image_url'               => $imageUrl,
            'description'             => $validated['description'] ?? $dest->description,
            'property_count_override' => $request->filled('property_count_override') ? $validated['property_count_override'] : null,
            'min_price_override'      => $request->filled('min_price_override') ? $validated['min_price_override'] : null,
            'is_active'               => $request->has('is_active'),
            'is_featured'             => $request->has('is_featured'),
            'sort_order'              => $validated['sort_order'] ?? $dest->sort_order,
        ]);

        Cache::forget('featured_destinations');
        $this->log('updated', $dest);

        return redirect()->route('destinations.index')->with('success', "\"{$dest->city}\" destination updated.");
    }

    public function destroy($id)
    {
        $dest = FeaturedDestination::findOrFail($id);
        $city = $dest->city;
        $this->log('deleted', $dest);
        $dest->delete();
        Cache::forget('featured_destinations');
        return redirect()->route('destinations.index')->with('success', "\"{$city}\" destination removed.");
    }

    private function log(string $action, FeaturedDestination $dest): void
    {
        try {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'user_name'   => auth()->user()?->name ?? 'Admin',
                'action'      => $action,
                'model_type'  => 'FeaturedDestination',
                'model_id'    => $dest->id,
                'description' => ucfirst($action) . " destination: {$dest->city}, {$dest->country}",
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Exception $e) {}
    }
}
