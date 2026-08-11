<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Property;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        if (!$userId) {
            return redirect()->route('login')->with('info', 'Please sign in to view your saved wishlist.');
        }

        $wishlists = Wishlist::where('user_id', $userId)
            ->with(['property' => function($q) {
                $q->select(['id','name','slug','type','city','address','star_rating','rating_score','total_reviews','price_per_night','original_price','primary_image','status']);
            }])
            ->latest()
            ->paginate(12);

        return view('pages.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please sign in to add properties to your wishlist.', 'redirect' => route('login')], 401);
            }
            return redirect()->route('login')->with('info', 'Please sign in to manage wishlist.');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $propertyId = $validated['property_id'];

        $existing = Wishlist::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isWishlisted = false;
            $msg = 'Property removed from your wishlist.';
        } else {
            Wishlist::create([
                'user_id'     => $userId,
                'property_id' => $propertyId,
            ]);
            $isWishlisted = true;
            $msg = 'Property saved to your wishlist! ❤️';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'       => true,
                'is_wishlisted' => $isWishlisted,
                'message'       => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }
}
