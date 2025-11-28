<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'loggable_type',
        'loggable_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'status',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = static::generateId();
            }
            if (empty($model->status)) {
                $model->status = 'active';
            }
        });
    }

    public static function generateId(): string
    {
        $prefix = 'BHW';
        // Get all logs with the prefix and find the highest number
        $logs = static::where('id', 'like', $prefix . '%')->get();

        $maxNumber = 0;
        foreach ($logs as $log) {
            if (preg_match('/' . preg_quote($prefix) . '(\d+)/', $log->id, $matches)) {
                $number = intval($matches[1]);
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        $newNumber = $maxNumber + 1;
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the entity that owns the log (Patient, Admin, or SuperAdmin).
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Legacy accessor for backwards compatibility.
     */
    public function user()
    {
        return $this->loggable();
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
