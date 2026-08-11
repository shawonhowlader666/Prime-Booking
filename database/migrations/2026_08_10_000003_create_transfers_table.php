<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airport_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->string('vehicle_type')->default('Sedan');
            $table->decimal('price', 10, 2);
            $table->integer('capacity')->default(4);
            $table->integer('luggage_capacity')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('transfer_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('transfer_id')->nullable()->constrained('airport_transfers')->onDelete('set null');
            $table->string('passenger_name');
            $table->string('passenger_phone');
            $table->string('passenger_email');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->dateTime('pickup_datetime');
            $table->string('flight_number')->nullable();
            $table->integer('passengers')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_bookings');
        Schema::dropIfExists('airport_transfers');
    }
};
