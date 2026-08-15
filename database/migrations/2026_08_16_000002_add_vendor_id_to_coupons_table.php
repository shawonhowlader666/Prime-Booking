<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                if (!Schema::hasColumn('coupons', 'vendor_id')) {
                    $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('coupons', 'property_id')) {
                    $table->unsignedBigInteger('property_id')->nullable()->after('vendor_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                if (Schema::hasColumn('coupons', 'vendor_id')) {
                    $table->dropColumn('vendor_id');
                }
                if (Schema::hasColumn('coupons', 'property_id')) {
                    $table->dropColumn('property_id');
                }
            });
        }
    }
};
