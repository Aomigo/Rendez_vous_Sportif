<?php

namespace App\Actions\Organization;

use App\Models\Organizations;

class DeleteOrganizationAction
{
    public function handle(Organizations $organization): void
    {
        // Les lignes de organization_user partent avec la suppression en cascade.
        $organization->delete();
    }
}
