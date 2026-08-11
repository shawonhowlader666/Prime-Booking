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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_code')->unique()->comment('bkash, nagad, sslcommerz, stripe');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sandbox')->default(true);
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('merchant_id')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
