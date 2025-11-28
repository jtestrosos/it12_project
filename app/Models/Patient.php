<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Patient extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'patients';

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
}
