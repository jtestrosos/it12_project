<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Function: Prevent double booking of appointments
        DB::unprepared("
            CREATE OR REPLACE FUNCTION prevent_double_booking()
            RETURNS TRIGGER AS $$
            DECLARE
                conflict_count INTEGER;
            BEGIN
                -- Check for conflicting appointments at the same date/time
                SELECT COUNT(*) INTO conflict_count
                FROM appointments
                WHERE appointment_date = NEW.appointment_date
                  AND appointment_time = NEW.appointment_time
                  AND status NOT IN ('cancelled', 'completed')
                  AND id != COALESCE(NEW.id, 0);
                
                -- Raise exception if conflict found
                IF conflict_count > 0 THEN
                    RAISE EXCEPTION 'Time slot already booked for % at %. Please choose a different time.', 
                        NEW.appointment_date, NEW.appointment_time;
                END IF;
                
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger: Prevent double booking before insert or update
        DB::unprepared("
            CREATE TRIGGER trigger_prevent_double_booking
                BEFORE INSERT OR UPDATE ON appointments
                FOR EACH ROW
                EXECUTE FUNCTION prevent_double_booking();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_prevent_double_booking ON appointments');
        DB::unprepared('DROP FUNCTION IF EXISTS prevent_double_booking()');
    }
};
