<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Syncable;

class Patient extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, Syncable;

    protected $table = 'patient';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'phone',
        'address',
        'barangay',
        'barangay_other',
        'purok',
        'birth_date',
        'age',
        'profile_picture',
        // Treatment Record fields
        'accompanying_person',
        'accompanying_relationship',
        'mother_name',
        'father_name',
        'maiden_name',
        'spouse_name',
        'spouse_age',
        'spouse_occupation',
        'religion',
        'marital_status',
        'educational_attainment',
        'occupation',
        'smoker',
        'smoker_packs_per_year',
        'drinks_alcohol',
        'alcohol_specify',
        'illicit_drug_use',
        'multiple_sexual_partners',
        'is_pwd',
        'pwd_specify',
        'has_sti',
        'has_allergies',
        'allergies_specify',
        'social_history_others',
        'family_hypertension',
        'family_diabetes',
        'family_goiter',
        'family_cancer',
        'family_history_others',
        'coitarche_years_old',
        'history_uti',
        'history_hypertension',
        'history_diabetes',
        'history_goiter',
        'history_cancer',
        'history_tuberculosis',
        'medical_history_others',
        'previous_surgeries',
        'maintenance_medicine',
        'consent_signed',
        'consent_signed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'deleted_at' => 'datetime',
        // Treatment Record boolean casts
        'smoker' => 'boolean',
        'drinks_alcohol' => 'boolean',
        'illicit_drug_use' => 'boolean',
        'multiple_sexual_partners' => 'boolean',
        'is_pwd' => 'boolean',
        'has_sti' => 'boolean',
        'has_allergies' => 'boolean',
        'family_hypertension' => 'boolean',
        'family_diabetes' => 'boolean',
        'family_goiter' => 'boolean',
        'family_cancer' => 'boolean',
        'history_uti' => 'boolean',
        'history_hypertension' => 'boolean',
        'history_diabetes' => 'boolean',
        'history_goiter' => 'boolean',
        'history_cancer' => 'boolean',
        'history_tuberculosis' => 'boolean',
        'consent_signed' => 'boolean',
        'consent_signed_at' => 'datetime',
    ];

    /**
     * Get the appointments for the patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Get the system logs for the patient.
     */
    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    /**
     * Get the full barangay name.
     */
    public function getFullBarangayAttribute()
    {
        if ($this->barangay === 'Other') {
            return $this->barangay_other;
        }
        return $this->barangay;
    }

    /**
     * Get the full address with barangay and purok.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->purok,
            $this->full_barangay,
        ]);
        return implode(', ', $parts);
    }

    public function isPatient()
    {
        return true;
    }

    public function isAdmin()
    {
        return false;
    }

    public function isSuperAdmin()
    {
        return false;
    }

    /**
     * Get the immunization record for the patient.
     */
    public function immunization()
    {
        return $this->hasOne(PatientImmunization::class);
    }

    /**
     * Check if patient is eligible for treatment record (6+ years).
     */
    public function isEligibleForTreatmentRecord()
    {
        return $this->age >= 6;
    }
}
