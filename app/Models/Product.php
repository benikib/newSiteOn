<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'etablissement_id', 'category_id', 'unit_id', 'code', 
        'barcode', 'name', 'description', 'image', 'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====
    
    /**
     * Relation avec l'établissement
     */
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relation avec l'unité
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relation avec le stock
     */
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    // ===== SCOPES =====
    
    /**
     * Filtrer les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Filtrer les produits inactifs
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Filtrer par établissement
     */
    public function scopeByEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }

    /**
     * Recherche par nom ou code
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', '%' . $term . '%')
                     ->orWhere('code', 'LIKE', '%' . $term . '%')
                     ->orWhere('barcode', 'LIKE', '%' . $term . '%');
    }

    // ===== ACCESSORS =====
    
    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Actif' : 'Inactif';
    }

    /**
     * Obtenir le badge HTML du statut
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status 
            ? '<span class="badge bg-success">Actif</span>'
            : '<span class="badge bg-danger">Inactif</span>';
    }

    /**
     * Obtenir l'URL complète de l'image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('assets/img/default-product.png');
    }
}