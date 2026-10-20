<?php

namespace App\DTOs\Organization;

use App\Models\Organization;

final readonly class OrganizationDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public bool $isOwner,
        public int $membersCount,
    ) {}

    /**
     * Attend un model chargé avec withCount('members').
     */
    public static function fromModel(Organization $organization, bool $isOwner): self
    {
        return new self(
            id: $organization->id,
            name: $organization->name,
            slug: $organization->slug,
            isOwner: $isOwner,
            membersCount: $organization->members_count,
        );
    }

    public function role(): string
    {
        return $this->isOwner ? 'Owner' : 'Membre';
    }
}
