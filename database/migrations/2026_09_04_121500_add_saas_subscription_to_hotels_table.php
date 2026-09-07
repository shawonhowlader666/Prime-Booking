<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'subscription_plan')) {
                $table->string('subscription_plan')->default('STANDARD')->after('is_active');
                $table->string('subscription_status')->default('ACTIVE')->after('subscription_plan'); // ACTIVE, TRIAL, EXPIRED, SUSPENDED
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
                $table->decimal('monthly_fee', 10, 2)->default(1000.00)->after('subscription_expires_at');
                $table->string('prime_booking_property_id')->nullable()->after('monthly_fee');
                $table->boolean('is_subscription_locked')->default(false)->after('prime_booking_property_id');
            }
        });

        if (!Schema::hasTable('tenant_subscriptions')) {
            Schema::create('tenant_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
                $table->string('plan_name')->default('STANDARD');
                $table->decimal('amount', 10, 2)->default(1000.00);
                $table->string('currency', 10)->default('BDT');
                $table->string('payment_method')->default('ONLINE_GATEWAY');
                $table->string('transaction_reference')->nullable();
                $table->string('status')->default('PAID'); // PAID, PENDING, FAILED
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan',
                'subscription_status',
                'subscription_expires_at',
                'monthly_fee',
                'prime_booking_property_id',
                'is_subscription_locked',
            ]);
        });
    }
};
