<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * search_logs — Billion-scale analytics table for trending & personalization.
 *
 * Index strategy:
 *  idx_query_created   → getTrending() GROUP BY query ORDER BY count DESC (last 7 days)
 *  idx_city_created    → getPopularInCity() per destination
 *  idx_user_created    → getPersonalizedSuggestions() per logged-in user
 *  idx_session         → dedup rapid re-searches in same session
 *
 * No foreign key on user_id intentionally → guest searches must also log.
 * Table is append-only: never UPDATE, only INSERT + SELECT.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();

            // ─── Core search params ───────────────────────────────────────
            $table->string('query', 255)->default('');
            $table->string('resolved_city', 100)->nullable();  // Normalized: "Khulna"
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->tinyInteger('guests')->unsigned()->default(1);
            $table->tinyInteger('rooms')->unsigned()->default(1);

            // ─── Result intelligence ──────────────────────────────────────
            $table->smallInteger('result_count')->unsigned()->default(0);
            $table->string('search_type', 30)->default('hotel'); // hotel|houseboat|homestay|transfer
            $table->boolean('resulted_in_booking')->default(false);

            // ─── User identity (privacy-safe) ────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable(); // NO FK — guest support
            $table->string('ip', 45)->nullable();
            $table->string('session_id', 64)->nullable();

            $table->timestamps(); // created_at = search timestamp

            // ─── Performance indexes ──────────────────────────────────────
            // Trending: "SELECT query, COUNT(*) FROM search_logs WHERE created_at > ? GROUP BY query"
            $table->index(['query', 'created_at'],    'idx_query_created');

            // City popularity: "SELECT ... WHERE resolved_city = ? AND created_at > ?"
            $table->index(['resolved_city', 'created_at'], 'idx_city_created');

            // Personalization: "SELECT ... WHERE user_id = ? ORDER BY created_at DESC"
            $table->index(['user_id', 'created_at'],  'idx_user_created');

            // Dedup check: "WHERE session_id = ? AND query = ? AND created_at > ?"
            $table->index(['session_id'],              'idx_session');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
