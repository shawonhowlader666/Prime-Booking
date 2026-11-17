<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * PRIME10 and PRIMECASH8 used to be created by CouponService whenever someone typed
 * them, so admins could not edit or switch them off. They are now ordinary coupons:
 * inserted once here (only if missing) and managed from Admin > Coupons like any other.
 */
return new class extends Migration
{
    public function up(): void
    {
        $codes = [
            'PRIME10'    => 10.0,
            'PRIMECASH8' => 8.0,
        ];

        foreach ($codes as $code => $percent) {
            if (DB::table('coupons')->where('code', $code)->exists()) {
                continue;
            }
            DB::table('coupons')->insert([
                'code'       => $code,
                'type'       => 'percentage',
                'amount'     => $percent,
                'min_spend'  => 500,
                'status'     => 'active',
                'used_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Coupons may have been edited or used since; never delete them on rollback.
    }
};
