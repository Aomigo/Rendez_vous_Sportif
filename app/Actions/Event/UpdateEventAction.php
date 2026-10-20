<?php

namespace App\Actions\Event;

use App\DTOs\Event\EventDTO;
use App\Models\Event;
use Illuminate\Validation\ValidationException;

class UpdateEventAction
{
    /**
     * @param  EventDTO|array<string, mixed>  $data
     */
    public function handle(Event $event, EventDTO|array $data): Event
    {
        $dto = $data instanceof EventDTO ? $data : EventDTO::fromArray($data);

        $confirmedCount = $event->confirmedParticipantsCount();

        if ($dto->capacity < $confirmedCount) {
            throw ValidationException::withMessages([
                'capacity' => "La capacité ne peut pas être inférieure au nombre d'inscriptions confirmées ({$confirmedCount}).",
            ]);
        }

        $event->update($dto->toArray());

        return $event;
    }
}

