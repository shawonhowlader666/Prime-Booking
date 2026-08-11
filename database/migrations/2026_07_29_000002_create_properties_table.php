<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('Hotel'); // Hotel, Resort, Villa, Apartment
            $table->integer('star_rating')->default(3);
            $table->decimal('rating_score', 3, 1)->default(8.5);
            $table->integer('total_reviews')->default(0);
            $table->text('address');
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('primary_image')->nullable();
            $table->json('images')->nullable();
            $table->json('amenities')->nullable(); // ['WiFi', 'Pool', 'Parking', 'Breakfast', 'Spa']
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
