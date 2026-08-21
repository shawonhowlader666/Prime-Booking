<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('accounting_ledgers')) {
            Schema::create('accounting_ledgers', function (Blueprint $table) {
                $table->id();
                $table->string('txn_reference', 64)->unique()->index();
                $table->enum('type', ['credit', 'debit', 'commission', 'payout', 'refund', 'gateway_fee'])->index();
                $table->string('category', 64)->index(); // hotel_booking, tour_booking, vendor_settlement, gateway_deduction, refund
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('gross_amount', 14, 2)->default(0.00);
                $table->decimal('commission_amount', 14, 2)->default(0.00);
                $table->decimal('gateway_fee', 14, 2)->default(0.00);
                $table->decimal('net_amount', 14, 2)->default(0.00);
                $table->string('payment_method', 32)->nullable()->index(); // bkash, nagad, card, sslcommerz, cash
                $table->string('currency', 8)->default('BDT');
                $table->enum('status', ['completed', 'pending', 'cancelled', 'reconciled'])->default('completed')->index();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                // High-Speed Big Data Composite Indexes
                $table->index(['created_at', 'type', 'status'], 'idx_ledger_analytics');
                $table->index(['vendor_id', 'status', 'created_at'], 'idx_ledger_vendor');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ledgers');
    }
};
