<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class equipe_personnel extends Model
{
    /** @use HasFactory<\Database\Factories\EquipePersonnelFactory> */
    use HasFactory;
    protected $fillable = [
        'equipe_id',
        'personnel_id',
    ];
    protected $table = 'equipe_personnels';
    public function equipe()
    {
        return $this->belongsTo(Equipe::class, 'equipe_id');
    }
    public function personnel()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }
}
