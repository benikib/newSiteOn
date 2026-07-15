<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_ht',
        'tva_rate',
        'tva_amount',
        'subtotal_ht',
        'subtotal_ttc'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_ht' => 'decimal:2',
        'tva_rate' => 'decimal:2',
        'tva_amount' => 'decimal:2',
        'subtotal_ht' => 'decimal:2',
        'subtotal_ttc' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ===== ACCESSORS =====

    public function getTvaLabelAttribute()
    {
        return $this->tva_rate . '%';
    }

    public function getTotalHtAttribute()
    {
        return $this->price_ht * $this->quantity;
    }

    public function getTotalTtcAttribute()
    {
        return $this->total_ht + ($this->total_ht * $this->tva_rate / 100);
    }
}