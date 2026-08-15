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
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'view_type')) {
                $table->string('view_type', 100)->nullable()->default('City View')->after('bed_type');
            }
            if (!Schema::hasColumn('rooms', 'bathroom_count')) {
                $table->integer('bathroom_count')->default(1)->after('view_type');
            }
            if (!Schema::hasColumn('rooms', 'bathroom_features')) {
                $table->json('bathroom_features')->nullable()->after('bathroom_count');
            }
            if (!Schema::hasColumn('rooms', 'smoking_policy')) {
                $table->string('smoking_policy', 50)->default('Non-Smoking')->after('bathroom_features');
            }
            if (!Schema::hasColumn('rooms', 'balcony_type')) {
                $table->string('balcony_type', 100)->nullable()->default('Private Balcony')->after('smoking_policy');
            }
            if (!Schema::hasColumn('rooms', 'extra_bed_allowed')) {
                $table->boolean('extra_bed_allowed')->default(false)->after('balcony_type');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'total_floors')) {
                $table->integer('total_floors')->nullable()->after('rooms_left');
            }
            if (!Schema::hasColumn('properties', 'total_rooms_count')) {
                $table->integer('total_rooms_count')->nullable()->after('total_floors');
            }
            if (!Schema::hasColumn('properties', 'year_built')) {
                $table->integer('year_built')->nullable()->after('total_rooms_count');
            }
            if (!Schema::hasColumn('properties', 'languages_spoken')) {
                $table->json('languages_spoken')->nullable()->after('year_built');
            }
            if (!Schema::hasColumn('properties', 'pets_policy')) {
                $table->string('pets_policy', 100)->nullable()->default('Pets Not Allowed')->after('languages_spoken');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['view_type', 'bathroom_count', 'bathroom_features', 'smoking_policy', 'balcony_type', 'extra_bed_allowed']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['total_floors', 'total_rooms_count', 'year_built', 'languages_spoken', 'pets_policy']);
        });
    }
};
