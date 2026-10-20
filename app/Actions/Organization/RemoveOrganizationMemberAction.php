<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RemoveOrganizationMemberAction
{
    public function handle(Organization $organization, User $member): void
    {
        // Sans owner, plus personne ne pourrait gérer l'organisation.
        if ($organization->isOwnedBy($member)) {
            throw ValidationException::withMessages(['member' => 'Le owner ne peut pas être retiré.']);
        }

        $organization->members()->detach($member);
    }
}
