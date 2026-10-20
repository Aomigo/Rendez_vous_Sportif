<?php

namespace App\DTOs\Event;

use App\Models\Event;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final readonly class EventDTO
{
    public function __construct(
        public int $sportId,
        public string $title,
        public string $description,
        public CarbonInterface $startsAt,
        public int $durationMinutes,
        public string $city,
        public string $venue,
        public int $capacity,
        public ?int $id = null,
        public ?int $organizationId = null,
        public ?string $publicToken = null,
        public ?string $status = null,
        public int $confirmedParticipantsCount = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sportId: (int) $data['sport_id'],
            title: (string) $data['title'],
            description: (string) $data['description'],
            startsAt: CarbonImmutable::parse($data['starts_at']),
            durationMinutes: (int) $data['duration_minutes'],
            city: (string) $data['city'],
            venue: (string) $data['venue'],
            capacity: (int) $data['capacity'],
            organizationId: isset($data['organization_id']) ? (int) $data['organization_id'] : null,
        );
    }

    public static function fromModel(Event $event): self
    {
        return new self(
            sportId: (int) $event->sport_id,
            title: $event->title,
            description: $event->description,
            startsAt: CarbonImmutable::parse($event->starts_at),
            durationMinutes: (int) $event->duration_minutes,
            city: $event->city,
            venue: $event->venue,
            capacity: (int) $event->capacity,
            id: $event->id,
            organizationId: (int) $event->organization_id,
            publicToken: $event->public_token,
            status: $event->status,
            confirmedParticipantsCount: $event->confirmedParticipantsCount(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sport_id' => $this->sportId,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->startsAt,
            'duration_minutes' => $this->durationMinutes,
            'city' => $this->city,
            'venue' => $this->venue,
            'capacity' => $this->capacity,
        ];
    }

    public function isCancelled(): bool
    {
        return $this->status === Event::STATUS_CANCELLED;
    }
}

