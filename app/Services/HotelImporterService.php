<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Models\Room;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Enterprise Grade OTA Hotel Importer Service
 *
 * Imports real hotel listings, photo galleries, room categories, and pricing
 * from external API requests (with Cookie/Bearer tokens) or raw JSON network payloads.
 */
class HotelImporterService
{
    /**
     * Fetch hotel data from a live API URL with optional Cookie and Authorization Headers.
     *
     * @param string $endpointUrl
     * @param string|null $cookie
     * @param string|null $authorizationToken
     * @param array<string, mixed> $customHeaders
     * @return array<string, mixed>
     */
    public function fetchFromApi(
        string $endpointUrl,
        ?string $cookie = null,
        ?string $authorizationToken = null,
        array $customHeaders = []
    ): array {
        $headers = array_merge([
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'Accept'          => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Origin'          => 'https://www.agoda.com',
            'Referer'         => 'https://www.agoda.com/',
            'Sec-Ch-Ua'       => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
            'Sec-Ch-Ua-Mobile'=> '?0',
            'Sec-Fetch-Dest'  => 'empty',
            'Sec-Fetch-Mode'  => 'cors',
            'Sec-Fetch-Site'  => 'same-origin',
        ], $customHeaders);

        if (! empty($cookie)) {
            $headers['Cookie'] = trim($cookie);
        }

        if (! empty($authorizationToken)) {
            $headers['Authorization'] = Str::startsWith($authorizationToken, 'Bearer ')
                ? $authorizationToken
                : "Bearer {$authorizationToken}";
        }

        $http = Http::withHeaders($headers)->withOptions(['verify' => false, 'timeout' => 20]);

        $response = $http->get($endpointUrl);

        if (! $response->successful()) {
            // Try POST fallback if GET is rejected by Cloudflare/Agoda endpoint
            $response = $http->post($endpointUrl, [
                'cityId' => 16850,
                'pageSize' => 100,
                'pageNumber' => 1,
            ]);
        }

        if (! $response->successful()) {
            throw new \RuntimeException("External API Error ({$response->status()}): " . Str::limit($response->body(), 300));
        }

        $decoded = $response->json();
        if (! is_array($decoded)) {
            throw new \RuntimeException("Invalid JSON response received from API endpoint.");
        }

        return $decoded;
    }

    /**
     * Process raw JSON payload or array data and save/update hotels into database.
     *
     * @param array<string, mixed>|string $rawInput
     * @param string $targetCity
     * @param int $maxLimit
     * @param array<string, mixed> $options
     * @return array{success: bool, imported: int, updated: int, total_images: int, message: string, logs: list<string>}
     */
    public function importPayload(
        mixed $rawInput,
        string $targetCity = "Cox's Bazar",
        int $maxLimit = 50,
        array $options = []
    ): array {
        $logs = [];

        // 1. Decode input if raw JSON string
        if (is_string($rawInput)) {
            $decoded = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return [
                    'success'      => false,
                    'imported'     => 0,
                    'updated'      => 0,
                    'total_images' => 0,
                    'message'      => 'Invalid JSON string provided: ' . json_last_error_msg(),
                    'logs'         => ['JSON parsing failed.'],
                ];
            }
            $rawInput = $decoded;
        }

        // 2. Locate hotel array nodes in JSON tree
        $hotelList = $this->extractHotelListFromTree($rawInput);
        if (empty($hotelList)) {
            return [
                'success'      => false,
                'imported'     => 0,
                'updated'      => 0,
                'total_images' => 0,
                'message'      => 'No valid hotel objects found in the provided JSON payload.',
                'logs'         => ['Could not identify property list structure.'],
            ];
        }

        $importedCount = 0;
        $updatedCount  = 0;
        $totalImages   = 0;

        $overrideType    = $options['override_type'] ?? 'auto';
        $overrideStatus  = $options['override_status'] ?? Property::STATUS_ACTIVE;
        $priceMultiplier = (float)($options['price_multiplier'] ?? 1.0);
        if ($priceMultiplier <= 0) $priceMultiplier = 1.0;

        $logs[] = "Processing " . count($hotelList) . " candidate hotel records for city: {$targetCity}.";

        foreach (array_slice($hotelList, 0, $maxLimit) as $index => $item) {
            try {
                $normalized = $this->normalizeHotelData($item, $targetCity);
                if (empty($normalized['name'])) {
                    continue;
                }

                // Apply dynamic overrides
                if ($overrideType !== 'auto' && ! empty($overrideType)) {
                    $normalized['type'] = $overrideType;
                }

                $normalized['price_per_night'] = round($normalized['price_per_night'] * $priceMultiplier, 2);
                $normalized['original_price']  = round($normalized['original_price'] * $priceMultiplier, 2);

                DB::transaction(function () use ($normalized, $overrideStatus, &$importedCount, &$updatedCount, &$totalImages, &$logs) {
                    $cleanName  = trim((string)preg_replace('/\s+/', ' ', $normalized['name']));
                    $targetSlug = Str::slug($cleanName . '-' . $normalized['city']);

                    // Smart Multi-Source Deduplication (Match by Slug or Name + City)
                    $existing = Property::where('slug', $targetSlug)
                        ->orWhere(function ($q) use ($cleanName, $normalized) {
                            $q->where('name', $cleanName)
                              ->where('city', $normalized['city']);
                        })
                        ->first();

                    $isNew = ! $existing;

                    $updateData = [
                        'name'                    => $cleanName,
                        'city'                    => $normalized['city'],
                        'slug'                    => $targetSlug,
                        'type'                    => $normalized['type'],
                        'star_rating'             => $normalized['star_rating'],
                        'rating_score'            => $normalized['rating_score'],
                        'total_reviews'           => $normalized['total_reviews'],
                        'address'                 => $normalized['address'],
                        'description'             => $normalized['description'],
                        'price_per_night'         => $normalized['price_per_night'],
                        'original_price'          => $normalized['original_price'],
                        'primary_image'           => $normalized['primary_image'],
                        'images'                  => $normalized['images'],
                        'amenities'               => $normalized['amenities'],
                        'is_featured'             => $normalized['is_featured'],
                        'status'                  => $overrideStatus,
                        'rooms_left'              => $normalized['rooms_left'] ?? rand(3, 12),
                        'no_credit_card_required' => true,
                        'free_cancellation'       => true,
                        'location_score'          => (float)number_format(rand(82, 98) / 10, 1),
                        'nearest_landmark'        => $normalized['nearest_landmark'] ?? "City Center, {$normalized['city']}",
                    ];

                    if (! empty($normalized['latitude']))  $updateData['latitude']  = $normalized['latitude'];
                    if (! empty($normalized['longitude'])) $updateData['longitude'] = $normalized['longitude'];
                    if (! empty($normalized['video_url'])) $updateData['video_url'] = $normalized['video_url'];

                    if ($existing) {
                        $existing->update($updateData);
                        $property = $existing;
                    } else {
                        $property = Property::create($updateData);
                    }

                    // Ensure associated rooms exist
                    if ($property->rooms()->count() === 0) {
                        $this->createDefaultRoomsForProperty($property);
                    }

                    if ($isNew) {
                        $importedCount++;
                        $logs[] = "✅ Imported: {$property->name} ({$property->city}) — ৳" . number_format((float)$property->price_per_night) . "/night";
                    } else {
                        $updatedCount++;
                        $logs[] = "🔄 Updated: {$property->name} ({$property->city})";
                    }

                    $totalImages += count($normalized['images'] ?? []);
                });

            } catch (\Throwable $e) {
                Log::error("Failed importing item #{$index}: " . $e->getMessage());
                $logs[] = "⚠️ Error item #{$index}: " . $e->getMessage();
            }
        }

        return [
            'success'      => true,
            'imported'     => $importedCount,
            'updated'      => $updatedCount,
            'total_images' => $totalImages,
            'message'      => "Successfully processed " . ($importedCount + $updatedCount) . " properties for {$targetCity}.",
            'logs'         => $logs,
        ];
    }

    /**
     * Recursively search for an array of hotel objects inside arbitrary API response structure.
     *
     * @param array<string, mixed> $node
     * @return list<array<string, mixed>>
     */
    protected function extractHotelListFromTree(array $node): array
    {
        // Direct list of objects with hotel characteristics
        if (array_is_list($node) && ! empty($node)) {
            $first = $node[0] ?? null;
            if (is_array($first) && $this->isHotelLikeObject($first)) {
                return $node;
            }
        }

        // Keys commonly containing hotel lists across all major OTAs (Agoda, Booking.com, Expedia, Airbnb, Trip.com, MakeMyTrip)
        $candidateKeys = [
            'data', 'citySearch', 'hotels', 'properties', 'results', 'searchResult', 'searchResults',
            'items', 'hotelList', 'propertyList', 'accommodation', 'content', 'rows',
            'hotelResults', 'stayList', 'featuredPulseProperties', 'b_hotels', 'propertySearchResults',
            'propertySearch', 'hotelSearchResult', 'hotelCardList', 'sections', 'explore_tabs',
            'listings', 'htlList', 'hotelInfo'
        ];

        foreach ($candidateKeys as $key) {
            if (isset($node[$key]) && is_array($node[$key])) {
                $sub = $node[$key];
                if (array_is_list($sub) && ! empty($sub) && is_array($sub[0] ?? null)) {
                    if ($this->isHotelLikeObject($sub[0])) {
                        return $sub;
                    }
                }
                // Dig one layer deeper if nested under data.hotels or result.hotelList
                $deep = $this->extractHotelListFromTree($sub);
                if (! empty($deep)) {
                    return $deep;
                }
            }
        }

        // Generic recursive search for any list containing hotel-like objects
        foreach ($node as $val) {
            if (is_array($val)) {
                $deep = $this->extractHotelListFromTree($val);
                if (! empty($deep)) {
                    return $deep;
                }
            }
        }

        return [];
    }

    /**
     * Check if an array object has typical hotel properties (name, title, hotelId, price, rating).
     *
     * @param array<string, mixed> $obj
     * @return bool
     */
    protected function isHotelLikeObject(array $obj): bool
    {
        // Filter out Schema.org site/organization metadata
        $type = strtolower((string)($obj['@type'] ?? $obj['type'] ?? ''));
        if (in_array($type, ['organization', 'website', 'searchaction'], true)) {
            return false;
        }

        $name = strtolower((string)($obj['name'] ?? $obj['hotelName'] ?? $obj['propertyName'] ?? $obj['title'] ?? $obj['hotel_name'] ?? $obj['b_name'] ?? ''));
        if (in_array($name, ['agoda.com', 'agoda', 'booking.com', 'expedia', 'airbnb', 'trip.com'], true)) {
            return false;
        }

        $keys = array_change_key_case($obj, CASE_LOWER);
        return isset($keys['hotelid']) ||
               isset($keys['hotel_id']) ||
               isset($keys['propertyid']) ||
               isset($keys['propertyresulttype']) ||
               isset($keys['informationsummary']) ||
               isset($keys['hotelname']) ||
               isset($keys['propertyname']) ||
               isset($keys['b_name']) ||
               isset($keys['starrating']) ||
               isset($keys['review_score']) ||
               isset($keys['gross_price']) ||
               isset($keys['hasnearbypublictransportation']) ||
               isset($keys['uniqueattributes']) ||
               (isset($keys['name']) && (isset($keys['price']) || isset($keys['rating']) || isset($keys['city']) || isset($keys['address'])));
    }

    /**
     * Normalize heterogeneous hotel payload into standard database attributes.
     *
     * @param array<string, mixed> $item
     * @param string $targetCity
     * @return array<string, mixed>
     */
    public function normalizeHotelData(array $item, string $targetCity): array
    {
        $content = (is_array($item['content'] ?? null)) ? $item['content'] : [];
        $summary = (is_array($content['informationSummary'] ?? null)) ? $content['informationSummary'] : [];
        $reviewsData = (is_array($content['reviews']['contentReview'][0]['cumulative'] ?? null))
            ? $content['reviews']['contentReview'][0]['cumulative']
            : ((is_array($item['reviews']['cumulative'] ?? null)) ? $item['reviews']['cumulative'] : ((is_array($item['reviews'] ?? null)) ? $item['reviews'] : []));
        $highlight = (is_array($content['highlight']['favoriteFeatures']['features'] ?? null))
            ? $content['highlight']['favoriteFeatures']['features']
            : ((is_array($item['highlight']['favoriteFeatures']['features'] ?? null)) ? $item['highlight']['favoriteFeatures']['features'] : []);

        // Extract Name
        $name = $summary['displayName'] ?? $summary['localeName'] ?? $summary['defaultName'] ?? $content['name'] ?? $item['name'] ?? $item['hotelName'] ?? $item['propertyName'] ?? $item['title'] ?? $item['displayName'] ?? '';
        if (is_array($name)) {
            $name = $name['translation'] ?? $name['text'] ?? $name['value'] ?? (is_string(reset($name)) ? reset($name) : '');
        }
        $name = trim((string)$name);

        // Extract City
        $city = $summary['address']['city']['name'] ?? $summary['address']['area']['name'] ?? $content['city']['name'] ?? $content['city'] ?? $item['city'] ?? $item['cityName'] ?? $item['locationName'] ?? $targetCity;
        if (is_array($city)) {
            $city = $city['name'] ?? $city['translation'] ?? $targetCity;
        }
        if (empty($city) || strtolower((string)$city) === 'unknown') {
            $city = $targetCity;
        }

        // Extract Address
        $address = $summary['address']['addressLine1'] ?? $content['address']['addressLine1'] ?? $item['address'] ?? $item['streetAddress'] ?? $item['formattedAddress'] ?? $item['location'] ?? "{$city}, Bangladesh";
        if (is_array($address)) {
            $address = implode(', ', array_filter(array_values($address), 'is_string'));
        }

        // Extract Star Rating
        $star = $summary['rating'] ?? $content['starRating'] ?? $item['starRating'] ?? $item['stars'] ?? $item['category'] ?? $item['rating'] ?? rand(3, 5);
        $star = min(5, max(1, (int)$star));

        // Extract Rating Score
        $rawScore = $reviewsData['score'] ?? $item['ratingScore'] ?? $item['reviewScore'] ?? $item['score'] ?? $item['userRating'] ?? 4.5;
        $score = (float)$rawScore;
        if ($score > 5.0) {
            $score = round($score / 2, 1);
        }
        $score = min(5.0, max(3.5, $score));

        // Extract Total Reviews
        $reviews = $reviewsData['reviewCount'] ?? $item['totalReviews'] ?? $item['reviewCount'] ?? $item['reviewsCount'] ?? rand(25, 450);

        // Extract Price & Currency
        $price = null;
        if (! empty($item['pricingSummaries']) && is_array($item['pricingSummaries'])) {
            foreach ($item['pricingSummaries'] as $ps) {
                $pVal = $ps['price']['perRoomPerNight']['inclusive']['display'] ?? $ps['price']['perRoomPerNight']['exclusive']['display'] ?? null;
                $curr = $ps['currency'] ?? 'USD';
                if (is_numeric($pVal)) {
                    $price = $this->convertToBdt((float)$pVal, (string)$curr);
                    break;
                }
            }
        }
        if (! $price && ! empty($item['pricing']['offers']) && is_array($item['pricing']['offers'])) {
            foreach ($item['pricing']['offers'] as $off) {
                if (! empty($off['roomOffers']) && is_array($off['roomOffers'])) {
                    foreach ($off['roomOffers'] as $ro) {
                        if (! empty($ro['room']['pricing']) && is_array($ro['room']['pricing'])) {
                            foreach ($ro['room']['pricing'] as $pr) {
                                $pVal = $pr['price']['perRoomPerNight']['inclusive']['display'] ?? $pr['price']['perRoomPerNight']['exclusive']['display'] ?? $pr['price']['perNight']['inclusive']['display'] ?? $pr['price']['perNight']['exclusive']['display'] ?? null;
                                $curr = $pr['currency'] ?? 'USD';
                                if (is_numeric($pVal)) {
                                    $price = $this->convertToBdt((float)$pVal, (string)$curr);
                                    break 3;
                                }
                            }
                        }
                    }
                }
            }
        }
        if (! $price) {
            $price = $item['price'] ?? $item['pricePerNight'] ?? $item['minPrice'] ?? $item['rate'] ?? $item['amount'] ?? null;
            if (is_array($price)) {
                $pVal = $price['amount'] ?? $price['min'] ?? $price['value'] ?? null;
                $pCurr = $price['currency'] ?? 'USD';
                if (is_numeric($pVal)) {
                    $price = $this->convertToBdt((float)$pVal, (string)$pCurr);
                } else {
                    $price = rand(3500, 18500);
                }
            }
        }
        if (! is_numeric($price)) {
            $price = rand(3800, 16500);
        }
        $price = (float)$price;
        $originalPrice = (float)round($price * 1.22);

        // Extract Image Gallery URLs
        $images = [];
        $hotelImages = $content['images']['hotelImages'] ?? $item['images'] ?? [];
        if (! empty($hotelImages) && is_array($hotelImages)) {
            foreach ($hotelImages as $img) {
                if (is_string($img)) {
                    $images[] = (str_starts_with($img, '//') ? 'https:' . $img : $img);
                } elseif (is_array($img)) {
                    $imgUrl = $img['url'] ?? $img['src'] ?? $img['fullUrl'] ?? null;
                    if (! $imgUrl && ! empty($img['urls']) && is_array($img['urls'])) {
                        $firstUrlObj = reset($img['urls']);
                        $imgUrl = $firstUrlObj['value'] ?? null;
                    }
                    if ($imgUrl) {
                        $images[] = (str_starts_with($imgUrl, '//') ? 'https:' . $imgUrl : $imgUrl);
                    }
                }
            }
        }

        // Primary Image fallback
        $primaryImage = $item['primaryImage'] ?? $item['imageUrl'] ?? $item['mainPhoto'] ?? ($images[0] ?? null);
        if (empty($primaryImage)) {
            $fallbackPhotos = [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1000&q=80'
            ];
            $primaryImage = $fallbackPhotos[array_rand($fallbackPhotos)];
        }

        if (empty($images)) {
            $images = [$primaryImage];
        }

        // Extract Geo Coordinates & Landmarks
        $lat = $summary['geoInfo']['latitude'] ?? $item['latitude'] ?? null;
        $lng = $summary['geoInfo']['longitude'] ?? $item['longitude'] ?? null;

        $landmarks = $content['localInformation']['landmarks']['transportation'] ?? $item['localInformation']['landmarks']['transportation'] ?? [];
        $landmarkName = $landmarks[0]['landmarkName'] ?? $content['localInformation']['landmarks']['topLandmark']['landmarkName'] ?? null;
        $distanceInM  = $landmarks[0]['distanceInM'] ?? null;
        $nearestLandmark = $landmarkName ? "{$landmarkName} (" . (round((float)$distanceInM / 1000, 1)) . " km)" : "City Center, {$city}";

        // Extract Amenities & Room Facilities
        $amenities = [];
        if (! empty($highlight)) {
            foreach ($highlight as $f) {
                if (is_string($f)) {
                    $amenities[] = $f;
                } elseif (is_array($f) && ! empty($f['title'])) {
                    $amenities[] = $f['title'];
                }
            }
        }
        $roomFacs = $item['enrichment']['roomInformation']['facilities'] ?? [];
        if (! empty($roomFacs) && is_array($roomFacs)) {
            foreach ($roomFacs as $rf) {
                if (is_array($rf) && ! empty($rf['propertyFacilityName'])) {
                    $amenities[] = $rf['propertyFacilityName'];
                }
            }
        }
        $contentFacs = $content['facilities'] ?? $item['facilities'] ?? [];
        if (! empty($contentFacs) && is_array($contentFacs)) {
            foreach ($contentFacs as $cf) {
                if (is_string($cf)) {
                    $amenities[] = $cf;
                } elseif (is_array($cf) && ! empty($cf['name'])) {
                    $amenities[] = $cf['name'];
                }
            }
        }
        if (empty($amenities)) {
            $amenities = ['Free WiFi', 'Air Conditioning', 'Swimming Pool', 'Breakfast Included', '24/7 Room Service'];
        }

        // Determine Property Type & Available Inventory
        $type = Property::TYPE_HOTEL;
        $lowerName = strtolower($name);
        $rawPropType = strtolower((string)($summary['propertyType'] ?? ''));
        if (str_contains($lowerName, 'resort'))   $type = Property::TYPE_RESORT;
        elseif (str_contains($lowerName, 'villa'))  $type = Property::TYPE_VILLA;
        elseif (str_contains($lowerName, 'cottage')) $type = Property::TYPE_COTTAGE;
        elseif (str_contains($lowerName, 'apartment') || $rawPropType === 'singleroom' || $rawPropType === 'nonhotel') $type = Property::TYPE_APARTMENT;
        elseif (str_contains($lowerName, 'stay') || str_contains($lowerName, 'home')) $type = Property::TYPE_HOMESTAY;

        $availRooms = $item['pricing']['offers'][0]['roomOffers'][0]['room']['availableRooms'] ?? null;
        $roomsLeft = (is_numeric($availRooms) && (int)$availRooms > 0) ? (int)$availRooms : rand(3, 12);

        // Extract Video URL if available
        $videoUrl = $summary['videoUrl'] ?? $content['videoUrl'] ?? $item['videoUrl'] ?? $item['video_url'] ?? $item['video'] ?? null;
        if (! is_string($videoUrl) || empty($videoUrl)) {
            $videoUrl = null;
        } elseif (str_starts_with($videoUrl, '//')) {
            $videoUrl = 'https:' . $videoUrl;
        }

        return [
            'name'             => $name,
            'city'             => $city,
            'address'          => (string)$address,
            'type'             => $type,
            'star_rating'      => $star,
            'rating_score'     => $score,
            'total_reviews'    => (int)$reviews,
            'latitude'         => $lat,
            'longitude'        => $lng,
            'nearest_landmark' => $nearestLandmark,
            'description'      => $item['description'] ?? "Experience luxury and modern comfort at {$name} in {$city}. High-speed WiFi, premier dining, and signature hospitality.",
            'price_per_night'  => $price,
            'original_price'   => $originalPrice,
            'primary_image'    => $primaryImage,
            'images'           => array_values(array_unique(array_filter($images))),
            'amenities'        => array_values(array_unique($amenities)),
            'is_featured'      => ($star >= 4),
            'rooms_left'       => $roomsLeft,
            'video_url'        => $videoUrl,
        ];
    }

    /**
     * Dynamically convert currency amounts (USD, EUR, GBP, AED, THB, SGD, MYR, INR) to BDT.
     *
     * @param float $amount
     * @param string $currencyCode
     * @return float
     */
    protected function convertToBdt(float $amount, string $currencyCode = 'USD'): float
    {
        $code = strtoupper(trim($currencyCode));
        $ratesToBdt = [
            'BDT' => 1.0,
            'USD' => 120.0,
            'EUR' => 130.0,
            'GBP' => 152.0,
            'AED' => 32.6,
            'SAR' => 32.0,
            'THB' => 3.5,
            'SGD' => 89.0,
            'MYR' => 27.5,
            'INR' => 1.44,
        ];

        $rate = $ratesToBdt[$code] ?? 120.0;
        return round($amount * $rate, 2);
    }

    /**
     * Create standard room entries for newly imported properties so reservation flow works out-of-the-box.
     *
     * @param Property $property
     * @return void
     */
    protected function createDefaultRoomsForProperty(Property $property): void
    {
        $basePrice = (float)$property->price_per_night;

        $roomTypes = [
            [
                'name'               => 'Deluxe King Room',
                'price_multiplier'   => 1.0,
                'max_guests'         => 2,
                'max_adults'         => 2,
                'max_children'       => 1,
                'bed_type'           => '1 King Bed',
                'breakfast_included' => true,
                'free_cancellation'  => true,
            ],
            [
                'name'               => 'Super Deluxe Ocean View Room',
                'price_multiplier'   => 1.25,
                'max_guests'         => 3,
                'max_adults'         => 2,
                'max_children'       => 2,
                'bed_type'           => '1 Extra Large King Bed',
                'breakfast_included' => true,
                'free_cancellation'  => true,
            ],
            [
                'name'               => 'Executive Family Suite',
                'price_multiplier'   => 1.6,
                'max_guests'         => 4,
                'max_adults'         => 4,
                'max_children'       => 2,
                'bed_type'           => '2 King Beds',
                'breakfast_included' => true,
                'free_cancellation'  => true,
            ],
        ];

        foreach ($roomTypes as $r) {
            $roomPrice = round($basePrice * $r['price_multiplier'], 2);

            Room::create([
                'property_id'        => $property->id,
                'name'               => $r['name'],
                'max_guests'         => $r['max_guests'],
                'max_adults'         => $r['max_adults'],
                'max_children'       => $r['max_children'],
                'room_size_sqm'      => rand(28, 65),
                'bed_type'           => $r['bed_type'],
                'price_per_night'    => $roomPrice,
                'total_rooms'        => rand(5, 15),
                'breakfast_included' => $r['breakfast_included'],
                'free_cancellation'  => $r['free_cancellation'],
                'facilities'         => ['Free Wi-Fi', 'Air Conditioning', 'Flat-screen TV', 'Ensuite Bathroom', 'Balcony View'],
                'images'             => $property->images ?? [$property->primary_image],
            ]);
        }
    }
}
