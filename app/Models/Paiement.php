<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    /** @use HasFactory<\Database\Factories\PaiementFactory> */

    use HasFactory;
    protected $fillable = [
        'service_id',
        'client',
        'client_phone',
        'montant',
        'date',
    ];
    protected $table = 'paiements';
      public function service()
    {
        return $this->belongsTo(Service::class,'service_id' );
    }
}
