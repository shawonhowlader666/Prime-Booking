<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EnsureDefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts with a known password must never exist on a live site.
        if (app()->environment("production")) {
            $this->command?->warn("EnsureDefaultUsersSeeder skipped in production. Use: php artisan users:set-password");
            return;
        }

        // 1. Single Exclusive Super Admin Account (High-Security Password with comma)
        $adminPassword = Hash::make('Prime@Booking,2026');

        User::updateOrCreate(
            ['email' => 'admin.primebooking.bd@gmail.com'],
            [
                'name'              => 'Prime Booking Super Admin',
                'password'          => $adminPassword,
                'role'              => 'super_admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@primeavn.com'],
            [
                'name' => 'Prime Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. Vendor Partner Account
        User::updateOrCreate(
            ['email' => 'vendor@primebooking.com'],
            [
                'name' => 'Hotel Vendor Partner',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'vendor@primeavn.com'],
            [
                'name' => 'Royal Tulip Manager',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'status' => 'active',
            ]
        );

        // 3. Customer Demo Account
        User::updateOrCreate(
            ['email' => 'user@primebooking.com'],
            [
                'name' => 'Customer Demo User',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ]
        );
    }
}
