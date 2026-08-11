<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'rooms_left')) {
                $table->integer('rooms_left')->default(5)->after('total_reviews');
            }
            if (!Schema::hasColumn('properties', 'no_credit_card_required')) {
                $table->boolean('no_credit_card_required')->default(true)->after('rooms_left');
            }
            if (!Schema::hasColumn('properties', 'location_score')) {
                $table->decimal('location_score', 3, 1)->default(8.8)->after('rating_score');
            }
            if (!Schema::hasColumn('properties', 'nearest_landmark')) {
                $table->string('nearest_landmark')->nullable()->after('address');
            }
            if (!Schema::hasColumn('properties', 'free_cancellation')) {
                $table->boolean('free_cancellation')->default(true)->after('no_credit_card_required');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'rooms_left',
                'no_credit_card_required',
                'location_score',
                'nearest_landmark',
                'free_cancellation'
            ]);
        });
    }
};
