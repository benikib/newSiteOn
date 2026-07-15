<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'etablissement_id',
        'product_id',
        'quantity',
        'purchase_price',
        'selling_price',
        'minimum_stock'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    // ===== SCOPES =====

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_stock')->where('quantity', '>', 0);
    }

    public function scopeNormalStock($query)
    {
        return $query->whereRaw('quantity > minimum_stock');
    }

    public function scopeByEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // ===== ACCESSORS =====

    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->purchase_price;
    }

    public function getTotalSellingValueAttribute()
    {
        return $this->quantity * $this->selling_price;
    }

    public function getPotentialProfitAttribute()
    {
        return $this->quantity * ($this->selling_price - $this->purchase_price);
    }

    public function getStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return [
                'label' => 'Rupture',
                'badge' => 'bg-danger',
                'icon' => 'fa-times-circle'
            ];
        } elseif ($this->quantity <= $this->minimum_stock) {
            return [
                'label' => 'Stock bas',
                'badge' => 'bg-warning text-dark',
                'icon' => 'fa-exclamation-triangle'
            ];
        } else {
            return [
                'label' => 'Normal',
                'badge' => 'bg-success',
                'icon' => 'fa-check-circle'
            ];
        }
    }

    public function getStockPercentageAttribute()
    {
        if ($this->minimum_stock <= 0) {
            return 100;
        }
        $percentage = ($this->quantity / $this->minimum_stock) * 100;
        return min($percentage, 100);
    }
}