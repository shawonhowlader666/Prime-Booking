<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payouts')) {
            Schema::create('payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('vendor_name')->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('payment_method')->default('bKash');
                $table->string('account_details')->nullable();
                $table->string('reference_number')->nullable();
                $table->string('status')->default('pending'); // pending, paid, rejected
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['vendor_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
