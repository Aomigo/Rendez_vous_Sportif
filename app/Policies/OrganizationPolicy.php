<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Voir l'organisation et la choisir comme organisation active.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $organization->hasMember($user);
    }

    public function update(User $user, Organization $organization): bool
    {
        return $organization->isOwnedBy($user);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $organization->isOwnedBy($user);
    }

    public function manageMembers(User $user, Organization $organization): bool
    {
        return $organization->isOwnedBy($user);
    }
}
