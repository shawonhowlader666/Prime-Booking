<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'helpful_count')) {
                $table->unsignedInteger('helpful_count')->default(0)->after('comment');
            }
            if (!Schema::hasColumn('reviews', 'unhelpful_count')) {
                $table->unsignedInteger('unhelpful_count')->default(0)->after('helpful_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'helpful_count')) {
                $table->dropColumn(['helpful_count', 'unhelpful_count']);
            }
        });
    }
};
