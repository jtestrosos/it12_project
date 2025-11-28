<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate patients (role = 'user')
        $patients = DB::table('users')->where('role', 'user')->get();
        foreach ($patients as $user) {
            DB::table('patients')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'gender' => $user->gender ?? 'other',
                'phone' => $user->phone ?? '',
                'address' => $user->address,
                'barangay' => $user->barangay ?? 'Other',
                'barangay_other' => $user->barangay_other,
                'purok' => $user->purok,
                'birth_date' => $user->birth_date ?? now()->subYears(18),
                'age' => $user->age,
                'profile_picture' => $user->profile_picture ?? null,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at ?? null,
            ]);
        }

        // Migrate admins (role = 'admin')
        $admins = DB::table('users')->where('role', 'admin')->get();
        foreach ($admins as $user) {
            DB::table('admins')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'role' => 'admin',
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture ?? null,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at ?? null,
            ]);
        }

        // Migrate super admins (role = 'superadmin')
        $superAdmins = DB::table('users')->where('role', 'superadmin')->get();
        foreach ($superAdmins as $user) {
            DB::table('super_admins')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture ?? null,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }

        // Update appointments approved_by references
        $appointments = DB::table('appointments')->whereNotNull('approved_by')->get();
        foreach ($appointments as $appointment) {
            $approver = DB::table('users')->find($appointment->approved_by);
            if ($approver) {
                if ($approver->role === 'admin') {
                    DB::table('appointments')
                        ->where('id', $appointment->id)
                        ->update(['approved_by_admin_id' => $approver->id]);
                } elseif ($approver->role === 'superadmin') {
                    DB::table('appointments')
                        ->where('id', $appointment->id)
                        ->update(['approved_by_super_admin_id' => $approver->id]);
                }
            }
        }

        // Update inventory_transactions to polymorphic
        $transactions = DB::table('inventory_transactions')->get();
        foreach ($transactions as $transaction) {
            $user = DB::table('users')->find($transaction->user_id);
            if ($user) {
                $type = match ($user->role) {
                    'admin' => 'App\\Models\\Admin',
                    'superadmin' => 'App\\Models\\SuperAdmin',
                    default => null,
                };

                if ($type) {
                    DB::table('inventory_transactions')
                        ->where('id', $transaction->id)
                        ->update([
                                'performable_type' => $type,
                                'performable_id' => $user->id,
                            ]);
                }
            }
        }

        // Update system_logs to polymorphic
        $logs = DB::table('system_logs')->whereNotNull('user_id')->get();
        foreach ($logs as $log) {
            $user = DB::table('users')->find($log->user_id);
            if ($user) {
                $type = match ($user->role) {
                    'user' => 'App\\Models\\Patient',
                    'admin' => 'App\\Models\\Admin',
                    'superadmin' => 'App\\Models\\SuperAdmin',
                    default => null,
                };

                if ($type) {
                    DB::table('system_logs')
                        ->where('id', $log->id)
                        ->update([
                                'loggable_type' => $type,
                                'loggable_id' => $user->id,
                            ]);
                }
            }
        }

        // Update backups created_by references
        $backups = DB::table('backups')->whereNotNull('created_by')->get();
        foreach ($backups as $backup) {
            $creator = DB::table('users')->find($backup->created_by);
            if ($creator) {
                if ($creator->role === 'admin') {
                    DB::table('backups')
                        ->where('id', $backup->id)
                        ->update(['created_by_admin_id' => $creator->id]);
                } elseif ($creator->role === 'superadmin') {
                    DB::table('backups')
                        ->where('id', $backup->id)
                        ->update(['created_by_super_admin_id' => $creator->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, down() would be complex
        // It's better to restore from backup if needed
        throw new \Exception('This migration cannot be reversed. Please restore from backup.');
    }
};
