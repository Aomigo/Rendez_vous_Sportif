<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class eventsFactory extends Factory
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
            'description' => $this->faker->paragraph(),
            'start_date' => $this->faker->date(),
            'duration_minutes' => $this->faker->randomDigit(),
            'city' => $this->faker->city(),
            'venue' => $this->faker->address(),
            'capacity' => $this->faker->randomDigit(),
            'public_token' => $this->faker->randomDigit(),
            'status' => "En cours",
        ];
    }
}
