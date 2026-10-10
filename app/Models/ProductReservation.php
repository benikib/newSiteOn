<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReservation extends Model
{
    protected $fillable = [
        'etablissement_id',
        'client_name',
        'client_phone',
        'message',
        'statut',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function items()
    {
        return $this->hasMany(ProductReservationItem::class);
    }
}