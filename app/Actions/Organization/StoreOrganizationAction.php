<?php

namespace App\Actions\Organization;

use App\Models\Organizations;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreOrganizationAction
{
    public function handle(Users $owner, string $name): Organizations
    {
        return DB::transaction(function () use ($owner, $name) {
            $organization = Organizations::create([
                'name' => $name,
                // Suffixe aléatoire : deux collectifs peuvent porter le même nom.
                'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            ]);

            $organization->members()->attach($owner, ['is_owner' => true]);

            return $organization;
        });
    }
}
