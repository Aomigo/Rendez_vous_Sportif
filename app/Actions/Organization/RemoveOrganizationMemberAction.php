<?php

namespace App\Actions\Organization;

use App\Models\Organizations;
use App\Models\Users;
use Illuminate\Validation\ValidationException;

class RemoveOrganizationMemberAction
{
    public function handle(Organizations $organization, Users $member): void
    {
        // Sans owner, plus personne ne pourrait gérer l'organisation.
        if ($organization->isOwnedBy($member)) {
            throw ValidationException::withMessages(['member' => 'Le owner ne peut pas être retiré.']);
        }

        $organization->members()->detach($member);
    }
}
