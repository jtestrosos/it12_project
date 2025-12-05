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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->string('performable_type')->nullable(); // Polymorphic: Admin or SuperAdmin or System
            $table->unsignedBigInteger('performable_id')->nullable();
            $table->enum('transaction_type', ['restock', 'usage', 'adjustment', 'expired']);
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['performable_type', 'performable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
