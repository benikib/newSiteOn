<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TauxDeChange extends Model
{
    use HasFactory;

    protected $fillable = ['usd_cdf', 'date'];
     protected $table = 'taux_de_changes';
}
