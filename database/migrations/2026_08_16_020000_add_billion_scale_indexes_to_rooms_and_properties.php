<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!$this->indexExists('rooms', 'idx_rooms_prop_status')) {
                $table->index(['property_id', 'status'], 'idx_rooms_prop_status');
            }
            if (!$this->indexExists('rooms', 'idx_rooms_prop_price')) {
                $table->index(['property_id', 'price_per_night'], 'idx_rooms_prop_price');
            }
            if (!$this->indexExists('rooms', 'idx_rooms_prop_capacity')) {
                $table->index(['property_id', 'max_adults', 'max_children'], 'idx_rooms_prop_capacity');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if (!$this->indexExists('properties', 'idx_props_city_price_rating')) {
                $table->index(['status', 'city', 'price_per_night', 'rating_score'], 'idx_props_city_price_rating');
            }
            if (!$this->indexExists('properties', 'idx_props_status_created')) {
                $table->index(['status', 'created_at'], 'idx_props_status_created');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if ($this->indexExists('rooms', 'idx_rooms_prop_status')) {
                $table->dropIndex('idx_rooms_prop_status');
            }
            if ($this->indexExists('rooms', 'idx_rooms_prop_price')) {
                $table->dropIndex('idx_rooms_prop_price');
            }
            if ($this->indexExists('rooms', 'idx_rooms_prop_capacity')) {
                $table->dropIndex('idx_rooms_prop_capacity');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if ($this->indexExists('properties', 'idx_props_city_price_rating')) {
                $table->dropIndex('idx_props_city_price_rating');
            }
            if ($this->indexExists('properties', 'idx_props_status_created')) {
                $table->dropIndex('idx_props_status_created');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $result = DB::select("PRAGMA index_list('{$table}')");
                foreach ($result as $index) {
                    if ($index->name === $indexName) {
                        return true;
                    }
                }
                return false;
            }

            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Throwable) {
            return false;
        }
    }
};
