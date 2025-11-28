<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create Admin
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@malasakit.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);
    }
}
