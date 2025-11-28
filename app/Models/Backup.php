<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'filename',
        'file_path',
        'size',
        'status',
        'created_by_admin_id',
        'created_by_super_admin_id',
        'notes',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the admin who created the backup.
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Get the super admin who created the backup.
     */
    public function createdBySuperAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'created_by_super_admin_id');
    }

    /**
     * Get the creator (either admin or super admin).
     */
    public function createdBy()
    {
        return $this->createdByAdmin ?? $this->createdBySuperAdmin;
    }

    /**
     * Get the creator relationship dynamically.
     */
    public function getCreatorAttribute()
    {
        if ($this->created_by_admin_id) {
            return $this->createdByAdmin;
        }
        if ($this->created_by_super_admin_id) {
            return $this->createdBySuperAdmin;
        }
        return null;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getSizeInBytes()
    {
        if (!$this->size)
            return 0;

        $size = trim($this->size);
        $unit = strtolower(substr($size, -2));
        $value = (float) substr($size, 0, -2);

        switch ($unit) {
            case 'kb':
                return $value * 1024;
            case 'mb':
                return $value * 1024 * 1024;
            case 'gb':
                return $value * 1024 * 1024 * 1024;
            default:
                return $value;
        }
    }
}

