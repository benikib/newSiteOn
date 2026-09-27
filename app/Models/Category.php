<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'nom', 'description', 'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====
    
    /**
     * Relation avec les produits
     * Une catégorie peut avoir plusieurs produits
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // ===== SCOPES =====
    
    /**
     * Filtrer les catégories actives
     */
    public function scopeActif($query)
    {
        return $query->where('status', true);
    }

    /**
     * Filtrer les catégories inactives
     */
    public function scopeInactif($query)
    {
        return $query->where('status', false);
    }

    /**
     * Filtrer les catégories avec produits
     */
    public function scopeAvecProduits($query)
    {
        return $query->has('products');
    }

    /**
     * Filtrer les catégories sans produits
     */
    public function scopeSansProduits($query)
    {
        return $query->doesntHave('products');
    }

    /**
     * Recherche par nom ou description
     */
    public function scopeRecherche($query, $term)
    {
        return $query->where('nom', 'LIKE', '%' . $term . '%')
                     ->orWhere('description', 'LIKE', '%' . $term . '%');
    }

    // ===== ACCESSORS =====
    
    /**
     * Obtenir le libellé du status
     */
    public function getstatusLabelAttribute()
    {
        return $this->status ? 'Actif' : 'Inactif';
    }

    /**
     * Obtenir le badge HTML du status
     */
    public function getstatusBadgeAttribute()
    {
        return $this->status 
            ? '<span class="badge bg-success">Actif</span>'
            : '<span class="badge bg-danger">Inactif</span>';
    }

    /**
     * Obtenir la valeur totale du stock de la catégorie
     */
    public function getTotalStockValueAttribute()
    {
        return $this->products()->sum('stock_value') ?? 0;
    }

    /**
     * Obtenir la quantité totale de produits
     */
    public function getTotalQuantityAttribute()
    {
        return $this->products()->sum('quantity') ?? 0;
    }
}