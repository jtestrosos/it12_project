<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the synchronization logic by creating a dummy patient';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Sync Test...');

        $email = 'test_sync_' . Str::random(5) . '@example.com';
        $this->info("Creating dummy patient with email: {$email}");

        try {
            $patient = Patient::create([
                'name' => 'Test Sync Patient',
                'email' => $email,
                'password' => bcrypt('password'),
                'gender' => 'male',
                'birth_date' => '1990-01-01',
                'age' => 30, // Approximate
                'phone' => '09123456789',
                'address' => 'Test Address',
                'barangay' => 'Barangay 11',
                'purok' => 'Purok 1',
            ]);

            $this->info("Patient created locally with ID: {$patient->id}");
            $this->info("Check your 'laravel.log' to see if 'Syncable: patient created locally' was logged.");
            $this->info("If your queue worker is running, check if it processed 'App\Jobs\SyncModelJob'.");
            $this->info("If configured correctly, check your ONLINE database for this patient.");

        } catch (\Exception $e) {
            $this->error("Error creating patient: " . $e->getMessage());
        }
    }
}
