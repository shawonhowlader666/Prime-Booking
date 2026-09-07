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
        // 1. Hotels (Tenants)
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 20)->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Bangladesh');
            $table->string('currency', 10)->default('BDT');
            $table->decimal('tax_percentage', 5, 2)->default(15.00);
            $table->decimal('service_charge_percentage', 5, 2)->default(5.00);
            $table->time('check_in_time')->default('14:00:00');
            $table->time('check_out_time')->default('12:00:00');
            $table->string('sync_api_key', 64)->nullable()->unique();
            $table->string('sync_webhook_secret', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // 2. Add Hotel & Role columns to Users table (if not already present)
        if (!Schema::hasColumn('users', 'hotel_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('hotel_id')->nullable()->after('id')->constrained('hotels')->nullOnDelete();
                $table->string('role', 30)->default('HOTEL_OWNER')->after('email');
                $table->string('phone', 30)->nullable()->after('role');
                $table->boolean('is_active')->default(true)->after('phone');

                $table->index(['hotel_id', 'role']);
            });
        }

        // 3. Room Types
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 30);
            $table->decimal('base_price', 12, 2);
            $table->unsignedSmallInteger('max_adults')->default(2);
            $table->unsignedSmallInteger('max_children')->default(1);
            $table->json('amenities')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hotel_id', 'code']);
            $table->index(['hotel_id', 'is_active']);
        });

        // 4. Rooms (Physical Inventory Units)
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('room_number', 30);
            $table->string('floor', 20)->default('1');
            $table->string('status', 30)->default('AVAILABLE'); // AVAILABLE, OCCUPIED, DIRTY, CLEANING, MAINTENANCE, OUT_OF_ORDER
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hotel_id', 'room_number']);
            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'room_type_id']);
        });

        // 5. Rate Plans & Seasonal Pricing
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price_per_night', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'room_type_id', 'start_date', 'end_date']);
        });

        // 6. Guests (Customer CRM)
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('id_type', 30)->default('NID'); // NID, PASSPORT, DRIVING_LICENSE
            $table->text('id_number_encrypted')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'phone']);
            $table->index(['hotel_id', 'email']);
            $table->index(['hotel_id', 'is_vip']);
        });

        // 7. Reservations (Bookings)
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('reservation_code', 32)->unique();
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedSmallInteger('adults')->default(1);
            $table->unsignedSmallInteger('children')->default(0);
            $table->string('status', 30)->default('CONFIRMED'); // CONFIRMED, CHECKED_IN, CHECKED_OUT, CANCELLED, NO_SHOW
            $table->string('source', 30)->default('DIRECT_WALKIN'); // DIRECT_WALKIN, PRIME_BOOKING_OTA, PHONE, CORPORATE
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance_amount', 12, 2)->default(0.00);
            $table->text('special_requests')->nullable();
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();

            // Critical Composite Indexes for sub-millisecond timeline queries
            $table->index(['hotel_id', 'check_in_date', 'check_out_date', 'status'], 'idx_pms_res_dates_status');
            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'room_id']);
        });

        // 8. Folios (Billing Accounts per Stay)
        Schema::create('folios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('folio_number', 32)->unique();
            $table->decimal('total_charges', 12, 2)->default(0.00);
            $table->decimal('total_payments', 12, 2)->default(0.00);
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->string('status', 30)->default('OPEN'); // OPEN, SETTLED, VOID
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
        });

        // 9. Folio Items (Charges: Room, Food, Laundry, POS)
        Schema::create('folio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('folio_id')->constrained('folios')->cascadeOnDelete();
            $table->string('item_type', 30); // ROOM_CHARGE, FOOD_BEVERAGE, LAUNDRY, MINIBAR, SPA, EXTRA_BED, SERVICE_CHARGE, TAX, DISCOUNT, OTHER
            $table->string('description');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_amount', 12, 2);
            $table->foreignId('posted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->useCurrent();
            $table->timestamps();

            $table->index(['hotel_id', 'folio_id']);
        });

        // 10. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('folio_id')->constrained('folios')->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('payment_method', 30); // CASH, CARD_POS, BKASH, NAGAD, BANK_TRANSFER, ONLINE
            $table->decimal('amount', 12, 2);
            $table->string('transaction_reference', 100)->nullable();
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->string('status', 30)->default('SUCCESS'); // SUCCESS, REFUNDED, VOID
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['hotel_id', 'created_at']);
        });

        // 11. Housekeeping Tasks
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority', 20)->default('MEDIUM'); // LOW, MEDIUM, HIGH, URGENT
            $table->string('status', 30)->default('PENDING'); // PENDING, IN_PROGRESS, INSPECTED, COMPLETED
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'room_id']);
        });

        // 12. Audit Logs (Financial & Security Integrity)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50); // CREATED, UPDATED, DELETED, CHECK_IN, CHECK_OUT, PAYMENT_RECEIVED
            $table->string('entity_type', 100);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['hotel_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // 13. Sync Events (Prime Booking Webhooks & Real-time Integration)
        Schema::create('sync_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('event_type', 50); // INVENTORY_SYNC, RATE_SYNC, BOOKING_SYNC, CANCELLATION_SYNC
            $table->json('payload');
            $table->string('status', 30)->default('PENDING'); // PENDING, SUCCESS, FAILED
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->text('response_body')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_events');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('housekeeping_tasks');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('folio_items');
        Schema::dropIfExists('folios');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('rate_plans');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
            $table->dropColumn(['hotel_id', 'role', 'phone', 'is_active']);
        });
        Schema::dropIfExists('hotels');
    }
};
