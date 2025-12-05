<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestTriggersSeeder extends Seeder
{
    /**
     * Run the database seeds to test all triggers.
     */
    public function run(): void
    {
        $this->command->info('🧪 Testing Database Triggers...');
        $this->command->newLine();

        // Test 1: Inventory Status Auto-Update
        $this->testInventoryStatusTrigger();

        // Test 2: Inventory Change Logging
        $this->testInventoryChangeLogging();

        // Test 3: Patient Age Calculation
        $this->testPatientAgeTrigger();

        // Test 4: Prevent Patient Deletion
        $this->testPreventPatientDeletion();

        // Test 5: Prevent Double Booking
        $this->testPreventDoubleBooking();

        // Test 6: Appointment Archival
        $this->testAppointmentArchival();

        $this->command->newLine();
        $this->command->info('✅ All trigger tests completed!');
    }

    private function testInventoryStatusTrigger()
    {
        $this->command->info('📦 Test 1: Inventory Status Auto-Update');

        // Test out_of_stock
        $item = Inventory::create([
            'item_name' => 'Test Item - Out of Stock',
            'category' => 'medicine',
            'current_stock' => 0,
            'minimum_stock' => 10,
            'unit' => 'pieces',
        ]);
        $item->refresh(); // Refresh to get trigger-updated status
        $this->assert($item->status === 'out_of_stock', 'Status should be out_of_stock when stock is 0');

        // Test low_stock
        $item->current_stock = 5;
        $item->save();
        $item->refresh();
        $this->assert($item->status === 'low_stock', 'Status should be low_stock when stock <= minimum');

        // Test in_stock
        $item->current_stock = 50;
        $item->save();
        $item->refresh();
        $this->assert($item->status === 'in_stock', 'Status should be in_stock when stock > minimum');

        // Test expired
        $item->expiry_date = now()->subDays(1);
        $item->save();
        $item->refresh();
        $this->assert($item->status === 'expired', 'Status should be expired when expiry_date is past');

        $this->command->info('   ✅ Inventory status trigger working correctly');
        $this->command->newLine();
    }

    private function testInventoryChangeLogging()
    {
        $this->command->info('📝 Test 2: Inventory Change Logging');

        $item = Inventory::create([
            'item_name' => 'Test Item - Logging',
            'category' => 'medicine',
            'current_stock' => 100,
            'minimum_stock' => 10,
            'unit' => 'pieces',
            'unit_price' => 25.00,
        ]);

        $initialTransactionCount = InventoryTransaction::where('inventory_id', $item->id)->count();

        // Change stock (should create transaction)
        $item->current_stock = 80;
        $item->save();

        $newTransactionCount = InventoryTransaction::where('inventory_id', $item->id)->count();
        $this->assert($newTransactionCount > $initialTransactionCount, 'Transaction should be logged when stock changes');

        $transaction = InventoryTransaction::where('inventory_id', $item->id)->latest()->first();
        $this->assert($transaction->transaction_type === 'usage', 'Transaction type should be usage when stock decreases');
        $this->assert($transaction->quantity === 20, 'Transaction quantity should be 20');

        $this->command->info('   ✅ Inventory change logging trigger working correctly');
        $this->command->newLine();
    }

    private function testPatientAgeTrigger()
    {
        $this->command->info('👤 Test 3: Patient Age Calculation');

        $patient = Patient::create([
            'name' => 'Test Patient Age',
            'email' => 'testage' . time() . '@example.com',
            'password' => bcrypt('password'),
            'phone' => '09123456789',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
            'barangay' => 'Barangay 11',
            'purok' => 'Purok 1',
        ]);
        $patient->refresh(); // Refresh to get trigger-calculated age

        $expectedAge = now()->year - 1990;
        $this->assert($patient->age == $expectedAge, "Age should be auto-calculated as $expectedAge");

        // Update birth_date
        $patient->birth_date = '2000-06-15';
        $patient->save();
        $patient->refresh();

        $expectedAge = now()->year - 2000;
        $this->assert($patient->age == $expectedAge, "Age should update to $expectedAge when birth_date changes");

        $this->command->info('   ✅ Patient age calculation trigger working correctly');
        $this->command->newLine();
    }

    private function testPreventPatientDeletion()
    {
        $this->command->info('🚫 Test 4: Prevent Patient Deletion with Active Appointments');

        $patient = Patient::create([
            'name' => 'Test Patient Delete',
            'email' => 'testdelete' . time() . '@example.com',
            'password' => bcrypt('password'),
            'phone' => '0912345' . substr(time(), -4),
            'gender' => 'female',
            'birth_date' => '1995-03-20',
            'barangay' => 'Barangay 11',
            'purok' => 'Purok 2',
        ]);

        // Create active appointment with unique time
        $uniqueTime = now()->format('H:i') . ':' . str_pad(now()->second, 2, '0', STR_PAD_LEFT);
        Appointment::create([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'patient_phone' => $patient->phone,
            'appointment_date' => now()->addDays(5),
            'appointment_time' => $uniqueTime,
            'service_type' => 'Consultation',
            'status' => 'pending',
        ]);

        $exceptionThrown = false;
        try {
            $patient->forceDelete(); // Use forceDelete() to trigger BEFORE DELETE
        } catch (\Exception $e) {
            $exceptionThrown = true;
            $this->assert(
                str_contains($e->getMessage(), 'Cannot delete patient'),
                'Exception message should mention patient deletion prevention'
            );
        }

        $this->assert($exceptionThrown, 'Exception should be thrown when deleting patient with active appointments');
        $this->command->info('   ✅ Patient deletion prevention trigger working correctly');
        $this->command->newLine();
    }

    private function testPreventDoubleBooking()
    {
        $this->command->info('📅 Test 5: Prevent Double Booking');

        $patient1 = Patient::first();
        $patient2 = Patient::skip(1)->first();

        $appointmentDate = now()->addDays(10);
        $appointmentTime = '14:00:00';

        // Create first appointment
        $appointment1 = Appointment::create([
            'patient_id' => $patient1->id,
            'patient_name' => $patient1->name,
            'patient_phone' => $patient1->phone,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'service_type' => 'Checkup',
            'status' => 'approved',
        ]);

        $this->assert($appointment1->exists, 'First appointment should be created successfully');

        // Try to create second appointment at same time
        $exceptionThrown = false;
        try {
            Appointment::create([
                'patient_id' => $patient2->id,
                'patient_name' => $patient2->name,
                'patient_phone' => $patient2->phone,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'service_type' => 'Consultation',
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            $exceptionThrown = true;
            $this->assert(
                str_contains($e->getMessage(), 'Time slot already booked'),
                'Exception message should mention time slot conflict'
            );
        }

        $this->assert($exceptionThrown, 'Exception should be thrown for double booking');
        $this->command->info('   ✅ Double booking prevention trigger working correctly');
        $this->command->newLine();
    }

    private function testAppointmentArchival()
    {
        $this->command->info('🗄️  Test 6: Appointment Archival');

        $patient = Patient::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'patient_phone' => $patient->phone,
            'appointment_date' => now()->addDays(15),
            'appointment_time' => '11:00:00',
            'service_type' => 'Vaccination',
            'status' => 'completed',
        ]);

        $appointmentId = $appointment->id;

        // Check archived_appointments before deletion
        $archivedCountBefore = DB::table('archived_appointments')
            ->where('original_id', $appointmentId)
            ->count();

        // Delete appointment
        $appointment->delete();

        // Check archived_appointments after deletion
        $archivedCountAfter = DB::table('archived_appointments')
            ->where('original_id', $appointmentId)
            ->count();

        $this->assert(
            $archivedCountAfter > $archivedCountBefore,
            'Appointment should be archived before deletion'
        );

        $archived = DB::table('archived_appointments')
            ->where('original_id', $appointmentId)
            ->first();

        $this->assert($archived !== null, 'Archived appointment should exist');
        $this->assert($archived->patient_name === $patient->name, 'Archived data should match original');

        $this->command->info('   ✅ Appointment archival trigger working correctly');
        $this->command->newLine();
    }

    private function assert($condition, $message)
    {
        if (!$condition) {
            $this->command->error("   ❌ FAILED: $message");
            throw new \Exception("Assertion failed: $message");
        }
    }
}
