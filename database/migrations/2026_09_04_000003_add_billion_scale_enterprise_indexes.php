<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - High-Concurrency Billion Scale Indexing Engine.
     */
    public function up(): void
    {
        // 1. HRMS Schema Optimizations
        Schema::table('departments', function (Blueprint $table) {
            $table->index(['hotel_id', 'is_active'], 'idx_dept_hotel_active');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->index(['hotel_id', 'is_active'], 'idx_shifts_hotel_active');
        });

        Schema::table('duty_rosters', function (Blueprint $table) {
            $table->index(['hotel_id', 'duty_date'], 'idx_roster_hotel_date');
            $table->index(['hotel_id', 'user_id', 'duty_date'], 'idx_roster_hotel_user_date');
        });

        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->index(['hotel_id', 'status'], 'idx_leaves_hotel_status');
            $table->index(['hotel_id', 'user_id'], 'idx_leaves_hotel_user');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->index(['hotel_id', 'month_year'], 'idx_payrolls_hotel_month');
            $table->unique(['hotel_id', 'user_id', 'month_year'], 'uniq_payroll_hotel_user_month');
        });

        // 2. Inventory & Stock Ledger Optimizations
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index(['hotel_id', 'type', 'created_at'], 'idx_stock_hotel_type_created');
            $table->index(['hotel_id', 'item_id', 'created_at'], 'idx_stock_hotel_item_created');
        });

        // 3. Personalised Amenities Optimizations
        Schema::table('amenity_services', function (Blueprint $table) {
            $table->index(['hotel_id', 'category', 'is_active'], 'idx_amenity_hotel_cat_active');
        });

        Schema::table('amenity_orders', function (Blueprint $table) {
            $table->index(['hotel_id', 'booked_for_date'], 'idx_amenity_ord_hotel_date');
            $table->index(['hotel_id', 'reservation_id'], 'idx_amenity_ord_hotel_res');
            $table->index(['hotel_id', 'status'], 'idx_amenity_ord_hotel_status');
        });

        // 4. Housekeeping & Folio Item Optimizations
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->index(['hotel_id', 'status', 'scheduled_date'], 'idx_hk_hotel_status_date');
        });

        Schema::table('folio_items', function (Blueprint $table) {
            $table->index(['hotel_id', 'folio_id'], 'idx_folio_items_hotel_folio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollbacks
    }
};
