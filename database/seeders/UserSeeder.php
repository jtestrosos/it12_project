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

        // Common Filipino first names and last names
        $firstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Antonio', 'Carmen',
            'Miguel', 'Teresa', 'Francisco', 'Elena', 'Luis', 'Sofia', 'Carlos', 'Isabella',
            'Roberto', 'Patricia', 'Manuel', 'Gabriela', 'Ricardo', 'Monica', 'Alberto', 'Valeria',
            'Fernando', 'Daniela', 'Rafael', 'Camila', 'Santiago', 'Valentina', 'Eduardo', 'Luciana'
        ];

        $lastNames = [
            'Reyes', 'Cruz', 'Santos', 'Garcia', 'Ramos', 'Lopez', 'Mendoza', 'Flores',
            'Torres', 'Rivera', 'Castillo', 'Vargas', 'Morales', 'Del Rosario', 'Bautista',
            'Aquino', 'Fernandez', 'Gonzales', 'Villanueva', 'Medina'
        ];

        $genders = ['male', 'female'];
        $puroks = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'];

        // Generate patients for Barangay 12
        for ($i = 1; $i <= 25; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $purok = $puroks[array_rand($puroks)];
            $gender = $genders[array_rand($genders)];
            
            // Generate random birthdate (between 18 and 80 years old)
            $age = rand(18, 80);
            $birthdate = now()->subYears($age)->subDays(rand(0, 364));
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => 'patient_brgy12_' . $i . '@malasakit.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'gender' => $gender,
                'birthdate' => $birthdate->format('Y-m-d'),
                'age' => $age,
                'barangay' => 'Barangay 12',
                'purok' => $purok,
                'email_verified_at' => now()
            ]);
        }

        // Generate patients for Barangay 11
        for ($i = 1; $i <= 25; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $purok = $puroks[array_rand($puroks)];
            $gender = $genders[array_rand($genders)];
            
            // Generate random birthdate (between 18 and 80 years old)
            $age = rand(18, 80);
            $birthdate = now()->subYears($age)->subDays(rand(0, 364));
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => 'patient_brgy11_' . $i . '@malasakit.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'gender' => $gender,
                'birthdate' => $birthdate->format('Y-m-d'),
                'age' => $age,
                'barangay' => 'Barangay 11',
                'purok' => $purok,
                'email_verified_at' => now()
            ]);
        }
    }
}
