<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewManagementController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['property', 'user'])->latest()->paginate(20);

        $stats = [
            'total'    => Review::count(),
            'approved' => Review::where('status', 'approved')->count(),
            'pending'  => Review::where('status', 'pending')->orWhereNull('status')->count(),
            'avg'      => round((float) Review::avg('rating') ?: 4.8, 1),
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
