<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('patient_name');
            $table->string('patient_phone');
            $table->text('patient_address');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('service_type');
            $table->enum('status', ['pending', 'approved', 'rescheduled', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('is_walk_in')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
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
