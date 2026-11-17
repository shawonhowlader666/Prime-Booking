<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Per-room "was" price, so each room card shows its own real discount. */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rooms', 'original_price')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->decimal('original_price', 12, 2)->nullable()->after('price_per_night');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rooms', 'original_price')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('original_price');
            });
        }
    }
};
