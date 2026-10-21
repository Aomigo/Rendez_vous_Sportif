<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'organization_id' => Organization::factory(),
            'sport_id' => Sport::factory(),
            'created_by' => User::factory(),
            'description' => $this->faker->paragraph(),
            'starts_at' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
            'duration_minutes' => $this->faker->randomElement([45, 60, 90, 120]),
            'city' => $this->faker->randomElement(['Paris', 'Lyon', 'Marseille', 'Nantes', 'Bordeaux', 'Lille', 'Toulouse']),
            'venue' => $this->faker->streetAddress(),
            'capacity' => $this->faker->numberBetween(6, 24),
            'public_token' => Str::random(40),
            'status' => Event::STATUS_PUBLISHED,
        ];
    }
}

