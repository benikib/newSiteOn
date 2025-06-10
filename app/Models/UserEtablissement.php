<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEtablissement extends Model
{
    /** @use HasFactory<\Database\Factories\UserEtablissementFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'etablissement_id',
    ];
    protected $table = 'user_etablissements';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }
    public function scopeWithUserAndEtablissement($query)
    {
        return $query->with(['user', 'etablissement']);
    }
    public function scopeWithEtablissement($query)
    {
        return $query->with('etablissement');
    }
    public function scopeWithUser($query)
    {
        return $query->with('user');
    }
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
    public function scopeSearch($query, $searchTerm)
    {
        return $query->whereHas('user', function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('email', 'like', '%' . $searchTerm . '%');
        })->orWhereHas('etablissement', function ($q) use ($searchTerm) {
            $q->where('nom', 'like', '%' . $searchTerm . '%');
        });
    }
    public function scopeFilterByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    public function scopeFilterByEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }
    public function scopeFilterByRole($query, $role)
    {
        return $query->whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }
    public function scopeFilterByEtablissementType($query, $type)
    {
        return $query->whereHas('etablissement', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }
    public function scopeFilterByEtablissementName($query, $name)
    {
        return $query->whereHas('etablissement', function ($q) use ($name) {
            $q->where('nom', 'like', '%' . $name . '%');
        });
    }
    public function scopeFilterByUserName($query, $name)
    {
        return $query->whereHas('user', function ($q) use ($name) {
            $q->where('name', 'like', '%' . $name . '%');
        });
    }
    public function scopeFilterByUserEmail($query, $email)
    {
        return $query->whereHas('user', function ($q) use ($email) {
            $q->where('email', 'like', '%' . $email . '%');
        });
    }
    public function scopeFilterByUserRole($query, $role)
    {
        return $query->whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }
    public function scopeFilterByUserTelephone($query, $telephone)
    {
        return $query->whereHas('user', function ($q) use ($telephone) {
            $q->where('telephone', 'like', '%' . $telephone . '%');
        });
    }
    public function scopeFilterByEtablissementAddress($query, $address)
    {
        return $query->whereHas('etablissement', function ($q) use ($address) {
            $q->where('adresse', 'like', '%' . $address . '%');
        });
    }
    
}
