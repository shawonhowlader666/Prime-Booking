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
        // 1. Promocodes & Coupons
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->nullOnDelete();
            $table->string('code', 50);
            $table->string('discount_type', 20)->default('FLAT'); // FLAT, PERCENT
            $table->decimal('discount_value', 12, 2)->default(0.00);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->string('status', 20)->default('ACTIVE'); // ACTIVE, EXPIRED
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'code']);
            $table->index(['hotel_id', 'status']);
        });

        // 2. Room Facilities
        Schema::create('room_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 3. Bed Types
        Schema::create('bed_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedSmallInteger('capacity')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 4. Room Sizes
        Schema::create('room_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('title', 100);
            $table->decimal('size_sqft', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 5. Booking Types / Channels
        Schema::create('booking_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('channel', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 6. Commissions & Agent Rates
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('agent_name', 100);
            $table->decimal('rate_percentage', 5, 2)->default(0.00);
            $table->string('channel', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 7. Complementary Services
        Schema::create('complementary_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complementary_services');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('booking_types');
        Schema::dropIfExists('room_sizes');
        Schema::dropIfExists('bed_types');
        Schema::dropIfExists('room_facilities');
        Schema::dropIfExists('promocodes');
    }
};
