<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'etablissement_id',
    ];
    protected $table = 'services';
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }
    
    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'service_id');
    }
    public function photos()
    {
        return $this->hasMany(Photo::class, 'service_id');
    }
}
