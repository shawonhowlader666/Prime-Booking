<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure both Prime Booking and legacy Prime Aviation admin and vendor credentials
     * are perpetually present in the database so login never fails across deployments.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        // 1. Primary Admin Accounts across all domain aliases
        $adminEmails = [
            'admin@primebooking.com',
            'admin@primebooking.com.bd',
            'admin@primeavn.com',
            'admin@primeaviation.com',
        ];

        foreach ($adminEmails as $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'     => 'Super Admin',
                    'password' => Hash::make('password'),
                    'role'     => 'admin',
                    'status'   => 'active',
                ]
            );
        }

        // 2. Vendor Accounts across all domain aliases
        $vendorEmails = [
            'vendor@primebooking.com',
            'vendor@primebooking.com.bd',
            'vendor@primeavn.com',
            'vendor@primeaviation.com',
        ];

        foreach ($vendorEmails as $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'     => 'Hotel Vendor Partner',
                    'password' => Hash::make('password'),
                    'role'     => 'vendor',
                    'status'   => 'active',
                ]
            );
        }

        // 3. Demo Customer Account
        User::updateOrCreate(
            ['email' => 'user@primebooking.com'],
            [
                'name'     => 'Customer Demo User',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'status'   => 'active',
            ]
        );
    }

    public function down(): void
    {
        // No destructive undo
    }
};
