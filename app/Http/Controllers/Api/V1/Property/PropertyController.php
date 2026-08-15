<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PropertyResource;
use App\Repositories\PropertyRepository;
use App\Traits\ApiResponse;
use App\Models\Property;
use Illuminate\Http\Request;

/**
 * API V1 Property Controller — 100% Resource-based responses
 * Public: index, show (with caching via repository)
 * Protected: store, update, destroy (vendor auth required)
 */
class PropertyController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PropertyRepository $repository
    ) {}

    // ─── Public: List Properties ──────────────────────────────────────────

    /** GET /api/v1/properties — Full search with filters + Redis cache */
    public function index(Request $request)
    {
        $params = $request->only([
            'destination','search_type','min_price','max_price',
            'star_rating','sort_by','amenities','page','per_page',
            // Legacy param support
            'city','type','q','stars','featured',
        ]);

        // Map legacy params to repository format
        if ($request->city && !isset($params['destination'])) {
            $params['destination'] = $request->city;
        }
        if ($request->type && !isset($params['search_type'])) {
            $params['search_type'] = $request->type;
        }
        if ($request->q && !isset($params['destination'])) {
            $params['destination'] = $request->q;
        }
        if ($request->stars && !isset($params['star_rating'])) {
            $params['star_rating'] = $request->stars;
        }
        if ($request->featured) {
            $params['featured'] = true;
        }

        $result     = $this->repository->search($params);
        $paginator  = $result['paginator'];
        $properties = PropertyResource::collection(collect($result['results']));

        return $this->success($paginator, 'Properties retrieved.', 200, [
            'data' => $properties,
        ]);
    }

    /** GET /api/v1/properties/{id} — Single property with rooms (Redis cached 15min) */
    public function show(int $id)
    {
        $property = $this->repository->findWithRooms($id);

        if (!$property) {
            return $this->notFound('Property not found.');
        }

        return $this->success(new PropertyResource($property));
    }

    /** GET /api/v1/properties/featured — Homepage featured list */
    public function featured()
    {
        $properties = $this->repository->getFeatured(8);
        return $this->success(PropertyResource::collection($properties), 'Featured properties.');
    }

    /** GET /api/v1/destinations — Grouped destinations for homepage */
    public function destinations()
    {
        $destinations = $this->repository->getDestinations(10);
        return $this->success($destinations, 'Popular destinations.');
    }

    /** GET /api/v1/properties/cities — Available cities for filter */
    public function cities()
    {
        return $this->success($this->repository->getAvailableCities(), 'Available cities.');
    }

    // ─── Protected: Vendor CRUD (auth:sanctum required) ──────────────────

    /** POST /api/v1/vendor/properties */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:hotel,houseboat,homestay,apartment,resort',
            'city'            => 'required|string|max:100',
            'star_rating'     => 'required|integer|min:1|max:5',
            'address'         => 'required|string|max:400',
            'price_per_night' => 'required|numeric|min:100',
            'original_price'  => 'nullable|numeric|min:100',
            'description'     => 'required|string|min:50',
            'primary_image'   => 'nullable|url|max:500',
            'images'          => 'nullable|array|max:20',
            'images.*'        => 'url',
            'amenities'       => 'nullable|array',
        ]);

        $property = Property::create([
            ...$validated,
            'vendor_id'        => auth()->id(),
            'is_featured'      => false,
            'status'           => Property::STATUS_PENDING,
            'rejection_reason' => null,
        ]);

        return $this->created(
            new PropertyResource($property),
            'Property submitted for admin review. It will go live once approved.'
        );
    }

    /** PUT /api/v1/vendor/properties/{id} */
    public function update(Request $request, int $id)
    {
        $property = Property::where('id', $id)
            ->where('vendor_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'type'            => 'sometimes|in:hotel,houseboat,homestay,apartment,resort',
            'city'            => 'sometimes|string|max:100',
            'star_rating'     => 'sometimes|integer|min:1|max:5',
            'address'         => 'sometimes|string|max:400',
            'price_per_night' => 'sometimes|numeric|min:100',
            'original_price'  => 'nullable|numeric|min:100',
            'description'     => 'sometimes|string|min:50',
            'primary_image'   => 'nullable|url',
            'images'          => 'nullable|array',
            'images.*'        => 'url',
            'amenities'       => 'nullable|array',
        ]);

        $wasRejected = $property->status === Property::STATUS_REJECTED;

        if ($wasRejected) {
            $validated['status']           = Property::STATUS_PENDING;
            $validated['rejection_reason'] = null;
        }

        $property->update($validated);

        $msg = $wasRejected
            ? 'Property updated and resubmitted for admin review.'
            : 'Property updated successfully.';

        return $this->success(
            new PropertyResource($property->fresh()),
            $msg
        );
    }

    /** DELETE /api/v1/vendor/properties/{id} */
    public function destroy(int $id)
    {
        $property = Property::where('id', $id)
            ->where('vendor_id', auth()->id())
            ->firstOrFail();

        $name = $property->name;
        $property->delete();

        return $this->success(null, "\"{$name}\" deleted successfully.");
    }
}
