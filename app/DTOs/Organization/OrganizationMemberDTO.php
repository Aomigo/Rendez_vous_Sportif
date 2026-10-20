<?php

namespace App\DTOs\Organization;

use App\Models\Users;

final readonly class OrganizationMemberDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public bool $isOwner,
    ) {}

    /**
     * Attend un Users chargé via Organizations::members() (pivot présent).
     */
    public static function fromModel(Users $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isOwner: (bool) $user->pivot->is_owner,
        );
    }
}
