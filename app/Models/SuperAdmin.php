<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdmin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    // Note: No SoftDeletes for super admins

    protected $table = 'super_admins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
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
    ];

    /**
     * Get the appointments approved by this super admin.
     */
    public function approvedAppointments()
    {
        return $this->hasMany(Appointment::class, 'approved_by_super_admin_id');
    }

    /**
     * Get the inventory transactions performed by this super admin.
     */
    public function inventoryTransactions()
    {
        return $this->morphMany(InventoryTransaction::class, 'performable');
    }

    /**
     * Get the system logs for this super admin.
     */
    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    /**
     * Get the backups created by this super admin.
     */
    public function backups()
    {
        return $this->hasMany(Backup::class, 'created_by_super_admin_id');
    }

    public function isPatient()
    {
        return false;
    }

    public function isAdmin()
    {
        return false;
    }

    public function isSuperAdmin()
    {
        return true;
    }
}
