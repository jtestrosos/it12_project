<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admin';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the appointments approved by this admin.
     */
    public function approvedAppointments()
    {
        return $this->hasMany(Appointment::class, 'approved_by_admin_id');
    }

    /**
     * Get the inventory transactions performed by this admin.
     */
    public function inventoryTransactions()
    {
        return $this->morphMany(InventoryTransaction::class, 'performable');
    }

    /**
     * Get the system logs for this admin.
     */
    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    /**
     * Get the backups created by this admin.
     */
    public function backups()
    {
        return $this->hasMany(Backup::class, 'created_by_admin_id');
    }

    public function isPatient()
    {
        return false;
    }

    public function isAdmin()
    {
        return true;
    }

    public function isSuperAdmin()
    {
        return false;
    }
}
