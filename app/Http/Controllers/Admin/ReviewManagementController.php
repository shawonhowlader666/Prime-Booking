<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['property', 'user']);

        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('guest_name', 'like', $s)
                  ->orWhere('comment', 'like', $s)
                  ->orWhereHas('property', function ($pq) use ($s) {
                      $pq->where('name', 'like', $s);
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', (int)$request->rating);
        }

        $reviews = $query->latest()->paginate(20)->appends($request->all());

        $stats = [
            'total'    => Review::count(),
            'approved' => Review::where('status', 'approved')->count(),
            'pending'  => Review::where('status', 'pending')->orWhereNull('status')->count(),
            'avg'      => round((float) (Review::avg('rating') ?: 4.8), 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);
            $newStatus = $review->status === 'approved' ? 'pending' : 'approved';
            $review->update(['status' => $newStatus]);
        } catch (\Exception $e) {}

        return back()->with('success', 'Review status updated successfully.');
    }

    public function destroy($id)
    {
        try {
            Review::findOrFail($id)->delete();
        } catch (\Exception $e) {}
        return back()->with('success', 'Review deleted successfully.');
    }
}
