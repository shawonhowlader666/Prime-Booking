<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'latitude')) {
                $table->string('latitude', 50)->nullable()->after('nearest_landmark');
            }
            if (!Schema::hasColumn('properties', 'longitude')) {
                $table->string('longitude', 50)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('properties', 'map_embed_url')) {
                $table->text('map_embed_url')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('properties', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('city');
            }
            if (!Schema::hasColumn('properties', 'checkin_time')) {
                $table->string('checkin_time', 20)->default('14:00')->after('description');
            }
            if (!Schema::hasColumn('properties', 'checkout_time')) {
                $table->string('checkout_time', 20)->default('12:00')->after('checkin_time');
            }
            if (!Schema::hasColumn('properties', 'contact_phone')) {
                $table->string('contact_phone', 50)->nullable()->after('checkout_time');
            }
            if (!Schema::hasColumn('properties', 'contact_email')) {
                $table->string('contact_email', 100)->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('properties', 'house_rules')) {
                $table->text('house_rules')->nullable()->after('contact_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'map_embed_url', 'postal_code',
                'checkin_time', 'checkout_time', 'contact_phone', 'contact_email', 'house_rules'
            ]);
        });
    }
};
