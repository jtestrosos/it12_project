<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create Sample Patient
        Patient::create([
            'name' => 'John Doe',
            'email' => 'patient@malasakit.com',
            'password' => Hash::make('password'),
            'gender' => 'male',
            'phone' => '09123456789',
            'address' => '123 Main St',
            'barangay' => 'Barangay 11',
            'purok' => 'Purok 1',
            'birth_date' => '1990-01-01',
            'age' => 35,
            'email_verified_at' => now()
        ]);

        // Common Filipino first names and last names
        $firstNames = [
            'Juan',
            'Maria',
            'Jose',
            'Ana',
            'Pedro',
            'Rosa',
            'Antonio',
            'Carmen',
            'Miguel',
            'Teresa',
            'Francisco',
            'Elena',
            'Luis',
            'Sofia',
            'Carlos',
            'Isabella',
            'Roberto',
            'Patricia',
            'Manuel',
            'Gabriela',
            'Ricardo',
            'Monica',
            'Alberto',
            'Valeria',
            'Fernando',
            'Daniela',
            'Rafael',
            'Camila',
            'Santiago',
            'Valentina',
            'Eduardo',
            'Luciana'
        ];

        $lastNames = [
            'Reyes',
            'Cruz',
            'Santos',
            'Garcia',
            'Ramos',
            'Lopez',
            'Mendoza',
            'Flores',
            'Torres',
            'Rivera',
            'Castillo',
            'Vargas',
            'Morales',
            'Del Rosario',
            'Bautista',
            'Aquino',
            'Fernandez',
            'Gonzales',
            'Villanueva',
            'Medina'
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

            Patient::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => 'patient_brgy12_' . $i . '@malasakit.com',
                'password' => Hash::make('password'),
                'gender' => $gender,
                'phone' => '0912345' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => $i . ' Sample Street',
                'birth_date' => $birthdate->format('Y-m-d'),
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

            Patient::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => 'patient_brgy11_' . $i . '@malasakit.com',
                'password' => Hash::make('password'),
                'gender' => $gender,
                'phone' => '0913345' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => $i . ' Sample Avenue',
                'birth_date' => $birthdate->format('Y-m-d'),
                'age' => $age,
                'barangay' => 'Barangay 11',
                'purok' => $purok,
                'email_verified_at' => now()
            ]);
        }
    }
}
