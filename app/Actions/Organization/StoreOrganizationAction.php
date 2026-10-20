<?php

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreOrganizationAction
{
    public function handle(User $owner, string $name): Organization
    {
        return DB::transaction(function () use ($owner, $name) {
            $organization = Organization::create([
                'name' => $name,
                // Suffixe aléatoire : deux collectifs peuvent porter le même nom.
                'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            ]);

            $organization->members()->attach($owner, ['is_owner' => true]);

            return $organization;
        });
    }
}
