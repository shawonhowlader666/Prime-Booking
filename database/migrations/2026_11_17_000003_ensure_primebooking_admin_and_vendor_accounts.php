<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * This migration used to create admin, vendor and customer accounts with the
     * password "password" on every environment, including production.
     *
     * Staff accounts are now created explicitly with:
     *   php artisan users:set-password admin@example.com --role=admin
     *
     * Kept as a no-op so environments where it already ran stay consistent.
     */
    public function up(): void
    {
    }

    public function down(): void
    {
    }
};
