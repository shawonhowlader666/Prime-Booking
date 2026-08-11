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

        return view('admin.reviews.index', compact('reviews'));
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
