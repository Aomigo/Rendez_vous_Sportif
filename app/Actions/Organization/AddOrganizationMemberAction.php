<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AddOrganizationMemberAction
{
    public function handle(Organization $organization, string $email): User
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($organization->hasMember($user)) {
            throw ValidationException::withMessages(['email' => 'Cet utilisateur est déjà membre.']);
        }

        $organization->members()->attach($user, ['is_owner' => false]);

        return $user;
    }
}
