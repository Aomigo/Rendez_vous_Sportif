<?php

namespace App\Actions\Organization;

use App\Models\Organizations;

class UpdateOrganizationAction
{
    public function handle(Organizations $organization, string $name): Organizations
    {
        // Le slug ne change pas pour que les liens existants restent valides.
        $organization->update(['name' => $name]);

        return $organization;
    }
}
