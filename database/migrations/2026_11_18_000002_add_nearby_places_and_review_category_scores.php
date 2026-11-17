<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Real location and review data for the hotel page:
 *  - properties.nearby_places: places near the hotel entered by admin/vendor
 *    (name, distance in km, category) instead of lists invented from the city name.
 *  - reviews.*_score: the per-category stars a guest gives (1-5, like rating), averaged into
 *    the hotel's "Cleanliness / Location / ..." breakdown.
 */
return new class extends Migration
{
    private const SCORE_COLUMNS = [
        'cleanliness_score', 'location_score', 'service_score',
        'facilities_score', 'comfort_score', 'value_score',
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('properties', 'nearby_places')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->json('nearby_places')->nullable()->after('nearest_landmark');
            });
        }

        Schema::table('reviews', function (Blueprint $table) {
            foreach (self::SCORE_COLUMNS as $column) {
                if (!Schema::hasColumn('reviews', $column)) {
                    $table->unsignedTinyInteger($column)->nullable()->after('rating');
                }
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('properties', 'nearby_places')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('nearby_places');
            });
        }

        $existing = array_values(array_filter(self::SCORE_COLUMNS, fn ($c) => Schema::hasColumn('reviews', $c)));
        if ($existing !== []) {
            Schema::table('reviews', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
