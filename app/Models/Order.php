<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'etablissement_id',
        'user_id',
        'customer_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'notes',
        'total_ht',
        'total_tva',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'type',
        'order_date'
    ];

    protected $casts = [
        'total_ht' => 'decimal:2',
        'total_tva' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePos($query)
    {
        return $query->where('type', 'pos');
    }

    public function scopeByEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }

    // ===== ACCESSORS =====

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];
        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'transfer' => 'Virement',
            'other' => 'Autre',
        ];
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getPaymentStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
        ];
        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    public function getTypeLabelAttribute()
    {
        $types = [
            'pos' => 'Point de vente',
            'direct' => 'Direct',
            'online' => 'En ligne',
        ];
        return $types[$this->type] ?? $this->type;
    }

    // ===== BOOT =====

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $prefix = $order->type == 'pos' ? 'POS' : 'CMD';
                $order->order_number = $prefix . '-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
        });
    }
}