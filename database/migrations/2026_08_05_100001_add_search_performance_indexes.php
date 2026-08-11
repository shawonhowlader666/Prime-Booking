<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add enterprise-grade composite indexes to properties table.
 *
 * Design Rationale:
 * ─────────────────
 * At 1M+ properties, a full table scan on LIKE '%city%' will timeout.
 * We add carefully chosen composite indexes that mirror the most common
 * search query patterns. MySQL/MariaDB will use the leftmost matching
 * index automatically via the optimizer.
 *
 * Index naming convention: idx_{table}_{col1}_{col2}
 *
 * Covered queries:
 *   1. Status + featured  → homepage, featured carousel
 *   2. Status + city      → city-based destination search
 *   3. Status + type      → type filter (hotel/resort/apartment)
 *   4. Status + price     → price range filter + sort
 *   5. Status + rating    → rating sort + guest rating filter
 *   6. Status + city + type + price → combined filter (most common search)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {

            // ── Core status + featured (homepage carousel) ──────────────
            // Used by: getFeatured(), scopeFeatured()
            if (!$this->indexExists('properties', 'idx_props_status_featured')) {
                $table->index(['status', 'is_featured', 'rating_score'], 'idx_props_status_featured');
            }

            // ── Status + city (city search) ──────────────────────────────
            // Used by: search() with destination filter, getDestinations()
            if (!$this->indexExists('properties', 'idx_props_status_city')) {
                $table->index(['status', 'city'], 'idx_props_status_city');
            }

            // ── Status + type (property type filter) ─────────────────────
            // Used by: search() with searchType filter
            if (!$this->indexExists('properties', 'idx_props_status_type')) {
                $table->index(['status', 'type'], 'idx_props_status_type');
            }

            // ── Status + price (price range + sort) ──────────────────────
            // Used by: search() price_low / price_high sort + price range filter
            if (!$this->indexExists('properties', 'idx_props_status_price')) {
                $table->index(['status', 'price_per_night'], 'idx_props_status_price');
            }

            // ── Status + rating (rating sort + guest rating filter) ───────
            // Used by: search() rating sort + guestRating filter
            if (!$this->indexExists('properties', 'idx_props_status_rating')) {
                $table->index(['status', 'rating_score'], 'idx_props_status_rating');
            }

            // ── Status + star_rating (star filter) ───────────────────────
            // Used by: search() star_rating filter
            if (!$this->indexExists('properties', 'idx_props_status_stars')) {
                $table->index(['status', 'star_rating'], 'idx_props_status_stars');
            }

            // ── Vendor index (vendor dashboard queries) ───────────────────
            // Used by: VendorController, VendorDashboardController
            if (!$this->indexExists('properties', 'idx_props_vendor_status')) {
                $table->index(['vendor_id', 'status'], 'idx_props_vendor_status');
            }

            // ── Slug unique lookup (route model binding) ──────────────────
            // Used by: hotels/{slug} route
            if (!$this->indexExists('properties', 'idx_props_slug')) {
                $table->index(['slug'], 'idx_props_slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            foreach ([
                'idx_props_status_featured',
                'idx_props_status_city',
                'idx_props_status_type',
                'idx_props_status_price',
                'idx_props_status_rating',
                'idx_props_status_stars',
                'idx_props_vendor_status',
                'idx_props_slug',
            ] as $index) {
                if ($this->indexExists('properties', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }

    /** Check if index already exists to make migration idempotent. */
    private function indexExists(string $table, string $index): bool
    {
        try {
            if (\DB::getDriverName() === 'mysql') {
                $indexes = \Illuminate\Support\Facades\DB::select(
                    "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                    [$index]
                );
                return count($indexes) > 0;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
};
