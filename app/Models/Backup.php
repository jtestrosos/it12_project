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
        'created_by',
        'notes',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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
        if (!$this->size) return 0;
        
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

