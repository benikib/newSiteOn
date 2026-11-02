<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationPatronale extends Model
{
    protected $table = 'organisations_patronales';

    protected $fillable = ['user_id', 'nom', 'telephone', 'email', 'adresse'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }

    public function cotisations(): HasMany
    {
        return $this->hasMany(Cotisation::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }
}
