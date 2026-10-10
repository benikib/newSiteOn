<?php

namespace App\Services;

use App\Models\TauxDeChange;
use Illuminate\Validation\ValidationException;

class PurchasePrice
{
    public static function normalize(float $amount, string $currency, string $errorField = 'purchase_currency'): array
    {
        if ($currency === 'USD') {
            $rate = TauxDeChange::orderByDesc('date')->value('usd_cdf');
            if (!$rate || $rate <= 0) {
                throw ValidationException::withMessages([
                    $errorField => 'Aucun taux USD/CDF n’est configuré.',
                ]);
            }

            return [round($amount * $rate, 2), $rate];
        }

        return [round($amount, 2), null];
    }
}