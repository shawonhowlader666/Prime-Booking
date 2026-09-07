<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    protected function getHotel(): Hotel
    {
        $hotelId = session('active_hotel_id', auth()->user()->hotel_id ?? null);
        return Hotel::find($hotelId) ?? Hotel::firstOrFail();
    }

    public function show(): View
    {
        $hotel = $this->getHotel();
        $details = $this->subscriptionService->getSubscriptionDetails($hotel);

        return view('pages.subscription', compact('details', 'hotel'));
    }

    public function renew(Request $request): RedirectResponse
    {
        $hotel = $this->getHotel();

        $validated = $request->validate([
            'months' => 'nullable|integer|min:1|max:24',
            'payment_method' => 'nullable|string',
        ]);

        $sub = $this->subscriptionService->renewSubscription($hotel, $validated);

        return redirect()->route('subscription.show')->with('success', "Subscription renewed successfully! Valid until {$sub->expires_at->format('d M, Y')}.");
    }
}
