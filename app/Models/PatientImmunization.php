<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientImmunization extends Model
{
    protected $fillable = [
        'patient_id',
        // For Children
        'bcg',
        'dpt1',
        'dpt2',
        'dpt3',
        'opv1',
        'opv2',
        'opv3',
        'measles',
        'hepatitis_b1',
        'hepatitis_b2',
        'hepatitis_b3',
        'hepatitis_a',
        // For Elderly/Immunocompromised
        'varicella',
        'hpv',
        'pneumococcal',
        'mmr',
        'flu_vaccine',
        'none',
        // COVID-19
        'covid_vaccine_name',
        'covid_first_dose',
        'covid_second_dose',
        'covid_booster1',
        'covid_booster2',
    ];

    protected $casts = [
        'bcg' => 'boolean',
        'dpt1' => 'boolean',
        'dpt2' => 'boolean',
        'dpt3' => 'boolean',
        'opv1' => 'boolean',
        'opv2' => 'boolean',
        'opv3' => 'boolean',
        'measles' => 'boolean',
        'hepatitis_b1' => 'boolean',
        'hepatitis_b2' => 'boolean',
        'hepatitis_b3' => 'boolean',
        'hepatitis_a' => 'boolean',
        'varicella' => 'boolean',
        'hpv' => 'boolean',
        'pneumococcal' => 'boolean',
        'mmr' => 'boolean',
        'flu_vaccine' => 'boolean',
        'none' => 'boolean',
        'covid_first_dose' => 'date',
        'covid_second_dose' => 'date',
        'covid_booster1' => 'date',
        'covid_booster2' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
