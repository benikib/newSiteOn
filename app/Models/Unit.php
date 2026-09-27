<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';
    
    protected $fillable = [
        'name', 'symbol', 'description', 'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====
    
    /**
     * Relation avec les produits
     * Une unité peut avoir plusieurs produits
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    // ===== SCOPES =====
    
    /**
     * Filtrer les unités actives
     */
    public function scopeActif($query)
    {
        return $query->where('status', true);
    }

    /**
     * Filtrer les unités inactives
     */
    public function scopeInactif($query)
    {
        return $query->where('status', false);
    }
}
