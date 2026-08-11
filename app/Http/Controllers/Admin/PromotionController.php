<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Admin Promotion Controller
 * ──────────────────────────
 * Full CRUD for homepage banners/promotions.
 * Supports: Accommodation, Flights, Activities, Destination, General types.
 */
class PromotionController extends Controller
{
    // ─── List ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Promotion::with('vendor:id,name')->latest();

        if ($type = $request->type) {
            $query->where('type', $type);
        }
        if ($request->vendor_only) {
            $query->whereNotNull('vendor_id');
        }
        if ($request->has('status')) {
            $query->where('is_active', (bool)$request->status);
        }

        $promotions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'          => Promotion::count(),
            'active'         => Promotion::where('is_active', true)->count(),
            'accommodation'  => Promotion::where('type', 'accommodation')->count(),
            'flights'        => Promotion::where('type', 'flights')->count(),
            'activities'     => Promotion::where('type', 'activities')->count(),
            'vendor_promos'  => Promotion::whereNotNull('vendor_id')->count(),
        ];

        return view('admin.promotions.index', compact('promotions', 'stats'));
    }

    // ─── Create Form ──────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.promotions.create');
    }

    // ─── Store ────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:100',
            'subtitle'     => 'nullable|string|max:150',
            'badge_text'   => 'nullable|string|max:50',
            'cta_text'     => 'nullable|string|max:60',
            'cta_link'     => 'nullable|string|max:300',
            'image_url'    => 'nullable|url|max:500',
            'icon'         => 'nullable|string|max:10',
            'bg_color'     => 'required|string|max:20',
            'bg_color_end' => 'nullable|string|max:20',
            'text_color'   => 'required|string|max:20',
            'badge_bg'     => 'required|string|max:20',
            'type'         => 'required|in:accommodation,flights,activities,destination,general',
            'target_type'  => 'nullable|string|max:50',
            'target_city'  => 'nullable|string|max:80',
            'is_active'    => 'boolean',
            'is_featured'  => 'boolean',
            'sort_order'   => 'integer|min:0',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);

        $promotion = Promotion::create($validated);

        $this->clearPromotionCache();
        $this->log('created', $promotion);

        return redirect()->route('admin.promotions.index')
            ->with('success', "Promotion \"{$promotion->title}\" created successfully.");
    }

    // ─── Edit Form ────────────────────────────────────────────────────────

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    // ─── Update ───────────────────────────────────────────────────────────

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:100',
            'subtitle'     => 'nullable|string|max:150',
            'badge_text'   => 'nullable|string|max:50',
            'cta_text'     => 'nullable|string|max:60',
            'cta_link'     => 'nullable|string|max:300',
            'image_url'    => 'nullable|url|max:500',
            'icon'         => 'nullable|string|max:10',
            'bg_color'     => 'required|string|max:20',
            'bg_color_end' => 'nullable|string|max:20',
            'text_color'   => 'required|string|max:20',
            'badge_bg'     => 'required|string|max:20',
            'type'         => 'required|in:accommodation,flights,activities,destination,general',
            'target_type'  => 'nullable|string|max:50',
            'target_city'  => 'nullable|string|max:80',
            'is_active'    => 'boolean',
            'is_featured'  => 'boolean',
            'sort_order'   => 'integer|min:0',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);

        $promotion->update($validated);

        $this->clearPromotionCache();
        $this->log('updated', $promotion);

        return back()->with('success', "Promotion \"{$promotion->title}\" updated.");
    }

    // ─── Toggle Active ────────────────────────────────────────────────────

    public function toggleActive(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        $this->clearPromotionCache();
        $status = $promotion->fresh()->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Promotion \"{$promotion->title}\" {$status}.");
    }

    // ─── Reorder (AJAX) ───────────────────────────────────────────────────

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $index => $id) {
            Promotion::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->clearPromotionCache();

        return response()->json(['success' => true, 'message' => 'Order saved.']);
    }

    // ─── Delete ───────────────────────────────────────────────────────────

    public function destroy(Promotion $promotion)
    {
        $title = $promotion->title;
        $this->log('deleted', $promotion);
        $promotion->delete();
        $this->clearPromotionCache();
        return back()->with('success', "Promotion \"{$title}\" deleted.");
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    private function clearPromotionCache(): void
    {
        foreach (['accommodation','flights','activities','destination','general'] as $type) {
            Cache::forget("promotions:{$type}");
        }
        Cache::forget('promotions:all');
    }

    private function log(string $action, Promotion $promo): void
    {
        try {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'user_name'   => auth()->user()?->name ?? 'Admin',
                'action'      => $action,
                'model_type'  => 'Promotion',
                'model_id'    => $promo->id,
                'description' => ucfirst($action) . " promotion: \"{$promo->title}\" [{$promo->type}]",
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Exception $e) {}
    }
}
