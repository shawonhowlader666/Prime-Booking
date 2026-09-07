<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Cab and Fleet Management Module.
     */
    public function up(): void
    {
        // 1. Cabs / Fleet Catalog
        Schema::create('cabs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('car_name', 100);
            $table->string('car_number', 50);
            $table->string('car_type', 50)->default('Sedan'); // Sedan, SUV, MPV, Microbus
            $table->string('fuel_type', 50)->default('Petrol'); // Petrol, Diesel, Octane, Hybrid, EV
            $table->unsignedSmallInteger('seat_capacity')->default(4);
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 30)->nullable();
            $table->decimal('base_fare', 12, 2)->default(0.00);
            $table->string('status', 30)->default('AVAILABLE'); // AVAILABLE, ON_TRIP, MAINTENANCE
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'is_active']);
            $table->unique(['hotel_id', 'car_number']);
        });

        // 2. Cab Bookings & Transport Orders
        Schema::create('cab_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('cab_id')->constrained('cabs')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('customer_phone', 30)->nullable();
            $table->date('booking_date');
            $table->string('booking_time', 30)->nullable();
            $table->string('transport_type', 50)->default('PICKUP'); // PICKUP, DROP, SIGHTSEEING, FULL_DAY
            $table->string('pickup_location')->nullable();
            $table->string('drop_location')->nullable();
            $table->decimal('fare_amount', 12, 2)->default(0.00);
            $table->string('payment_status', 30)->default('PENDING'); // PAID_CASH, CHARGED_TO_ROOM, PENDING
            $table->string('status', 30)->default('CONFIRMED'); // CONFIRMED, IN_PROGRESS, COMPLETED, CANCELLED
            $table->string('driver_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'booking_date', 'status']);
            $table->index(['hotel_id', 'reservation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cab_bookings');
        Schema::dropIfExists('cabs');
    }
};
