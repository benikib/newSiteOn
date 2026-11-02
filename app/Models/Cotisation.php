<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotisation extends Model
{
    protected $fillable = [
        'organisation_patronale_id', 'etablissement_id', 'libelle', 'description',
        'montant', 'devise', 'date_echeance', 'statut',
    ];

    protected function casts(): array
    {
        return ['date_echeance' => 'date', 'montant' => 'decimal:2'];
    }

    public function organisationPatronale(): BelongsTo
    {
        return $this->belongsTo(OrganisationPatronale::class);
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementCotisation::class);
    }
}
