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
        $destinations = FeaturedDestination::orderBy('sort_order')->paginate(20);
        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('admin.destinations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city'                    => 'required|string|max:80',
            'country'                 => 'required|string|max:50',
            'image_url'               => 'required|url|max:500',
            'description'             => 'nullable|string|max:200',
            'property_count_override' => 'nullable|integer|min:0',
            'min_price_override'      => 'nullable|numeric|min:0',
            'is_active'               => 'boolean',
            'is_featured'             => 'boolean',
            'sort_order'              => 'integer|min:0',
        ]);

        $dest = FeaturedDestination::create($validated);

        Cache::forget('featured_destinations');
        $this->log('created', $dest);

        return redirect()->route('admin.destinations.index')
            ->with('success', "\"{$dest->city}\" destination added successfully.");
    }

    public function edit(FeaturedDestination $destination)
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(Request $request, FeaturedDestination $destination)
    {
        $validated = $request->validate([
            'city'                    => 'required|string|max:80',
            'country'                 => 'required|string|max:50',
            'image_url'               => 'required|url|max:500',
            'description'             => 'nullable|string|max:200',
            'property_count_override' => 'nullable|integer|min:0',
            'min_price_override'      => 'nullable|numeric|min:0',
            'is_active'               => 'boolean',
            'is_featured'             => 'boolean',
            'sort_order'              => 'integer|min:0',
        ]);

        $destination->update($validated);

        Cache::forget('featured_destinations');
        $this->log('updated', $destination);

        return back()->with('success', "\"{$destination->city}\" updated.");
    }

    public function destroy(FeaturedDestination $destination)
    {
        $city = $destination->city;
        $this->log('deleted', $destination);
        $destination->delete();
        Cache::forget('featured_destinations');
        return back()->with('success', "\"{$city}\" destination removed.");
    }

    public function reorder(Request $request)
    {
        foreach ($request->ids as $index => $id) {
            FeaturedDestination::where('id', $id)->update(['sort_order' => $index]);
        }
        Cache::forget('featured_destinations');
        return response()->json(['success' => true]);
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
