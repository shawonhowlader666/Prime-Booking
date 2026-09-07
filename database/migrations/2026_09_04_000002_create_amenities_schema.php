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
        // 1. Amenity Services Catalog
        Schema::create('amenity_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('category', 50)->index(); // JACUZZI, SPA, SWIMMING_POOL, PEDICURE, THEATER, BANQUET
            $table->string('name', 150);
            $table->integer('duration_minutes')->default(60);
            $table->decimal('price', 12, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->default(15.00);
            $table->text('description')->nullable();
            $table->integer('capacity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Amenity Service Bookings / Orders
        Schema::create('amenity_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('amenity_services')->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->date('booked_for_date');
            $table->string('time_slot', 50)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_status', 30)->default('PENDING'); // CHARGED_TO_ROOM, PAID_CASH, PAID_CARD, PENDING
            $table->string('status', 30)->default('CONFIRMED'); // CONFIRMED, COMPLETED, CANCELLED
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_orders');
        Schema::dropIfExists('amenity_services');
    }
};
