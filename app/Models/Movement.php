<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $table = 'movements';

    protected $fillable = [
        'etablissement_id',
        'product_id',
        'type',
        'quantity',
        'before',
        'after',
        'purchase_price',
        'selling_price',
        'note',
        'user_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'before' => 'integer',
        'after' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
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
     * Relation avec le produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation avec l'utilisateur qui a effectué le mouvement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== SCOPES =====

    /**
     * Filtrer par type de mouvement
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Filtrer par établissement
     */
    public function scopeByEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }

    /**
     * Filtrer par produit
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Filtrer par période
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    // ===== ACCESSORS =====

    /**
     * Obtenir le libellé du type de mouvement
     */
    public function getTypeLabelAttribute()
    {
        $types = [
            'in' => 'Entrée',
            'out' => 'Sortie',
            'adjust_positive' => 'Ajustement +',
            'adjust_negative' => 'Ajustement -',
            'inventory_in' => 'Inventaire +',
            'inventory_out' => 'Inventaire -',
            'stock_in_create' => 'Création stock',
            'stock_in_update' => 'Approvisionnement',
        ];
        return $types[$this->type] ?? $this->type;
    }

    /**
     * Obtenir le badge Bootstrap pour le type
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'in' => 'bg-success',
            'out' => 'bg-danger',
            'adjust_positive' => 'bg-info',
            'adjust_negative' => 'bg-warning text-dark',
            'inventory_in' => 'bg-primary',
            'inventory_out' => 'bg-secondary',
            'stock_in_create' => 'bg-success',
            'stock_in_update' => 'bg-success',
        ];
        return $badges[$this->type] ?? 'bg-secondary';
    }

    /**
     * Obtenir le libellé de la variation
     */
    public function getVariationLabelAttribute()
    {
        $difference = $this->after - $this->before;
        if ($difference > 0) {
            return '+' . $difference;
        } elseif ($difference < 0) {
            return $difference;
        }
        return '0';
    }

    /**
     * Obtenir la variation en pourcentage
     */
    public function getVariationPercentAttribute()
    {
        if ($this->before == 0) {
            return $this->after > 0 ? 100 : 0;
        }
        return round((($this->after - $this->before) / $this->before) * 100, 2);
    }
}