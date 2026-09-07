<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->index(['subscription_status', 'is_subscription_locked'], 'idx_hotels_sub_status_locked');
            $table->index(['is_active'], 'idx_hotels_is_active');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['hotel_id', 'status', 'check_in_date'], 'idx_reservations_lookup');
            $table->index(['hotel_id', 'created_at'], 'idx_reservations_recent');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->index(['hotel_id', 'status', 'cleanliness'], 'idx_rooms_status_clean');
            $table->index(['hotel_id', 'room_type_id'], 'idx_rooms_type');
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->index(['hotel_id', 'status'], 'idx_folios_hotel_status');
        });

        Schema::table('tenant_subscriptions', function (Blueprint $table) {
            $table->index(['hotel_id', 'status', 'expires_at'], 'idx_subscriptions_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex('idx_hotels_sub_status_locked');
            $table->dropIndex('idx_hotels_is_active');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_reservations_lookup');
            $table->dropIndex('idx_reservations_recent');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_status_clean');
            $table->dropIndex('idx_rooms_type');
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->dropIndex('idx_folios_hotel_status');
        });

        Schema::table('tenant_subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_subscriptions_expiry');
        });
    }
};
