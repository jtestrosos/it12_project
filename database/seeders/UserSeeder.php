<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@malasakit.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now()
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@malasakit.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        // Create Sample Patient
        User::create([
            'name' => 'John Doe',
            'email' => 'patient@malasakit.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now()
        ]);
    }
}
