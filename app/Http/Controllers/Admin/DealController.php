<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use Illuminate\Support\Facades\Cache;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::ordered()->paginate(15);
        return view('admin.deals.index', compact('deals'));
    }

    public function create()
    {
        return view('admin.deals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'discount_pct'   => 'nullable|numeric|min:0|max:100',
            'original_price' => 'nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'valid_until'    => 'nullable|date',
            'image_url'      => 'nullable|url|max:500',
            'badge_text'     => 'nullable|string|max:50',
            'link_url'       => 'nullable|string|max:300',
            'type'           => 'required|string|in:hotel,flight,package,activity',
            'is_active'      => 'nullable|boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Deal::create($validated);
        Cache::forget('deals_active');

        return redirect()->route('admin.deals.index')->with('success', 'Deal created successfully!');
    }

    public function edit(Deal $deal)
    {
        return view('admin.deals.edit', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'discount_pct'   => 'nullable|numeric|min:0|max:100',
            'original_price' => 'nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'valid_until'    => 'nullable|date',
            'image_url'      => 'nullable|url|max:500',
            'badge_text'     => 'nullable|string|max:50',
            'link_url'       => 'nullable|string|max:300',
            'type'           => 'required|string|in:hotel,flight,package,activity',
            'is_active'      => 'nullable|boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $deal->update($validated);
        Cache::forget('deals_active');

        return redirect()->route('admin.deals.index')->with('success', 'Deal updated successfully!');
    }

    public function toggleStatus($id)
    {
        $deal = Deal::findOrFail($id);
        $deal->update(['is_active' => !$deal->is_active]);
        Cache::forget('deals_active');
        return back()->with('success', 'Deal status updated!');
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();
        Cache::forget('deals_active');
        return redirect()->route('admin.deals.index')->with('success', 'Deal deleted!');
    }
}
