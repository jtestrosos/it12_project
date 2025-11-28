<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['user_id']);

            // Rename user_id to patient_id
            $table->renameColumn('user_id', 'patient_id');

            // Add new foreign key to patients table
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

            // Modify approved_by to handle both admins and super_admins
            // We'll use separate columns for clarity
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');

            $table->unsignedBigInteger('approved_by_admin_id')->nullable()->after('is_walk_in');
            $table->unsignedBigInteger('approved_by_super_admin_id')->nullable()->after('approved_by_admin_id');

            $table->foreign('approved_by_admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('approved_by_super_admin_id')->references('id')->on('super_admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop new foreign keys
            $table->dropForeign(['approved_by_admin_id']);
            $table->dropForeign(['approved_by_super_admin_id']);
            $table->dropColumn(['approved_by_admin_id', 'approved_by_super_admin_id']);

            // Add back approved_by
            $table->unsignedBigInteger('approved_by')->nullable();

            // Drop patient foreign key
            $table->dropForeign(['patient_id']);

            // Rename back to user_id
            $table->renameColumn('patient_id', 'user_id');

            // Add back old foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }
};
