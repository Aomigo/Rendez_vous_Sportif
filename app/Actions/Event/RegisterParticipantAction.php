<?php

namespace App\Actions\Event;

use App\DTOs\Event\RegistrationDTO;
use App\Events\ParticipantRegistered;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterParticipantAction
{
    /**
     * @throws ValidationException
     */
    public function handle(RegistrationDTO $registration): Event
    {
        return DB::transaction(function () use ($registration): Event {
            // Verrou sur l'événement : deux inscriptions simultanées ne peuvent pas prendre la même dernière place.
            $event = Event::lockForUpdate()->findOrFail($registration->eventId);
            $participant = User::findOrFail($registration->userId);

            $refusal = match (true) {
                $event->isCancelled() => 'Cet événement a été annulé.',
                $event->isPast() => 'Cet événement est déjà passé.',
                $event->participants()->whereKey($participant->id)->exists() => 'Vous êtes déjà inscrit à cet événement.',
                $event->confirmedParticipantsCount() >= $event->capacity => 'Cet événement est complet.',
                default => null,
            };

            if ($refusal !== null) {
                throw ValidationException::withMessages(['registration' => $refusal]);
            }

            $event->participants()->attach($participant->id, [
                'status' => Event::PARTICIPANT_CONFIRMED,
                'registered_at' => now(),
            ]);

            ParticipantRegistered::dispatch($event, $participant);

            return $event;
        });
    }
}
