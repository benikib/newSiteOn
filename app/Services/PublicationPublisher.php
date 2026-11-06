<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\User;
use App\Notifications\PublicationAvailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PublicationPublisher
{
    public function publish(User $author, array $data, ?int $organisationId = null): Publication
    {
        $publication = Publication::create([
            'organisation_patronale_id' => $organisationId,
            'auteur_id' => $author->id,
            'titre' => $data['titre'],
            'synopsis' => $data['synopsis'] ?? null,
            'contenu' => $data['contenu'],
            'cible' => $data['cible'],
            'categorie' => $data['categorie'],
            'ville' => $data['ville'] ?? null,
            'commune' => $data['commune'] ?? null,
            'publie_le' => $data['publie_le'] ?? now(),
            'expire_le' => $data['expire_le'] ?? null,
        ]);

        if ($publication->cible === 'domaines') {
            $publication->typesEtablissement()->sync($data['types_etablissement']);
        }

        $this->notifyIfDue($publication);

        return $publication->refresh();
    }

    public function notifyIfDue(Publication $publication): void
    {
        DB::transaction(function () use ($publication) {
            $locked = Publication::query()->lockForUpdate()->findOrFail($publication->id);
            if ($locked->notifications_envoyees || !$locked->publie_le || $locked->publie_le->isFuture()) {
                return;
            }

            $typeIds = $locked->typesEtablissement()->pluck('type_etablissements.id');
            $recipients = User::query()
                ->where('is_approved', true)
                ->when($locked->auteur_id, fn ($query) => $query->whereKeyNot($locked->auteur_id));

            if ($locked->cible === 'domaines' || $locked->ville || $locked->commune) {
                $recipients->whereHas('usersEtablissements.etablissement', function ($query) use ($locked, $typeIds) {
                    if ($locked->cible === 'domaines') {
                        $query->whereIn('type_etablissement_id', $typeIds);
                    }
                    if ($locked->ville) {
                        $query->whereRaw('LOWER(ville) = ?', [mb_strtolower($locked->ville)]);
                    }
                    if ($locked->commune) {
                        $query->whereRaw('LOWER(commune) = ?', [mb_strtolower($locked->commune)]);
                    }
                });
            }

            Notification::send($recipients->get(), new PublicationAvailable($locked));
            $locked->update(['notifications_envoyees' => true]);
        });
    }
}
