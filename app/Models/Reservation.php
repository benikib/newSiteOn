<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'service_id',
        'client_name',
        'client_phone',
        'statut',
        'date'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    
}
