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
        Schema::table('room_availabilities', function (Blueprint $table) {
            // Composite performance indexes for sub-millisecond query execution on billions of rows
            $table->index(['date', 'is_blocked'], 'idx_avail_date_blocked');
            $table->index(['room_id', 'is_blocked', 'date'], 'idx_avail_room_blocked_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_availabilities', function (Blueprint $table) {
            $table->dropIndex('idx_avail_date_blocked');
            $table->dropIndex('idx_avail_room_blocked_date');
        });
    }
};
