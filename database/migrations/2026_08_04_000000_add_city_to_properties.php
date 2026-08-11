<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add city column to properties table (extracted from address field for indexing)
 * Also add status column if it uses 'published' convention
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'city')) {
                $table->dropColumn('city');
            }
        });
    }
};
