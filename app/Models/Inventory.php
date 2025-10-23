<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_name',
        'description',
        'category',
        'current_stock',
        'minimum_stock',
        'unit',
        'unit_price',
        'expiry_date',
        'supplier',
        'status'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function updateStatus()
    {
        if ($this->current_stock == 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->current_stock <= $this->minimum_stock) {
            $this->status = 'low_stock';
        } elseif ($this->expiry_date && $this->expiry_date < now()) {
            $this->status = 'expired';
        } else {
            $this->status = 'in_stock';
        }
        
        $this->save();
    }
}
