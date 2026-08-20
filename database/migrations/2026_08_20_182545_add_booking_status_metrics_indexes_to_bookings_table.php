<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Composite index for fast user booking history & VIP loyalty calculations
            $table->index(['user_id', 'created_at', 'booking_status'], 'idx_bk_user_created_status');
            $table->index(['guest_email', 'created_at', 'booking_status'], 'idx_bk_email_created_status');
            $table->index(['payment_status', 'total_amount'], 'idx_bk_payment_total');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bk_user_created_status');
            $table->dropIndex('idx_bk_email_created_status');
            $table->dropIndex('idx_bk_payment_total');
        });
    }
};
