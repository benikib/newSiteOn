<?php

namespace App\Console\Commands;

use App\Models\Abonnement;
use App\Models\Etablissement;
use App\Services\AbonnementService;
use Illuminate\Console\Command;

class ExpireAbonnements extends Command
{
    protected $signature = 'abonnements:expire';

    protected $description = 'Expire les abonnements échus et synchronise les établissements';

    public function handle(AbonnementService $service): int
    {
        $dueCount = Abonnement::where('statut', 'actif')
            ->whereDate('date_fin', '<', now()->toDateString())
            ->count();

        Etablissement::query()->each(fn (Etablissement $etablissement) => $service->refreshEtablissement($etablissement));

        $this->info("Abonnements expirés : {$dueCount}");

        return self::SUCCESS;
    }
}