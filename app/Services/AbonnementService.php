<?php

namespace App\Services;

use App\Models\Abonnement;
use App\Models\Etablissement;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AbonnementService
{
    public function refreshEtablissement(Etablissement $etablissement): bool
    {
        $today = now()->toDateString();

        Abonnement::where('etablissement_id', $etablissement->id)
            ->where('statut', 'actif')
            ->whereDate('date_fin', '<', $today)
            ->get()
            ->each(fn (Abonnement $abonnement) => $abonnement->update(['statut' => 'expire']));

        $current = Abonnement::where('etablissement_id', $etablissement->id)
            ->where('statut', 'actif')
            ->whereDate('date_debut', '<=', $today)
            ->whereDate('date_fin', '>=', $today)
            ->where('type_operation', '!=', 'suspension')
            ->orderByDesc('id')
            ->first();

        $isSuspended = $current && Abonnement::where('etablissement_id', $etablissement->id)
            ->where('type_operation', 'suspension')
            ->where('id', '>', $current->id)
            ->exists();

        $isActive = (bool) $current && !$isSuspended;
        $newStatus = $isActive ? 'actif' : 'desactive';
        if ($etablissement->statut !== $newStatus && ($isActive || $etablissement->statut === 'actif')) {
            $etablissement->update(['statut' => $newStatus]);
        }

        return $isActive;
    }

    public function userHasValidSubscription(User $user): bool
    {
        if ($user->role !== 'etablissement') {
            return true;
        }

        $links = $user->usersEtablissements()->with('etablissement')->get();
        $hasValidSubscription = false;
        foreach ($links as $link) {
            if ($link->etablissement) {
                $hasValidSubscription = $this->refreshEtablissement($link->etablissement) || $hasValidSubscription;
            }
        }

        return $hasValidSubscription;
    }

    public function create(Etablissement $etablissement, array $data, User $actor): Abonnement
    {
        return DB::transaction(function () use ($etablissement, $data, $actor) {
            Etablissement::whereKey($etablissement->id)->lockForUpdate()->firstOrFail();

            $operation = $data['type_operation'];
            $today = CarbonImmutable::today();
            $current = $this->currentPeriod($etablissement);

            if (in_array($operation, ['renouvellement', 'prolongation'], true)) {
                if (!$current) {
                    throw ValidationException::withMessages([
                        'type_operation' => 'Un renouvellement ou une prolongation exige un abonnement en cours.',
                    ]);
                }
                $start = CarbonImmutable::parse($current->date_fin)->addDay();
            } else {
                if ($current) {
                    throw ValidationException::withMessages([
                        'type_operation' => 'Un abonnement est déjà en cours. Choisissez un renouvellement ou une prolongation.',
                    ]);
                }
                $start = CarbonImmutable::parse($data['date_debut']);
            }

            $end = CarbonImmutable::parse($data['date_fin']);
            if ($end->lt($start)) {
                throw ValidationException::withMessages([
                    'date_fin' => 'La date de fin doit être égale ou postérieure à la date de début calculée.',
                ]);
            }

            $overlap = Abonnement::where('etablissement_id', $etablissement->id)
                ->where('type_operation', '!=', 'suspension')
                ->whereDate('date_debut', '<=', $end->toDateString())
                ->whereDate('date_fin', '>=', $start->toDateString())
                ->exists();
            if ($overlap) {
                throw ValidationException::withMessages([
                    'date_debut' => 'Cette période chevauche un abonnement déjà enregistré.',
                ]);
            }

            $abonnement = Abonnement::create([
                'etablissement_id' => $etablissement->id,
                'date_debut' => $start->toDateString(),
                'date_fin' => $end->toDateString(),
                'statut' => 'actif',
                'type_operation' => $operation,
                'motif' => $data['motif'],
                'montant_paye' => $data['montant_paye'] ?? null,
                'devise' => $data['devise'] ?? null,
                'created_by' => $actor->id,
            ]);

            $this->refreshEtablissement($etablissement->fresh());

            return $abonnement;
        });
    }

    public function suspend(Etablissement $etablissement, string $motif, User $actor): Abonnement
    {
        return DB::transaction(function () use ($etablissement, $motif, $actor) {
            Etablissement::whereKey($etablissement->id)->lockForUpdate()->firstOrFail();
            $current = $this->currentPeriod($etablissement);

            if (!$current) {
                throw ValidationException::withMessages([
                    'motif' => 'Aucun abonnement en cours ne peut être suspendu.',
                ]);
            }

            $suspensionAfterCurrent = Abonnement::where('etablissement_id', $etablissement->id)
                ->where('type_operation', 'suspension')
                ->where('id', '>', $current->id)
                ->exists();
            if ($suspensionAfterCurrent) {
                throw ValidationException::withMessages([
                    'motif' => 'Cet abonnement est déjà suspendu.',
                ]);
            }

            $today = now()->toDateString();
            $suspension = Abonnement::create([
                'etablissement_id' => $etablissement->id,
                'date_debut' => $today,
                'date_fin' => $today,
                'statut' => 'annule',
                'type_operation' => 'suspension',
                'motif' => $motif,
                'created_by' => $actor->id,
            ]);

            $this->refreshEtablissement($etablissement->fresh());

            return $suspension;
        });
    }

    public function currentPeriod(Etablissement $etablissement): ?Abonnement
    {
        $today = now()->toDateString();

        $current = Abonnement::where('etablissement_id', $etablissement->id)
            ->where('statut', 'actif')
            ->where('type_operation', '!=', 'suspension')
            ->whereDate('date_debut', '<=', $today)
            ->whereDate('date_fin', '>=', $today)
            ->orderByDesc('id')
            ->first();

        if (!$current) {
            return null;
        }

        $isSuspended = Abonnement::where('etablissement_id', $etablissement->id)
            ->where('type_operation', 'suspension')
            ->where('id', '>', $current->id)
            ->exists();

        return $isSuspended ? null : $current;
    }
}