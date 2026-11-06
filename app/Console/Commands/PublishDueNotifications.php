<?php

namespace App\Console\Commands;

use App\Models\Publication;
use App\Services\PublicationPublisher;
use Illuminate\Console\Command;

class PublishDueNotifications extends Command
{
    protected $signature = 'notifications:publish-due';

    protected $description = 'Envoie les notifications des publications programmées arrivées à échéance.';

    public function handle(PublicationPublisher $publisher): int
    {
        Publication::query()
            ->where('notifications_envoyees', false)
            ->whereNotNull('publie_le')
            ->where('publie_le', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expire_le')->orWhere('expire_le', '>=', now());
            })
            ->orderBy('id')
            ->chunkById(100, function ($publications) use ($publisher) {
                foreach ($publications as $publication) {
                    $publisher->notifyIfDue($publication);
                }
            });

        return self::SUCCESS;
    }
}
