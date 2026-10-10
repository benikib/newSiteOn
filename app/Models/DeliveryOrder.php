<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'etablissement_id',
        'client_name',
        'client_phone',
        'delivery_address',
        'payment_plan',
        'total_amount',
        'deposit_amount',
        'amount_paid',
        'statut',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }
}