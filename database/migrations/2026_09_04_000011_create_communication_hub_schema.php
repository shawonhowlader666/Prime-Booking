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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['hotel_id', 'sender_id']);
            $table->index(['hotel_id', 'receiver_id']);
            $table->index(['hotel_id', 'created_at']);
        });

        Schema::create('internal_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('recipient_email');
            $table->string('subject');
            $table->text('body');
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_trash')->default(false);
            $table->timestamps();

            $table->index(['hotel_id', 'sender_id']);
            $table->index(['hotel_id', 'recipient_email']);
            $table->index(['hotel_id', 'created_at']);
        });

        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('event_type')->default('MEETING');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'user_id']);
            $table->index(['hotel_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
        Schema::dropIfExists('internal_emails');
        Schema::dropIfExists('chat_messages');
    }
};
