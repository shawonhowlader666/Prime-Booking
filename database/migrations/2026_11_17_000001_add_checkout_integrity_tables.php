<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Checkout holds live in the database (not in cache counters) so they
        // expire on their own and can never leak inventory permanently.
        if (!Schema::hasTable('room_holds')) {
            Schema::create('room_holds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('room_id');
                $table->string('token', 64)->unique();
                $table->date('check_in');
                $table->date('check_out');
                $table->unsignedInteger('rooms_count')->default(1);
                $table->timestamp('expires_at');
                $table->timestamps();

                $table->index(['room_id', 'expires_at', 'check_in', 'check_out'], 'idx_holds_room_window');
                $table->index('expires_at', 'idx_holds_expiry');
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'transaction_id')) {
                $table->string('transaction_id', 128)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('bookings', 'vip_discount_amount')) {
                $table->decimal('vip_discount_amount', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'points_redeemed')) {
                $table->unsignedInteger('points_redeemed')->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'points_discount_amount')) {
                $table->decimal('points_discount_amount', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'addons_total')) {
                $table->decimal('addons_total', 10, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('bookings', 'payment_expires_at')) {
                $table->timestamp('payment_expires_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
        });

        // Availability lookups: every active booking overlapping a date window for one room.
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['room_id', 'status', 'check_in', 'check_out'], 'idx_bk_room_status_window');
            $table->index(['status', 'payment_status', 'payment_expires_at'], 'idx_bk_pending_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bk_room_status_window');
            $table->dropIndex('idx_bk_pending_expiry');
        });

        Schema::table('bookings', function (Blueprint $table) {
            foreach (['transaction_id', 'vip_discount_amount', 'points_redeemed', 'points_discount_amount', 'addons_total', 'payment_expires_at', 'cancelled_at'] as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('room_holds');
    }
};
