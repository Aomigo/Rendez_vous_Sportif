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
        // users::factory(10)->create();

        $user = users::factory()->count(10)->create();
        $organization = organizations::factory()->count(3)->create();
        $sport = sports::factory()->count(8)->create();
        events::factory()
            ->count(10)
            ->recycle([$organization, $sport, $user])
            ->create();
    }
}
