<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create archived_appointments table
        Schema::create('archived_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id'); // Original appointment ID
            $table->unsignedBigInteger('patient_id');
            $table->string('patient_name');
            $table->string('patient_phone');
            $table->text('patient_address')->nullable();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('service_type');
            $table->enum('status', ['pending', 'approved', 'rescheduled', 'cancelled', 'completed', 'no_show']);
            $table->text('notes')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('is_walk_in')->default(false);
            $table->unsignedBigInteger('approved_by_admin_id')->nullable();
            $table->unsignedBigInteger('approved_by_super_admin_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('original_created_at')->nullable();
            $table->timestamp('original_updated_at')->nullable();
            $table->timestamp('archived_at');
            $table->string('archived_by_type')->nullable(); // Admin or SuperAdmin
            $table->unsignedBigInteger('archived_by_id')->nullable();
            $table->text('archive_reason')->nullable();
        });

        // Function: Archive appointment before deletion
        DB::unprepared("
            CREATE OR REPLACE FUNCTION archive_deleted_appointment()
            RETURNS TRIGGER AS $$
            BEGIN
                INSERT INTO archived_appointments (
                    original_id,
                    patient_id,
                    patient_name,
                    patient_phone,
                    patient_address,
                    appointment_date,
                    appointment_time,
                    service_type,
                    status,
                    notes,
                    medical_history,
                    is_walk_in,
                    approved_by_admin_id,
                    approved_by_super_admin_id,
                    approved_at,
                    original_created_at,
                    original_updated_at,
                    archived_at,
                    archive_reason
                ) VALUES (
                    OLD.id,
                    OLD.patient_id,
                    OLD.patient_name,
                    OLD.patient_phone,
                    OLD.patient_address,
                    OLD.appointment_date,
                    OLD.appointment_time,
                    OLD.service_type,
                    OLD.status,
                    OLD.notes,
                    OLD.medical_history,
                    OLD.is_walk_in,
                    OLD.approved_by_admin_id,
                    OLD.approved_by_super_admin_id,
                    OLD.approved_at,
                    OLD.created_at,
                    OLD.updated_at,
                    NOW(),
                    'Appointment deleted'
                );
                
                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger: Archive appointment before deletion
        DB::unprepared("
            CREATE TRIGGER trigger_archive_appointment
                BEFORE DELETE ON appointments
                FOR EACH ROW
                EXECUTE FUNCTION archive_deleted_appointment();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_archive_appointment ON appointments');
        DB::unprepared('DROP FUNCTION IF EXISTS archive_deleted_appointment()');
        Schema::dropIfExists('archived_appointments');
    }
};
