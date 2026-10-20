<?php

namespace App\Actions\Organization;

use App\Models\Organizations;
use App\Models\Users;
use Illuminate\Validation\ValidationException;

class AddOrganizationMemberAction
{
    public function handle(Organizations $organization, string $email): Users
    {
        $user = Users::where('email', $email)->firstOrFail();

        if ($organization->hasMember($user)) {
            throw ValidationException::withMessages(['email' => 'Cet utilisateur est déjà membre.']);
        }

        $organization->members()->attach($user, ['is_owner' => false]);

        return $user;
    }
}
