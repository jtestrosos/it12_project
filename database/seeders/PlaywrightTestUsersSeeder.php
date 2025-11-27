<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PlaywrightTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test users for Playwright E2E testing
     */
    public function run(): void
    {
        // Create Patient Test User
        User::firstOrCreate(
            ['email' => 'patient@test.com'],
            [
                'name' => 'Test Patient',
                'email' => 'patient@test.com',
                'password' => Hash::make('Patient@123'),
                'role' => 'user',
                'gender' => 'male',
                'phone' => '09123456789',
                'address' => '123 Test Street',
                'barangay' => 'Barangay 11',
                'purok' => 'Purok 1',
                'birth_date' => '1990-01-01',
                'age' => 34,
            ]
        );

        // Create Admin Test User
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'gender' => 'female',
                'phone' => '09123456788',
                'address' => '456 Admin Avenue',
                'barangay' => 'Barangay 12',
                'purok' => 'Purok 2',
                'birth_date' => '1985-05-15',
                'age' => 39,
            ]
        );

        // Create Super Admin Test User
        User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name' => 'Test Super Admin',
                'email' => 'superadmin@test.com',
                'password' => Hash::make('SuperAdmin@123'),
                'role' => 'superadmin',
                'gender' => 'male',
                'phone' => '09123456787',
                'address' => '789 Super Admin Boulevard',
                'barangay' => 'Barangay 11',
                'purok' => 'Purok 3',
                'birth_date' => '1980-12-25',
                'age' => 44,
            ]
        );

        $this->command->info('Playwright test users created successfully!');
        $this->command->info('Patient: patient@test.com / Patient@123');
        $this->command->info('Admin: admin@test.com / Admin@123');
        $this->command->info('Super Admin: superadmin@test.com / SuperAdmin@123');
    }
}
