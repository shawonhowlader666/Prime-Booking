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
        'comment',
        'helpful_count',
        'unhelpful_count',
        'status',
        'vendor_reply',
        'replied_at',
    ];

    protected $casts = [
        'rating'          => 'float',
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
            // If rawAvg is on a 5-star scale, scale to Agoda 10-point standard
            $scaledScore = ($rawAvg <= 5.0) ? round($rawAvg * 2.0, 1) : round($rawAvg, 1);
            $property->update([
                'rating_score'  => $scaledScore,
                'total_reviews' => $count,
            ]);
        } else {
            // Keep default rating or fallback to star_rating based baseline
            $fallback = $property->star_rating ? min(10.0, (float)($property->star_rating * 1.8)) : 8.8;
            $property->update([
                'rating_score'  => $fallback,
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
