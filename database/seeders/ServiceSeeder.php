<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // ISO-8601: Monday=1 ... Sunday=7
        $map = [
            1 => ['General Checkup', 'Comprehensive health checkups and consultation for all ages.'],
            2 => ['Prenatal', 'Regular checkups and guidance for a healthy pregnancy.'],
            3 => ['Medical Check-up', 'Routine assessments to monitor and maintain your health.'],
            4 => ['Immunization', 'Vaccinations for preventable diseases in children and adults.'],
            5 => ['Family Planning', 'Counseling and services for reproductive health.'],
        ];

        foreach ($map as $day => [$name, $desc]) {
            Service::updateOrCreate(
                ['day_of_week' => $day],
                ['name' => $name, 'description' => $desc, 'active' => true]
            );
        }
    }
}


