<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->string('gateway_code', 32)->index();          // bkash, nagad, sslcommerz, cash
                $table->string('txn_id', 128)->nullable()->index();   // Gateway's own transaction ID
                $table->decimal('amount', 14, 2)->default(0.00);
                $table->string('currency', 8)->default('BDT');
                $table->enum('status', ['initiated', 'verified', 'failed', 'refunded', 'cancelled'])
                      ->default('initiated')->index();
                $table->json('gateway_response')->nullable();         // Full raw API response for dispute resolution
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 512)->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                // Prevent double-processing the same transaction
                $table->unique(['booking_id', 'txn_id'], 'uniq_booking_txn');

                // Composite indexes for admin lookups
                $table->index(['gateway_code', 'status', 'created_at'], 'idx_payment_analytics');
                $table->index(['booking_id', 'status'], 'idx_payment_booking');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

