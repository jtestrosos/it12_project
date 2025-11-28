<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create Super Admin
        SuperAdmin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@malasakit.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
    }
}
