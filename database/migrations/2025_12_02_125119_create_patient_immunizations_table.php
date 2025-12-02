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
        Schema::create('patient_immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient')->onDelete('cascade');

            // For Children
            $table->boolean('bcg')->default(false);
            $table->boolean('dpt1')->default(false);
            $table->boolean('dpt2')->default(false);
            $table->boolean('dpt3')->default(false);
            $table->boolean('opv1')->default(false);
            $table->boolean('opv2')->default(false);
            $table->boolean('opv3')->default(false);
            $table->boolean('measles')->default(false);
            $table->boolean('hepatitis_b1')->default(false);
            $table->boolean('hepatitis_b2')->default(false);
            $table->boolean('hepatitis_b3')->default(false);
            $table->boolean('hepatitis_a')->default(false);

            // For Elderly and Immunocompromised
            $table->boolean('varicella')->default(false);
            $table->boolean('hpv')->default(false);
            $table->boolean('pneumococcal')->default(false);
            $table->boolean('mmr')->default(false);
            $table->boolean('flu_vaccine')->default(false);
            $table->boolean('none')->default(false);

            // COVID-19 Immunization
            $table->string('covid_vaccine_name')->nullable();
            $table->date('covid_first_dose')->nullable();
            $table->date('covid_second_dose')->nullable();
            $table->date('covid_booster1')->nullable();
            $table->date('covid_booster2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_immunizations');
    }
};
