<?php

namespace App\Policies;

use App\Models\Organizations;
use App\Models\Users;

class OrganizationPolicy
{
    /**
     * Voir l'organisation et la choisir comme organisation active.
     */
    public function view(Users $user, Organizations $organization): bool
    {
        return $organization->hasMember($user);
    }

    public function update(Users $user, Organizations $organization): bool
    {
        return $organization->isOwnedBy($user);
    }

    public function delete(Users $user, Organizations $organization): bool
    {
        return $organization->isOwnedBy($user);
    }

    public function manageMembers(Users $user, Organizations $organization): bool
    {
        return $organization->isOwnedBy($user);
    }
}
