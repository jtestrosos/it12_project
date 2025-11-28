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
        Schema::table('system_logs', function (Blueprint $table) {
            // Drop old foreign key
            if (Schema::hasColumn('system_logs', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // Add polymorphic relationship
            $table->string('loggable_type')->nullable()->after('id');
            $table->unsignedBigInteger('loggable_id')->nullable()->after('loggable_type');

            $table->index(['loggable_type', 'loggable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            $table->dropIndex(['loggable_type', 'loggable_id']);
            $table->dropColumn(['loggable_type', 'loggable_id']);

            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
