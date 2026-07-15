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
        'statut',
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
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Relation avec les commandes (one-to-many)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation avec les clients (one-to-many)
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Relation avec les catégories (one-to-many)
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Relation avec les mouvements (one-to-many)
     */
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    // ===== SCOPES =====

    public function scopeActif($query)
    {
        return $query->where('statut', true);
    }

    public function scopeInactif($query)
    {
        return $query->where('statut', false);
    }

    // ===== ACCESSORS =====

    public function getStatutLabelAttribute()
    {
        return $this->statut ? 'Actif' : 'Inactif';
    }

    public function getStatutBadgeAttribute()
    {
        return $this->statut 
            ? '<span class="badge bg-success">Actif</span>'
            : '<span class="badge bg-danger">Inactif</span>';
    }

    // ===== MUTATORS =====

    public function getNomAttribute($value)
    {
        return $value ?? $this->name;
    }
}
