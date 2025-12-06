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
        $password = Hash::make('password');
        $now = now();
        $patients = [];

        // Create Sample Patient
        $patients[] = [
            'name' => 'John Doe',
            'email' => 'patient@malasakit.com',
            'password' => $password,
            'gender' => 'male',
            'phone' => '09123456789',
            'address' => '123 Main St',
            'barangay' => 'Barangay 11',
            'purok' => 'Purok 1',
            'birth_date' => '1990-01-01',
            'age' => 35,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

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

        // Generate all possible unique names first to avoid duplicates
        $uniqueNames = [];
        foreach ($firstNames as $firstName) {
            foreach ($lastNames as $lastName) {
                $uniqueNames[] = [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                ];
            }
        }

        // Shuffle to randomize
        shuffle($uniqueNames);

        // We need 50 names total (25 for each loop)
        // Check if we have enough names (32 * 20 = 640 combinations, so we are safe)
        if (count($uniqueNames) < 50) {
            $this->command->warn('Not enough unique name combinations for the requested number of patients.');
        }

        // Pointer for unique names
        $uniqueNameIndex = 0;

        $genders = ['male', 'female'];
        $puroks = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'];

        // Generate patients for Barangay 12
        for ($i = 1; $i <= 25; $i++) {
            $nameData = $uniqueNames[$uniqueNameIndex++];
            $name = $nameData['firstName'] . ' ' . $nameData['lastName'];
            $emailName = strtolower(str_replace(' ', '', $name));
            $purok = $puroks[array_rand($puroks)];
            $gender = $genders[array_rand($genders)];

            $age = rand(18, 80);
            $birthdate = $now->copy()->subYears($age)->subDays(rand(0, 364));

            $patients[] = [
                'name' => $name,
                'email' => $emailName . '@malasakit.com',
                'password' => $password,
                'gender' => $gender,
                'phone' => '0912345' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => $i . ' Sample Street',
                'barangay' => 'Barangay 12',
                'purok' => $purok,
                'birth_date' => $birthdate->format('Y-m-d'),
                'age' => $age,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Generate patients for Barangay 11
        for ($i = 1; $i <= 25; $i++) {
            $nameData = $uniqueNames[$uniqueNameIndex++];
            $name = $nameData['firstName'] . ' ' . $nameData['lastName'];
            $emailName = strtolower(str_replace(' ', '', $name));
            $purok = $puroks[array_rand($puroks)];
            $gender = $genders[array_rand($genders)];

            $age = rand(18, 80);
            $birthdate = $now->copy()->subYears($age)->subDays(rand(0, 364));

            $patients[] = [
                'name' => $name,
                'email' => $emailName . '@malasakit.com',
                'password' => $password,
                'gender' => $gender,
                'phone' => '0913345' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => $i . ' Sample Avenue',
                'barangay' => 'Barangay 11',
                'purok' => $purok,
                'birth_date' => $birthdate->format('Y-m-d'),
                'age' => $age,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Chunk insert to avoid hitting query size limits if any
        foreach (array_chunk($patients, 50) as $chunk) {
            Patient::insert($chunk);
        }
    }
}
