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
        Schema::table('backups', function (Blueprint $table) {
            // Drop old foreign key
            if (Schema::hasColumn('backups', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            // Add separate columns for admin and super_admin
            $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('id');
            $table->unsignedBigInteger('created_by_super_admin_id')->nullable()->after('created_by_admin_id');

            $table->foreign('created_by_admin_id')->references('id')->on('admin')->onDelete('set null');
            $table->foreign('created_by_super_admin_id')->references('id')->on('super_admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->dropForeign(['created_by_admin_id']);
            $table->dropForeign(['created_by_super_admin_id']);
            $table->dropColumn(['created_by_admin_id', 'created_by_super_admin_id']);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
