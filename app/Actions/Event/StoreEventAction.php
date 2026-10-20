<?php

namespace App\Actions\Event;

use App\DTOs\Event\EventDTO;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;

class StoreEventAction
{
    /**
     * @param  EventDTO|array<string, mixed>  $data
     */
    public function handle(User $creator, Organization $organization, EventDTO|array $data): Event
    {
        $dto = $data instanceof EventDTO ? $data : EventDTO::fromArray($data);

        return Event::create([
            ...$dto->toArray(),
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'public_token' => Str::random(40),
            'status' => Event::STATUS_PUBLISHED,
        ]);
    }
}

