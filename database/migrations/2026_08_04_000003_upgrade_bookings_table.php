<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Booking table upgrade — adds missing columns needed by BookingFlowController
 * (guests, price_per_night, subtotal, tax_amount, total_price, status, payment_method)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add guests column (replaces adults+children for simplicity)
            if (!Schema::hasColumn('bookings', 'guests')) {
                $table->integer('guests')->default(2)->after('check_out');
            }
            // Price breakdown columns
            if (!Schema::hasColumn('bookings', 'price_per_night')) {
                $table->decimal('price_per_night', 10, 2)->nullable()->after('guests');
            }
            if (!Schema::hasColumn('bookings', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->nullable()->after('price_per_night');
            }
            if (!Schema::hasColumn('bookings', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 10, 2)->nullable()->after('tax_amount');
            }
            // Unified status column (maps booking_status)
            if (!Schema::hasColumn('bookings', 'status')) {
                $table->string('status', 30)->default('pending')->after('total_price');
            }
            // Payment method (bkash, nagad, card, cash)
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guests','price_per_night','subtotal','tax_amount','total_price','status','payment_method']);
        });
    }
};
