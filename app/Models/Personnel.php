<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    /** @use HasFactory<\Database\Factories\PersonnelFactory> */
    use HasFactory;
    protected $fillable = [
        'etablissement_id',
        'nom',
        'telephone',
        'poste',
    ];
    protected $table = 'personnels';
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }
    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'equipe_personnels', 'personnel_id', 'equipe_id');
    }
    public function equipePersonnels()
    {
        return $this->hasMany(equipe_personnel::class, 'personnel_id');
    }

}
