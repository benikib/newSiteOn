<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReservationItem extends Model
{
    protected $fillable = [
        'product_reservation_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(ProductReservation::class, 'product_reservation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}