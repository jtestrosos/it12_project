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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient')->onDelete('cascade');
            $table->string('patient_name');
            $table->string('patient_phone');
            $table->text('patient_address')->nullable();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('service_type');
            $table->enum('status', ['pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('is_walk_in')->default(false);
            $table->unsignedBigInteger('approved_by_admin_id')->nullable();
            $table->unsignedBigInteger('approved_by_super_admin_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('approved_by_admin_id')->references('id')->on('admin')->onDelete('set null');
            $table->foreign('approved_by_super_admin_id')->references('id')->on('super_admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
