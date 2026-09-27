<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'etablissement_id',
        'inventory_date',
        'status',
        'user_id',
        'notes'
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== SCOPES =====

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // ===== ACCESSORS =====

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning text-dark',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];
        return $badges[$this->status] ?? 'bg-secondary';
    }
}