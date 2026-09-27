<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'etablissement_id',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ===== SCOPES =====

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', '%' . $term . '%')
                     ->orWhere('phone', 'LIKE', '%' . $term . '%')
                     ->orWhere('email', 'LIKE', '%' . $term . '%');
    }
}