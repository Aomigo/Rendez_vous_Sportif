<?php

namespace Database\Seeders;

use App\Models\Events;
use App\Models\Organizations;
use App\Models\Sports;
use App\Models\Users;
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

        users::factory()->count(20)->create();
        events::factory()->count(20)->create();
        organizations::factory()->count(20)->create();
        sports::factory()->count(8)->create();
    }
}
