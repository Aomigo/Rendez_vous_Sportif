<?php

namespace App\Actions\Organization;

use App\Models\Organization;

class UpdateOrganizationAction
{
    public function handle(Organization $organization, string $name): Organization
    {
        // Le slug ne change pas pour que les liens existants restent valides.
        $organization->update(['name' => $name]);

        return $organization;
    }
}
