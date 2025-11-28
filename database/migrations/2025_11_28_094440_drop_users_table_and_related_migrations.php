<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the users table - all data should have been migrated by now
        Schema::dropIfExists('users');

        // Note: The old user-related migration files should be manually deleted or archived
        // after confirming the migration was successful
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this migration - restore from backup if needed
        throw new \Exception('Cannot reverse dropping users table. Please restore from backup.');
    }
};
