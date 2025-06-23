<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'image_path'
    ];
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
    
 
   
    
   
}
