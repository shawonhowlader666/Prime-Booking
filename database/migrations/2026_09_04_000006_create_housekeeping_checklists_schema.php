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
        // 1. Housekeeping Checklist Items
        Schema::create('housekeeping_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('checkpoint', 150);
            $table->string('type', 50)->default('House Keeper'); // Laundry, House Keeper, Bathroom, Linen
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'type']);
            $table->index(['hotel_id', 'is_active']);
        });

        // 2. Add QR token and cleanliness to rooms if missing
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'cleanliness')) {
                $table->string('cleanliness', 30)->default('CLEAN')->after('status');
            }
            if (!Schema::hasColumn('rooms', 'qr_token')) {
                $table->string('qr_token', 64)->nullable()->after('cleanliness');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_checklists');
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'qr_token')) {
                $table->dropColumn('qr_token');
            }
        });
    }
};
