<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Get algorithmically ranked similar properties using Multi-Attribute Jaccard & Proximity Scoring.
     */
    public function getSimilarProperties(Property $target, int $limit = 4): Collection
    {
        $cacheKey = "smart_recommendations_v2_{$target->id}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($target, $limit) {
            $candidates = Property::where('id', '!=', $target->id)
                ->whereIn('status', ['active', 'published'])
                ->with(['rooms:id,property_id,name,price_per_night'])
                ->get();

            if ($candidates->isEmpty()) {
                return collect();
            }

            $targetPrice     = (float) ($target->price_per_night ?? 5000);
            $targetStar      = (int) ($target->star_rating ?? 3);
            $targetRating    = (float) ($target->rating_score ?? 8.0);
            $targetAmenities = is_array($target->amenities) ? $target->amenities : (json_decode((string)$target->amenities, true) ?: []);
            $targetCity      = strtolower(trim((string)$target->city));

            $scored = $candidates->map(function ($candidate) use ($target, $targetPrice, $targetStar, $targetRating, $targetAmenities, $targetCity) {
                $score = 0.0;

                // 1. City / Location Match (35 Points)
                $candCity = strtolower(trim((string)$candidate->city));
                if (!empty($targetCity) && $candCity === $targetCity) {
                    $score += 35.0;
                } elseif (str_contains($candCity, $targetCity) || str_contains($targetCity, $candCity)) {
                    $score += 25.0;
                }

                // 2. Price Proximity Gaussian-like Decay (30 Points)
                $candPrice = (float) ($candidate->price_per_night ?? 5000);
                $priceDiffRatio = abs($candPrice - $targetPrice) / max($targetPrice, 1000);
                $priceScore = max(0, 30.0 * (1.0 - min(1.0, $priceDiffRatio)));
                $score += $priceScore;

                // 3. Star Rating & Review Alignment (20 Points)
                $candStar = (int) ($candidate->star_rating ?? 3);
                $starDiff = abs($candStar - $targetStar);
                $starScore = max(0, 10.0 * (1.0 - ($starDiff / 5.0)));

                $candRating = (float) ($candidate->rating_score ?? 8.0);
                $ratingDiff = abs($candRating - $targetRating);
                $ratingScore = max(0, 10.0 * (1.0 - ($ratingDiff / 10.0)));
                $score += ($starScore + $ratingScore);

                // 4. Amenities Overlap Jaccard Similarity (15 Points)
                $candAmenities = is_array($candidate->amenities) ? $candidate->amenities : (json_decode((string)$candidate->amenities, true) ?: []);
                $intersection = count(array_intersect($targetAmenities, $candAmenities));
                $union = count(array_unique(array_merge($targetAmenities, $candAmenities)));
                $jaccardIndex = $union > 0 ? ($intersection / $union) : 0.5;
                $score += (15.0 * $jaccardIndex);

                $candidate->similarity_score = round($score, 1);
                $candidate->match_percentage = min(99, max(60, (int) round($score)));
                return $candidate;
            });

            return $scored->sortByDesc('similarity_score')->take($limit)->values();
        });
    }
}
