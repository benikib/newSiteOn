<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Publicite extends Model
{
    /** @use HasFactory<\Database\Factories\PubliciteFactory> */
    use HasFactory;
    protected $fillable = [
        'titre',
        'description',
        'date',
        'etablissement_id',
        'dure',
        'image_path',
        'status',
        'promotion'
    ];
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }


public function scopeActives($query)
{
    return $query->where('status', 'active')
                 ->whereRaw("DATE_ADD(date, INTERVAL dure DAY) >= ?", [now()->toDateString()]);
}


 
   
    
   
}
