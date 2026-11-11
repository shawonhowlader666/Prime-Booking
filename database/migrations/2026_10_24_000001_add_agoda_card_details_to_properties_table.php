<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'feature_snippet')) {
                $table->string('feature_snippet', 255)->nullable()->after('nearest_landmark');
            }
            if (!Schema::hasColumn('properties', 'ribbon_badge')) {
                $table->string('ribbon_badge', 100)->nullable()->after('feature_snippet');
            }
            if (!Schema::hasColumn('properties', 'is_sold_out')) {
                $table->boolean('is_sold_out')->default(false)->after('rooms_left');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'feature_snippet',
                'ribbon_badge',
                'is_sold_out'
            ]);
        });
    }
};
