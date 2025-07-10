<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    /** @use HasFactory<\Database\Factories\EtablissementFactory> */
    use HasFactory;
    protected $fillable = [
        'nom',
        'ville',
        'commune',
        'avenue',
        'website',
        'email',
        'quartier',
        'numero',
        'description',
        'latitude',
        'longitude',
        'telephone',
        'note_moyenne',
        'type_etablissement_id',
        'image_path',
        'facebook', 'twitter', 'instagram'
        
    ];
    protected $table = 'etablissements';
    public function typeEtablissement()
    {
        return $this->belongsTo(TypeEtablissement::class, 'type_etablissement_id');
    }
    public function publicites()
{
    return $this->hasMany(Publicite::class);
}
    public function services()
    {
        return $this->hasMany(Service::class, 'etablissement_id');
    }
    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'etablissement_id');
    }
    public function photos()
    {
        return $this->hasMany(Photo::class, 'etablissement_id');
    }
    public function users()
{
    return $this->belongsToMany(User::class, 'user_etablissements');
}
public function scopeSearch($query, $term)
{
    return $query->where(function($q) use ($term) {
        $q->where('nom', 'like', "%{$term}%")
          ->orWhere('description', 'like', "%{$term}%")
          ->orWhereHas('services', function($q) use ($term) {
              $q->where('nom', 'like', "%{$term}%");
          });
    });
}

public function scopeByCategory($query, $categoryId)
{
    return $query->where('category_id', $categoryId);
}
}
