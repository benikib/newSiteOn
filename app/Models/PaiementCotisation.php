<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementCotisation extends Model
{
    protected $table = 'paiements_cotisations';

    protected $fillable = [
        'cotisation_id', 'montant', 'date_paiement', 'mode', 'reference', 'notes', 'enregistre_par',
    ];

    protected function casts(): array
    {
        return ['date_paiement' => 'date', 'montant' => 'decimal:2'];
    }

    public function cotisation(): BelongsTo
    {
        return $this->belongsTo(Cotisation::class);
    }

    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }
}
