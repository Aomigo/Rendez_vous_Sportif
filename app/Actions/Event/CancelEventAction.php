<?php

namespace App\Actions\Event;

use App\Models\Event;

class CancelEventAction
{
    public function handle(Event $event): Event
    {
        // Les inscriptions de eventParticipants sont conservées pour l'historique.
        $event->update(['status' => Event::STATUS_CANCELLED]);

        return $event;
    }
}

