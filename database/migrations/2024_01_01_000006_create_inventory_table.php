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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->integer('current_stock');
            $table->integer('minimum_stock');
            $table->string('unit');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'expired'])->default('in_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
