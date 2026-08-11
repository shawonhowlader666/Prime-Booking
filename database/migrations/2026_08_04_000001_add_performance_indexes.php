<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance Indexes Migration
 * Adds composite and single-column indexes to all hot query paths.
 * This alone can reduce query time by 10-100x on large datasets.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Properties Table Indexes ──────────────────────────────────────
        Schema::table('properties', function (Blueprint $table) {
            // Most frequent search: active properties by city
            if (!$this->hasIndex('properties', 'idx_prop_status_city')) {
                $table->index(['status', 'city'], 'idx_prop_status_city');
            }
            // Featured property carousel query
            if (!$this->hasIndex('properties', 'idx_prop_featured_status')) {
                $table->index(['is_featured', 'status'], 'idx_prop_featured_status');
            }
            // Price range filter
            if (!$this->hasIndex('properties', 'idx_prop_price')) {
                $table->index(['price_per_night'], 'idx_prop_price');
            }
            // Star rating filter
            if (!$this->hasIndex('properties', 'idx_prop_star_status')) {
                $table->index(['star_rating', 'status'], 'idx_prop_star_status');
            }
            // Vendor properties listing
            if (!$this->hasIndex('properties', 'idx_prop_vendor')) {
                $table->index(['vendor_id', 'status'], 'idx_prop_vendor');
            }
            // Type filter (hotel/houseboat/homestay)
            if (!$this->hasIndex('properties', 'idx_prop_type_status')) {
                $table->index(['type', 'status'], 'idx_prop_type_status');
            }
            // Rating score (for ordering by best-rated)
            if (!$this->hasIndex('properties', 'idx_prop_rating')) {
                $table->index(['rating_score'], 'idx_prop_rating');
            }
            // Full-text search on name (MySQL FULLTEXT)
            if (\DB::getDriverName() === 'mysql' && !$this->hasIndex('properties', 'ft_prop_name')) {
                $table->fullText(['name'], 'ft_prop_name');
            }
        });

        // ── Rooms Table Indexes ───────────────────────────────────────────
        Schema::table('rooms', function (Blueprint $table) {
            // Rooms by property (most common join)
            if (!$this->hasIndex('rooms', 'idx_room_property_price')) {
                $table->index(['property_id', 'price_per_night'], 'idx_room_property_price');
            }
        });

        // ── Bookings Table Indexes ────────────────────────────────────────
        Schema::table('bookings', function (Blueprint $table) {
            // Admin dashboard: latest bookings by booking_status
            if (!$this->hasIndex('bookings', 'idx_booking_status_created') && Schema::hasColumn('bookings', 'booking_status')) {
                $table->index(['booking_status', 'created_at'], 'idx_booking_status_created');
            }
            // Also index 'status' if it exists (new column)
            if (!$this->hasIndex('bookings', 'idx_booking_new_status') && Schema::hasColumn('bookings', 'status')) {
                $table->index(['status', 'created_at'], 'idx_booking_new_status');
            }
            // Property booking history
            if (!$this->hasIndex('bookings', 'idx_booking_property_status')) {
                $table->index(['property_id'], 'idx_booking_property_status');
            }
            // User booking history (my-bookings page)
            if (!$this->hasIndex('bookings', 'idx_booking_user')) {
                $table->index(['user_id', 'created_at'], 'idx_booking_user');
            }
            // Availability check: date range overlap queries
            if (!$this->hasIndex('bookings', 'idx_booking_dates')) {
                $table->index(['property_id', 'check_in', 'check_out'], 'idx_booking_dates');
            }
        });

        // ── Users Table Indexes ───────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            // Role-based filtering (vendor list, customer list)
            if (!$this->hasIndex('users', 'idx_user_role_status')) {
                $table->index(['role', 'status'], 'idx_user_role_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('idx_prop_status_city');
            $table->dropIndex('idx_prop_featured_status');
            $table->dropIndex('idx_prop_price');
            $table->dropIndex('idx_prop_star_status');
            $table->dropIndex('idx_prop_vendor');
            $table->dropIndex('idx_prop_type_status');
            $table->dropIndex('idx_prop_rating');
            try { $table->dropFullText('ft_prop_name'); } catch(\Exception $e) {}
        });
        Schema::table('rooms',    function (Blueprint $table) { $table->dropIndex('idx_room_property_price'); });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_booking_status_created');
            $table->dropIndex('idx_booking_property_status');
            $table->dropIndex('idx_booking_user');
            $table->dropIndex('idx_booking_payment_status');
            $table->dropIndex('idx_booking_dates');
        });
        Schema::table('users', function (Blueprint $table) { $table->dropIndex('idx_user_role_status'); });
    }

    private function hasIndex(string $table, string $name): bool
    {
        try {
            if (\DB::getDriverName() === 'mysql') {
                $indexes = \DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$name}'");
                return count($indexes) > 0;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
};
