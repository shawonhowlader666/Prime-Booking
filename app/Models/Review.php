<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'guest_name',
        'rating',
        'cleanliness_score',
        'location_score',
        'service_score',
        'facilities_score',
        'comfort_score',
        'value_score',
        'comment',
        'helpful_count',
        'unhelpful_count',
        'status',
        'vendor_reply',
        'replied_at',
    ];

    protected $casts = [
        'rating'          => 'float',
        'cleanliness_score' => 'integer',
        'location_score'    => 'integer',
        'service_score'     => 'integer',
        'facilities_score'  => 'integer',
        'comfort_score'     => 'integer',
        'value_score'       => 'integer',
        'helpful_count'   => 'integer',
        'unhelpful_count' => 'integer',
        'replied_at'      => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            static::syncPropertyRating($review->property_id);
        });

        static::deleted(function (Review $review) {
            static::syncPropertyRating($review->property_id);
        });
    }

    /**
     * Synchronize hotel rating score & count with approved reviews
     */
    public static function syncPropertyRating(?int $propertyId): void
    {
        if (!$propertyId) {
            return;
        }

        $property = Property::find($propertyId);
        if (!$property) {
            return;
        }

        $approvedReviews = static::where('property_id', $propertyId)
            ->where('status', 'approved');

        $count = $approvedReviews->count();
        if ($count > 0) {
            $rawAvg = (float) $approvedReviews->avg('rating');
            // Ratings are stored as 1-5 stars; the listing shows the 10-point equivalent.
            $scaledScore = round(min(10.0, $rawAvg * 2.0), 1);
            $locationAvg = (clone $approvedReviews)->avg('location_score');
            $update = [
                'rating_score'  => $scaledScore,
                'total_reviews' => $count,
            ];
            // Location score on the map card follows guests who rated location; otherwise the admin value stays.
            if ($locationAvg !== null) {
                $update['location_score'] = round(min(10.0, (float) $locationAvg * 2.0), 1);
            }
            $property->update($update);
        } else {
            // No approved reviews: the listing is unrated. The site shows "New" instead of a score.
            $property->update([
                'rating_score'  => 0,
                'total_reviews' => 0,
            ]);
        }
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
