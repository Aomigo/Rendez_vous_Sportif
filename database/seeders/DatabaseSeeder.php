<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\Organization;
use App\Models\Sports;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = User::factory()->count(20)->create();
        $users->push(User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']));
        Events::factory()->count(20)->create();

        // 4 membres par organisation, le premier est owner.
        Organization::factory()->count(20)->create()->each(
            fn (Organization $organization) => $users->random(4)->values()->each(
                fn (User $user, int $i) => $organization->members()->attach($user, ['is_owner' => $i === 0])
            )
        );
        Sports::factory()->count(8)->create();
    }
}
