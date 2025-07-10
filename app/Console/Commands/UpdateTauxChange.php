<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TauxDeChange;
use Illuminate\Support\Facades\Http;

class UpdateTauxChange extends Command
{
    protected $signature = 'taux:update';
    protected $description = 'Récupère et enregistre le taux USD → CDF du jour';

    public function handle()
    {
        $date = now()->toDateString();

        // Exemple avec exchangerate.host
        $response = Http::get('https://api.exchangerate.host/latest?base=USD&symbols=CDF');

        if ($response->successful()) {
            $taux = $response->json()['rates']['CDF'];

            TauxDeChange::updateOrCreate(
                ['date' => $date],
                ['usd_cdf' => $taux]
            );

            $this->info("Taux du jour sauvegardé : 1 USD = $taux CDF");
        } else {
            $this->error("Échec de récupération du taux.");
        }
    }
}
