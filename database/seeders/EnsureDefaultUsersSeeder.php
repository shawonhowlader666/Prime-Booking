<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EnsureDefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin Account
        User::updateOrCreate(
            ['email' => 'admin@primebooking.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@primeavn.com'],
            [
                'name' => 'Prime Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. Vendor Partner Account
        User::updateOrCreate(
            ['email' => 'vendor@primebooking.com'],
            [
                'name' => 'Hotel Vendor Partner',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'vendor@primeavn.com'],
            [
                'name' => 'Royal Tulip Manager',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'status' => 'active',
            ]
        );

        // 3. Customer Demo Account
        User::updateOrCreate(
            ['email' => 'user@primebooking.com'],
            [
                'name' => 'Customer Demo User',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'status' => 'active',
            ]
        );
    }
}
