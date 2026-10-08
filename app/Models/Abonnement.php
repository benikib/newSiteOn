<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class Abonnement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'etablissement_id',
        'date_debut',
        'date_fin',
        'statut',
        'type_operation',
        'motif',
        'montant_paye',
        'devise',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant_paye' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (self $abonnement) {
            if ($abonnement->getDirty() !== ['statut' => 'expire'] || $abonnement->getOriginal('statut') !== 'actif') {
                throw new LogicException('Un abonnement est immuable, sauf pour son expiration automatique.');
            }
        });

        static::deleting(function () {
            throw new LogicException('Un abonnement historique ne peut pas être supprimé.');
        });
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePeriodes($query)
    {
        return $query->where('type_operation', '!=', 'suspension');
    }

    public function getTypeOperationLabelAttribute(): string
    {
        return [
            'creation' => 'Création',
            'renouvellement' => 'Renouvellement',
            'prolongation' => 'Prolongation',
            'reactivation_manuelle' => 'Réactivation manuelle',
            'suspension' => 'Suspension',
            'migration' => 'Migration',
            'expiration_automatique' => 'Expiration automatique',
        ][$this->type_operation] ?? $this->type_operation;
    }

    public function getStatutLabelAttribute(): string
    {
        return [
            'actif' => 'Actif',
            'expire' => 'Expiré',
            'annule' => 'Annulé',
            'remplace' => 'Remplacé',
        ][$this->statut] ?? $this->statut;
    }
}