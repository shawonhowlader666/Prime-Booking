<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'coupon_code')) {
                $table->string('coupon_code', 50)->nullable()->after('special_requests');
            }
            if (!Schema::hasColumn('bookings', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('coupon_code');
            }
            if (!Schema::hasColumn('bookings', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(10.00)->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'commission_amount')) {
                $table->decimal('commission_amount', 10, 2)->default(0.00)->after('commission_rate');
            }
            if (!Schema::hasColumn('bookings', 'vendor_payout_amount')) {
                $table->decimal('vendor_payout_amount', 10, 2)->default(0.00)->after('commission_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $cols = ['coupon_code', 'discount_amount', 'commission_rate', 'commission_amount', 'vendor_payout_amount'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
