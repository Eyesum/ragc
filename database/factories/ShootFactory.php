<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shoot>
 */
class ShootFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => 1,
            'name' => $this->faker->colorName() . " Shoot",
            'date' => $this->faker->dateTimeBetween('now', '+1 years'),
            'start_time_utc' => '09:00:00',
            'end_time_utc' => '14:00:00',
        ];
    }
}
