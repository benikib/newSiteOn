<?php

namespace App\Notifications;

use App\Models\Publication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PublicationAvailable extends Notification
{
    use Queueable;

    public function __construct(private readonly Publication $publication)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'publication_id' => $this->publication->id,
            'titre' => $this->publication->titre,
            'message' => 'Une nouvelle information BISIKA vous concerne.',
            'url' => route('publications.index'),
        ];
    }
}
