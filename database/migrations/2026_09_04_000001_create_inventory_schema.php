<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Item Categories
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->index();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 2. Item Units of Measurement
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->index();
            $table->string('name', 50);
            $table->string('short_code', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 3. Item Suppliers / Vendors
        Schema::create('item_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->index();
            $table->string('name', 100);
            $table->string('company_name', 150)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
        });

        // 4. Inventory Items Catalog
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->index();
            $table->string('name', 150);
            $table->string('sku', 50)->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0.00);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('current_stock', 10, 2)->default(0.00);
            $table->decimal('min_alert_stock', 10, 2)->default(5.00);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'is_active']);
            $table->index(['hotel_id', 'category_id']);
        });

        // 5. Stock Movements & Transactions (Purchases, Issues, Damages)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->index();
            $table->unsignedBigInteger('item_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('type', 30)->default('PURCHASE_IN'); // PURCHASE_IN, ROOM_ISSUE, DAMAGED, ADJUSTMENT
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->decimal('total_cost', 12, 2)->default(0.00);
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'item_id']);
            $table->index(['hotel_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('item_suppliers');
        Schema::dropIfExists('item_units');
        Schema::dropIfExists('item_categories');
    }
};
