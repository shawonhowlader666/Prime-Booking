<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'vendor_reply')) {
                $table->text('vendor_reply')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('reviews', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('vendor_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'replied_at')) {
                $table->dropColumn('replied_at');
            }
            if (Schema::hasColumn('reviews', 'vendor_reply')) {
                $table->dropColumn('vendor_reply');
            }
        });
    }
};
