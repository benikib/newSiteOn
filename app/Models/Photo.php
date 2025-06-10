<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'image_path',
        'url',
        'alt_text',
        'status',
        'etablissement_id',
        'service_id',
        'promotion_id'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    // Accesseur pour l'URL complète de l'image
    public function getImageUrlAttribute()
    {
        return asset('storage/'.$this->image_path);
    }
}