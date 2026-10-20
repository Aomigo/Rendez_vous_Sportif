<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Sport;
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
        $user = User::factory()->count(10)->create();
        $organization = Organization::factory()->count(3)->create();
        $sport = Sport::factory()->count(8)->create();
        Event::factory()
            ->count(10)
            ->recycle([$organization, $sport, $user])
            ->create();
    }
}
