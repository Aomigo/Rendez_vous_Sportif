<?php

namespace App\Actions\Organization;

use App\Models\Organization;

class DeleteOrganizationAction
{
    public function handle(Organization $organization): void
    {
        // Les lignes de organization_user partent avec la suppression en cascade.
        $organization->delete();
    }
}
