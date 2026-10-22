<?php

namespace App\DTOs\Event;

use App\Models\Event;
use App\Models\User;

final readonly class RegistrationDTO
{
    public function __construct(
        public int $eventId,
        public int $userId,
    ) {}

    public static function fromModels(Event $event, User $user): self
    {
        return new self(eventId: $event->id, userId: $user->id);
    }
}
