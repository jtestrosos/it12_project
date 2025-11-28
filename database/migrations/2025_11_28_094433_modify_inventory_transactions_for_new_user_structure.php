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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['user_id']);

            // Change to polymorphic relationship
            $table->dropColumn('user_id');

            $table->string('performable_type')->after('inventory_id');
            $table->unsignedBigInteger('performable_id')->after('performable_type');

            $table->index(['performable_type', 'performable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex(['performable_type', 'performable_id']);
            $table->dropColumn(['performable_type', 'performable_id']);

            $table->unsignedBigInteger('user_id')->after('inventory_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
