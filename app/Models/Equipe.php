<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    /** @use HasFactory<\Database\Factories\EquipeFactory> */
    use HasFactory;
    protected $fillable = [
        'etablissement_id',
        'nom',
        'description',
    ];
    protected $table = 'equipes';
    public function personnels()
    {
        return $this->belongsToMany(Personnel::class, 'equipe_personnels', 'equipe_id', 'personnel_id');
    }
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }
    public function equipePersonnels()
    {
        return $this->hasMany(equipe_personnel::class, 'equipe_id');
    }
}
