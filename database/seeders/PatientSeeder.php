<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Common Filipino first names and last names
        $firstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Antonio', 'Carmen',
            'Miguel', 'Teresa', 'Francisco', 'Elena', 'Luis', 'Sofia', 'Carlos', 'Isabella',
            'Roberto', 'Patricia', 'Manuel', 'Gabriela', 'Ricardo', 'Monica', 'Alberto', 'Valeria',
            'Fernando', 'Daniela', 'Rafael', 'Camila', 'Santiago', 'Valentina', 'Eduardo', 'Luciana',
            'Javier', 'Martina', 'Daniel', 'Victoria', 'Alejandro', 'Jimena', 'Sebastian', 'Renata',
            'Nicolas', 'Adriana', 'Diego', 'Beatriz', 'Andres', 'Paula', 'Felipe', 'Mariana'
        ];

        $lastNames = [
            'Reyes', 'Cruz', 'Santos', 'Garcia', 'Ramos', 'Lopez', 'Mendoza', 'Flores',
            'Torres', 'Rivera', 'Castillo', 'Vargas', 'Morales', 'Del Rosario', 'Bautista',
            'Aquino', 'Fernandez', 'Gonzales', 'Villanueva', 'Medina', 'Diaz', 'Paredes',
            'Santiago', 'Navarro', 'Salazar', 'Molina', 'Castro', 'Ortiz', 'De Leon', 'Velasco'
        ];

        // Purok options for barangays
        $puroks = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'];

        // Generate patients for Barangay 12
        for ($i = 1; $i <= 25; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $purok = $puroks[array_rand($puroks)];
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => 'patient_brgy12_' . $i . '@malasakit.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'barangay' => '12',
                'purok' => $purok,
                'email_verified_at' => now()
            ]);
        }

        // Generate patients for Barangay 11
        for ($i = 1; $i <= 25; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $purok = $puroks[array_rand($puroks)];
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => 'patient_brgy11_' . $i . '@malasakit.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'barangay' => '11',
                'purok' => $purok,
                'email_verified_at' => now()
            ]);
        }
    }
}
