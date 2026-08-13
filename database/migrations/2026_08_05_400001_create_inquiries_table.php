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
        if (!Schema::hasTable('inquiries')) {
            Schema::create('inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('service_type')->default('General Inquiry');
                $table->string('destination')->nullable();
                $table->string('travel_date')->nullable();
                $table->integer('passengers')->default(1);
                $table->text('message')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();

                $table->index(['service_type', 'status']);
                $table->index(['phone', 'email']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
