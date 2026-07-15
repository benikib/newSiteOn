<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';

    protected $fillable = [
        'inventory_id',
        'stock_id',
        'system_quantity',
        'physical_quantity',
        'difference'
    ];

    protected $casts = [
        'system_quantity' => 'integer',
        'physical_quantity' => 'integer',
        'difference' => 'integer',
    ];

    // ===== RELATIONS =====

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    // ===== SCOPES =====

    public function scopeWithDifference($query)
    {
        return $query->where('difference', '!=', 0);
    }

    public function scopePositiveDifference($query)
    {
        return $query->where('difference', '>', 0);
    }

    public function scopeNegativeDifference($query)
    {
        return $query->where('difference', '<', 0);
    }
}