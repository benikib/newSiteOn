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
        'promotion',
        'ciblage',
        'ville',
        'commune',
    ];
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }


public function scopeActives($query)
{
    $dateExpression = match ($query->getConnection()->getDriverName()) {
        'sqlite' => "date(date, '+' || dure || ' days') >= ?",
        'pgsql' => "date + dure * INTERVAL '1 day' >= ?::date",
        'sqlsrv' => 'DATEADD(day, dure, date) >= ?',
        default => 'DATE_ADD(date, INTERVAL dure DAY) >= ?',
    };

    return $query->where('status', 'active')
        ->whereRaw($dateExpression, [now()->toDateString()]);
}

public function scopeVisiblesDans($query, ?string $ville = null, ?string $commune = null)
{
    return $query->where(function ($target) use ($ville, $commune) {
        $target->where('ciblage', 'generalisee');
        if ($ville) {
            $target->orWhere(function ($location) use ($ville, $commune) {
                $location->where('ciblage', 'emplacement')
                    ->whereRaw('LOWER(ville) = ?', [mb_strtolower($ville)]);
                if ($commune) {
                    $location->whereRaw('LOWER(commune) = ?', [mb_strtolower($commune)]);
                }
            });
        }
    });
}

}
