<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Function: Auto-calculate patient age from birth_date
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_patient_age()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.birth_date IS NOT NULL THEN
                    NEW.age = EXTRACT(YEAR FROM AGE(NEW.birth_date));
                END IF;
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger: Update patient age before insert or update
        DB::unprepared("
            CREATE TRIGGER trigger_update_patient_age
                BEFORE INSERT OR UPDATE ON patient
                FOR EACH ROW
                EXECUTE FUNCTION update_patient_age();
        ");

        // Function: Prevent deletion of patients with active appointments
        DB::unprepared("
            CREATE OR REPLACE FUNCTION prevent_patient_deletion()
            RETURNS TRIGGER AS $$
            DECLARE
                active_appointments INTEGER;
            BEGIN
                -- Count active appointments (pending or approved)
                SELECT COUNT(*) INTO active_appointments
                FROM appointments
                WHERE patient_id = OLD.id
                  AND status IN ('pending', 'approved');
                
                -- Raise exception if patient has active appointments
                IF active_appointments > 0 THEN
                    RAISE EXCEPTION 'Cannot delete patient with % active appointment(s). Please cancel or complete appointments first.', active_appointments;
                END IF;
                
                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger: Prevent patient deletion if they have active appointments
        DB::unprepared("
            CREATE TRIGGER trigger_prevent_patient_deletion
                BEFORE DELETE ON patient
                FOR EACH ROW
                EXECUTE FUNCTION prevent_patient_deletion();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_prevent_patient_deletion ON patient');
        DB::unprepared('DROP FUNCTION IF EXISTS prevent_patient_deletion()');
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_update_patient_age ON patient');
        DB::unprepared('DROP FUNCTION IF EXISTS update_patient_age()');
    }
};
