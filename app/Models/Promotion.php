<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory;
    protected $fillable = [
         'titre',
        'description',
        'date_debut',
        'date_fin',
        'service_id',
        'dure',
        'prix',
    ];
    protected $table = 'promotions';
    public function etablissement()
{
    return $this->belongsTo(Etablissement::class);
}
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function photos()
    {
        return $this->hasMany(Photo::class, 'promotion_id');
    }
    public function getDureAttribute($value)
    {
        return $value . ' jours';
    }

}
