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
        if (!Schema::hasTable('tour_packages')) {
            Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('destination')->index();
            $table->integer('duration_days')->default(3);
            $table->integer('duration_nights')->default(2);
            $table->decimal('price_per_person', 12, 2)->index();
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('featured_image');
            $table->json('gallery_images')->nullable();
            $table->json('inclusions')->nullable(); // ['Hotel Stay', 'AC Bus', 'Breakfast', 'Tour Guide']
            $table->json('highlights')->nullable();
            $table->json('itinerary')->nullable();  // [{'day': 1, 'title': 'Arrival & Beach Walk', 'description': '...'}]
            $table->string('status')->default('active')->index(); // 'active', 'pending', 'inactive'
            $table->integer('max_seats')->default(20);
            $table->integer('available_seats')->default(20);
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
