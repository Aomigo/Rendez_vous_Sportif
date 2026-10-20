<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\Organizations;
use App\Models\Sports;
use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Model>
 */
class EventsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'organization_id' => organizations::factory()->create(),
            'sport_id' => sports::factory()->create(),
            'created_by' => users::factory()->create(),
            'description' => $this->faker->paragraph(),
            'starts_at' => $this->faker->date(),
            'duration_minutes' => $this->faker->randomDigit(),
            'city' => $this->faker->city(),
            'venue' => $this->faker->address(),
            'capacity' => $this->faker->randomDigit(),
            'public_token' => Str::random(40),
            'status' => "En cours",
        ];
    }
}

