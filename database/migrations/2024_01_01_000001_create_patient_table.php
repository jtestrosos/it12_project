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
        Schema::create('patient', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('barangay');
            $table->string('barangay_other')->nullable();
            $table->string('purok')->nullable();
            $table->date('birth_date');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('profile_picture')->nullable();

            // Treatment Record Fields - Demographics
            $table->string('accompanying_person')->nullable();
            $table->string('accompanying_relationship')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('maiden_name')->nullable();
            $table->string('spouse_name')->nullable();
            $table->unsignedTinyInteger('spouse_age')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('religion')->nullable();
            $table->enum('marital_status', ['single', 'married', 'widowed', 'separated', 'co-habitation'])->nullable();
            $table->string('educational_attainment')->nullable();
            $table->string('occupation')->nullable();

            // Personal/Social History
            $table->boolean('smoker')->default(false);
            $table->string('smoker_packs_per_year')->nullable();
            $table->boolean('drinks_alcohol')->default(false);
            $table->string('alcohol_specify')->nullable();
            $table->boolean('illicit_drug_use')->default(false);
            $table->boolean('multiple_sexual_partners')->default(false);
            $table->boolean('is_pwd')->default(false);
            $table->string('pwd_specify')->nullable();
            $table->boolean('has_sti')->default(false);
            $table->boolean('has_allergies')->default(false);
            $table->text('allergies_specify')->nullable();
            $table->text('social_history_others')->nullable();

            // Family History
            $table->boolean('family_hypertension')->default(false);
            $table->boolean('family_diabetes')->default(false);
            $table->boolean('family_goiter')->default(false);
            $table->boolean('family_cancer')->default(false);
            $table->text('family_history_others')->nullable();
            $table->string('coitarche_years_old')->nullable();

            // Patient Medical History
            $table->boolean('history_uti')->default(false);
            $table->boolean('history_hypertension')->default(false);
            $table->boolean('history_diabetes')->default(false);
            $table->boolean('history_goiter')->default(false);
            $table->boolean('history_cancer')->default(false);
            $table->boolean('history_tuberculosis')->default(false);
            $table->text('medical_history_others')->nullable();

            // Previous Surgeries & Maintenance Medicine
            $table->text('previous_surgeries')->nullable();
            $table->text('maintenance_medicine')->nullable();

            // Consent tracking
            $table->boolean('consent_signed')->default(false);
            $table->timestamp('consent_signed_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
};
