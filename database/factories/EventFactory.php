<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
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
        $minStartTime = Carbon::now()->setTime(9, 00,00);
        $maxStartTime = Carbon::now()->setTime(17, 00, 00);
        $startTime = Carbon::parse($this->faker->dateTimeBetween(
            $minStartTime->format('Y-m-d H:i:s'),
            $maxStartTime->format('Y-m-d H:i:s')
        ));
        $endTime = $startTime->copy()->addHours(rand(1, 6));

        return [
            'season_id' => 1,
            'name' => $this->faker->colorName() . ' Event',
            'date' => $this->faker->dateTimeBetween('now', '+1 years'),
            'start_time_utc' => $startTime,
            'end_time_utc' => $endTime,
        ];
    }
}
