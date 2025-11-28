<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status::text = ANY (ARRAY['pending'::text, 'approved'::text, 'rescheduled'::text, 'cancelled'::text, 'completed'::text, 'no_show'::text]))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To rollback, we'll remove 'no_show' from the ENUM
        // First, update any 'no_show' statuses to 'cancelled'
        DB::table('appointments')
            ->where('status', 'no_show')
            ->update(['status' => 'cancelled']);

        // Then modify the column to remove 'no_show' from the ENUM
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'approved', 'rescheduled', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status::text = ANY (ARRAY['pending'::text, 'approved'::text, 'rescheduled'::text, 'cancelled'::text, 'completed'::text]))");
        }
    }
};
