<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotel_payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('gateway', 50); // STRIPE, PAYPAL, RAZORPAY, SSLCOMMERZ, BANK_TRANSFER, CASH_POS
            $table->boolean('is_enabled')->default(false);
            $table->string('environment', 20)->default('sandbox'); // sandbox, live
            $table->string('currency_code', 10)->default('USD');
            $table->string('endpoint_url')->nullable();
            $table->text('publishable_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->json('additional_config')->nullable();
            $table->timestamps();

            $table->unique(['hotel_id', 'gateway']);
            $table->index(['hotel_id', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_payment_gateways');
    }
};
