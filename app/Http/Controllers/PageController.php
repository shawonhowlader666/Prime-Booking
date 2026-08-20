<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PropertyRepository;
use App\Models\Promotion;
use App\Models\FeaturedDestination;
use App\Models\SiteSetting;
use App\Models\Property;
use App\Models\Booking;
use App\Models\CmsContent;
use App\Models\TourPackage;
use App\Models\Deal;
use App\Services\VIPLoyaltyService;

class PageController extends Controller
{
    public function __construct(
        protected PropertyRepository $properties
    ) {}

    public function switchCurrency(Request $request, string $code)
    {
        \App\Helpers\CurrencyHelper::setCurrency($code);
        return back()->with('success', "Currency switched to {$code}");
    }

    // ─────────────────────────────────────────────────────────────────────
    // HOMEPAGE — 100% Dynamic from DB/Admin/Vendor panels
    // ─────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        // Disable Varnish & Browser Caching for Homepage to serve fresh views
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        $company = config('company');
        $theme   = config('theme');

        // ── Featured Properties (8 items across 2 rows of 4) ─────────
        $featuredProperties = $this->properties->getFeatured(8);
        $stats              = $this->properties->getSiteStats();

        // ── Dynamic Promotions from Admin panel (cached 10 min) ───────────
        $accommodationPromos = Cache::remember(
            'promotions:accommodation', 600,
            fn() => Promotion::active()->ofType('accommodation')->ordered()->get()
        );

        $flightActivityPromos = Cache::remember(
            'promotions:flights_activities', 600,
            fn() => Promotion::active()
                ->whereIn('type', ['flights', 'activities', 'destination', 'general'])
                ->ordered()->get()
        );

        // ── Featured Destinations from Admin CMS (cached 15 min) ──────────
        $destinations = Cache::remember(
            'featured_destinations', 900,
            fn() => FeaturedDestination::active()->get()
        );

        // ── Property Type Stats from DB (live count, cached 30 min) ───────
        $propertyTypeCounts = Cache::remember('property_type_counts', 1800, function () {
            $counts = Property::whereIn('status', ['active', 'published'])
                ->selectRaw('LOWER(type) as property_type, COUNT(*) as count')
                ->groupBy('property_type')
                ->pluck('count', 'property_type');

            $types = ['hotel', 'resort', 'apartment', 'villa', 'hostel', 'guesthouse',
                      'houseboat', 'eco_lodge', 'cottage'];
            $result = [];
            foreach ($types as $t) {
                $result[$t] = $counts[$t] ?? 0;
            }
            $result['_total'] = $counts->sum();
            return $result;
        });

        // ── Logged-in User Data ───────────────────────────────────────────
        $currentUser     = Auth::user();
        $userBookings    = 0;
        $recentBookings  = collect();
        $vipTier         = 'Bronze';
        $vipDiscount     = 0;
        $vipNextTier     = 'Silver';
        $vipNextRequired = (int) SiteSetting::get('vip_silver_threshold', 2);

        if ($currentUser) {
            $userBookings = Cache::remember(
                "user_booking_count:{$currentUser->id}", 300,
                fn() => Booking::where('user_id', $currentUser->id)
                    ->where('created_at', '>=', now()->subYears(2))
                    ->count()
            );

            $recentBookings = Cache::remember(
                "user_recent_bookings:{$currentUser->id}", 300,
                fn() => Booking::where('user_id', $currentUser->id)
                    ->with(['property:id,name,city,primary_image,type'])
                    ->latest()
                    ->limit(4)
                    ->get()
            );

            $vipTier     = SiteSetting::vipTierForBookings($userBookings);
            $vipDiscount = (float) SiteSetting::get('vip_' . strtolower($vipTier) . '_discount', 0);

            $tierOrder  = ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond'];
            $currentIdx = array_search($vipTier, $tierOrder);
            if ($currentIdx !== false && $currentIdx < 4) {
                $vipNextTier     = $tierOrder[$currentIdx + 1];
                $nextKey         = 'vip_' . strtolower($vipNextTier) . '_threshold';
                $vipNextRequired = (int) SiteSetting::get($nextKey, 0);
            } else {
                $vipNextTier     = null;
                $vipNextRequired = null;
            }
        }

        // ── VIP Tier Thresholds for stepper (from DB settings) ────────────
        $vipThresholds = [
            'bronze'   => 0,
            'silver'   => (int) SiteSetting::get('vip_silver_threshold', 2),
            'gold'     => (int) SiteSetting::get('vip_gold_threshold', 5),
            'platinum' => (int) SiteSetting::get('vip_platinum_threshold', 10),
            'diamond'  => (int) SiteSetting::get('vip_diamond_threshold', 15),
        ];

        // ── Platform settings used in homepage hero ────────────────────────
        $siteSettings = [
            'site_name'    => SiteSetting::get('site_name', $company['name'] ?? 'Prime Booking'),
            'site_tagline' => SiteSetting::get('site_tagline', 'Best Price Guarantee'),
        ];

        return view('home', compact(
            'company', 'theme', 'featuredProperties', 'destinations', 'stats',
            'accommodationPromos', 'flightActivityPromos', 'propertyTypeCounts',
            'currentUser', 'userBookings', 'recentBookings',
            'vipTier', 'vipDiscount', 'vipNextTier', 'vipNextRequired', 'vipThresholds',
            'siteSettings'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // HOTEL DETAIL PAGE — Fully Dynamic from DB (Redis cached 15 min)
    // ─────────────────────────────────────────────────────────────────────
    public function hotelDetail($id)
    {
        $company  = config('company');
        $property = $this->properties->findWithRooms((int) $id);

        if (!$property) {
            abort(404, 'Property not found.');
        }

        $relatedProperties = $this->properties->getRelated($property, 4);

        return view('pages.hotel-detail', compact('company', 'property', 'relatedProperties'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // DYNAMIC CMS & PUBLIC PAGES
    // ─────────────────────────────────────────────────────────────────────
    public function about()
    {
        $aboutCms = CmsContent::getContent('about');
        return view('pages.about', ['company' => config('company'), 'aboutCms' => $aboutCms]);
    }

    public function services()
    {
        $company     = config('company');
        $servicesCms = CmsContent::getContent('services');
        return view('pages.services', compact('company', 'servicesCms'));
    }

    public function packages()
    {
        $packages = Cache::remember('tour_packages_active', 600, fn() => TourPackage::active()->ordered()->get());
        return view('pages.packages', ['company' => config('company'), 'packages' => $packages]);
    }

    public function deals()
    {
        $deals = Cache::remember('deals_active', 600, fn() => Deal::active()->ordered()->get());
        return view('pages.deals', ['company' => config('company'), 'deals' => $deals]);
    }

    public function contact()
    {
        $contactCms = CmsContent::getContent('contact_info');
        $siteSettings = [
            'email'   => SiteSetting::get('support_email', 'support@primebooking.com'),
            'phone'   => SiteSetting::get('support_phone', '+880 9612-345678'),
            'address' => SiteSetting::get('support_address', 'Dhaka, Bangladesh'),
        ];
        return view('pages.contact', ['company' => config('company'), 'contactCms' => $contactCms, 'siteSettings' => $siteSettings]);
    }

    public function privacy()
    {
        $privacyCms = CmsContent::getContent('privacy_policy', 'Privacy Policy', 'Our privacy policy details how we protect your data.');
        return view('pages.privacy', ['company' => config('company'), 'privacyCms' => $privacyCms]);
    }

    public function terms()
    {
        $termsCms = CmsContent::getContent('terms_conditions', 'Terms & Conditions', 'Our terms and conditions.');
        return view('pages.terms', ['company' => config('company'), 'termsCms' => $termsCms]);
    }

    public function trips()
    {
        return view('pages.trips', ['company' => config('company')]);
    }

    public function bookings()
    {
        return view('pages.bookings', ['company' => config('company')]);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('pages.profile', ['company' => config('company'), 'user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return back()->with('error', 'Please sign in to update your profile.');
        }

        $validated = $request->validate([
            'first_name'      => 'nullable|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:50',
            'dob'             => 'nullable|date',
            'gender'          => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:10',
            'passport_number' => 'nullable|string|max:100',
            'passport_expiry' => 'nullable|date',
        ]);

        $firstName = trim($validated['first_name'] ?? '');
        $lastName  = trim($validated['last_name'] ?? '');
        $fullName  = trim("{$firstName} {$lastName}");

        $user->update([
            'name'            => $fullName ?: $user->name,
            'phone'           => $validated['phone'] ?? $user->phone,
            'dob'             => $validated['dob'] ?? $user->dob,
            'gender'          => $validated['gender'] ?? $user->gender,
            'country'         => $validated['country'] ?? $user->country,
            'passport_number' => $validated['passport_number'] ?? $user->passport_number,
            'passport_expiry' => $validated['passport_expiry'] ?? $user->passport_expiry,
        ]);

        return back()->with('success', 'Your personal profile & travel details have been updated successfully!');
    }

    public function vip(VIPLoyaltyService $vipService)
    {
        $user = Auth::user();
        $userVipStats = $vipService->getUserTier($user);

        return view('pages.vip', [
            'company'       => config('company'),
            'userVipStats'  => $userVipStats,
        ]);
    }

    public function cashback(Request $request, \App\Services\RewardPointService $rewardService)
    {
        $user = auth()->user();
        $rewardSummary = $rewardService->getUserRewardSummary($user);
        $transactions = $user
            ? \App\Models\RewardTransaction::where('user_id', $user->id)->latest()->paginate(10)
            : collect();
        $pendingPayouts = $user
            ? \App\Models\RewardPayoutRequest::where('user_id', $user->id)->latest()->take(5)->get()
            : collect();

        return view('pages.cashback', [
            'company'        => config('company'),
            'user'           => $user,
            'rewardSummary'  => $rewardSummary,
            'transactions'   => $transactions,
            'pendingPayouts' => $pendingPayouts,
        ]);
    }

    public function submitRewardPayout(Request $request, \App\Services\RewardPointService $rewardService)
    {
        $request->validate([
            'points'          => 'required|integer|min:1',
            'payment_gateway' => 'required|string|in:bkash,nagad,rocket,bank',
            'account_number'  => 'required|string|max:50',
            'account_name'    => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to request reward cashout.');
        }

        $result = $rewardService->requestPayout(
            $user,
            (int) $request->points,
            $request->payment_gateway,
            $request->account_number,
            $request->account_name
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function pointsmax()
    {
        $user = auth()->user();
        $linkedPrograms = $user && $user->pointsmax_programs
            ? json_decode($user->pointsmax_programs, true)
            : (session('pointsmax_programs_' . ($user?->id ?? 'guest')) ?: []);

        return view('pages.pointsmax', [
            'company'        => config('company'),
            'linkedPrograms' => $linkedPrograms,
        ]);
    }

    public function linkPointsmaxProgram(Request $request)
    {
        $request->validate([
            'program'       => 'required|string',
            'membership_id' => 'required|string',
        ]);

        $user = auth()->user();
        $key = 'pointsmax_programs_' . ($user?->id ?? 'guest');
        $current = session($key, []);
        
        $newProgram = [
            'id'            => uniqid(),
            'program'       => $request->program,
            'membership_id' => $request->membership_id,
            'is_primary'    => $request->boolean('is_primary', true),
            'linked_at'     => now()->toFormattedDateString(),
        ];

        // If marked primary, unset other primaries
        if ($newProgram['is_primary']) {
            foreach ($current as &$p) {
                $p['is_primary'] = false;
            }
        }

        $current[] = $newProgram;
        session([$key => $current]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PointsMAX program linked successfully!',
                'program' => $newProgram,
            ]);
        }

        return redirect()->back()->with('success', 'PointsMAX program linked successfully!');
    }

    public function unlinkPointsmaxProgram(Request $request, $id)
    {
        $user = auth()->user();
        $key = 'pointsmax_programs_' . ($user?->id ?? 'guest');
        $current = session($key, []);

        $filtered = array_values(array_filter($current, function($item) use ($id) {
            return ($item['id'] ?? '') !== $id;
        }));

        session([$key => $filtered]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Program unlinked successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Program unlinked successfully.');
    }

    public function messages()
    {
        return view('pages.messages', ['company' => config('company')]);
    }

    public function reviews()
    {
        $user = auth()->user();
        $userReviews = $user
            ? \App\Models\Review::where('user_id', $user->id)->with('property:id,name,city,primary_image')->latest()->paginate(10)
            : collect();

        // Completed bookings without reviews for instant review CTA
        $pendingBookings = $user
            ? \App\Models\Booking::where('user_id', $user->id)
                ->whereIn('booking_status', ['completed', 'confirmed'])
                ->whereDoesntHave('property.reviews', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->with('property:id,name,city,primary_image')
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('pages.reviews', [
            'company'         => config('company'),
            'userReviews'     => $userReviews,
            'pendingBookings' => $pendingBookings,
        ]);
    }

    public function homes()
    {
        $vacationHomes = \App\Models\Property::whereIn('type', ['apartment', 'villa', 'homestay', 'cottage', 'Resort'])
            ->whereIn('status', ['active', 'published'])
            ->latest()
            ->take(6)
            ->get();
        return view('pages.homes', ['company' => config('company'), 'vacationHomes' => $vacationHomes]);
    }

    public function transfer()
    {
        $transfers = \App\Models\AirportTransfer::active()->get();
        return view('pages.transfer', ['company' => config('company'), 'transfers' => $transfers]);
    }

    public function subscriptions()
    {
        $user = auth()->user();
        return view('pages.subscriptions', [
            'company' => config('company'),
            'user'    => $user,
        ]);
    }

    public function updateSubscriptionSetting(Request $request)
    {
        $user = auth()->user();
        $key = $request->input('key');
        $value = $request->input('value');

        if ($user && $key) {
            // Save to user preferences in session / cache
            session(["sub_pref_{$user->id}_{$key}" => $value]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription preference updated successfully.',
            'key'     => $key,
            'value'   => $value,
        ]);
    }

    public function hostProperty()
    {
        return view('pages.host_property', ['company' => config('company')]);
    }

    public function signin()
    {
        return view('pages.signin', ['company' => config('company')]);
    }
}
