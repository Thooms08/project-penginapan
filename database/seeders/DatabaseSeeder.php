<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin dummy data
        User::firstOrCreate(
            ['email' => 'admin@penginapan.com'],
            [
                'name'              => 'Admin',
                'email'             => 'admin@penginapan.com',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@penginapan.com'],
            [
                'name'              => 'Manager',
                'email'             => 'manager@penginapan.com',
                'password'          => Hash::make('manager123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
