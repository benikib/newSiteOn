<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publication extends Model
{
    protected $fillable = [
        'organisation_patronale_id', 'titre', 'synopsis', 'contenu', 'cible', 'publie_le', 'expire_le',
    ];

    protected function casts(): array
    {
        return ['publie_le' => 'datetime', 'expire_le' => 'datetime'];
    }

    public function organisationPatronale(): BelongsTo
    {
        return $this->belongsTo(OrganisationPatronale::class);
    }

    public function typesEtablissement(): BelongsToMany
    {
        return $this->belongsToMany(TypeEtablissement::class, 'publication_type_etablissement');
    }

    public function scopeEnLigne($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('publie_le')->orWhere('publie_le', '<=', now());
        })->where(function ($query) {
            $query->whereNull('expire_le')->orWhere('expire_le', '>=', now());
        });
    }
}
