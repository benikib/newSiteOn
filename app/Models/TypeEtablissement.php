<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeEtablissement extends Model
{
    /** @use HasFactory<\Database\Factories\TypeEtablissementFactory> */
    use HasFactory;
    protected $fillable = [
        'nom',
        'description',
    ];
    protected $table = 'type_etablissements';
    public function etablissements()
    {
        return $this->hasMany(Etablissement::class, 'type_etablissement_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'type_etablissement_id');
    }
  

}
