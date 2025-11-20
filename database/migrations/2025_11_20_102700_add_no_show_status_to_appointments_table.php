<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show') NOT NULL DEFAULT 'pending'");
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
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'approved', 'rescheduled', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
    }
};
