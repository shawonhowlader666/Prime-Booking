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
        if (!Schema::hasColumn('properties', 'video_url')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('video_url')->nullable()->after('primary_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('properties', 'video_url')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('video_url');
            });
        }
    }
};
