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
        // 1. Tour Packages
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->string('destination')->nullable()->index();
            $table->integer('duration_days')->default(3);
            $table->integer('duration_nights')->default(2);
            $table->string('days')->nullable()->comment('e.g. 5D/4N or 14 Days');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('price_per_person', 12, 2)->default(0)->index();
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('badge')->nullable()->comment('e.g. Popular, Best Seller');
            $table->string('image_url')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('includes')->nullable()->comment('["Flight", "Hotel", "Breakfast"]');
            $table->json('highlights')->nullable();
            $table->json('itinerary')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('max_seats')->default(20);
            $table->integer('available_seats')->default(20);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Time-Limited Deals
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('image_url')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('link_url')->nullable();
            $table->string('type')->default('hotel')->comment('hotel, flight, package, activity');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. CMS Contents (About, Services, Contact, Policies, Footer text)
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('group')->default('pages');
            $table->longText('content')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });

        // 4. Guest Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('guest_name')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
        });

        // 5. Amenities
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('category')->default('general');
            $table->timestamps();
        });

        // 6. Property Amenities Pivot
        Schema::create('property_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_amenities');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('cms_contents');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('tour_packages');
    }
};
