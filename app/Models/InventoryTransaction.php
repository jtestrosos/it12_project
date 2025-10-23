<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'user_id',
        'transaction_type',
        'quantity',
        'notes'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRestock($query)
    {
        return $query->where('transaction_type', 'restock');
    }

    public function scopeUsage($query)
    {
        return $query->where('transaction_type', 'usage');
    }
}
