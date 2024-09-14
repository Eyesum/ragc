<?php

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = CarbonImmutable::now('UTC');
        $startOfSeason = $now->copy()->subMonths(6)->startOfMonth();
        $endOfSeason = $now->copy()->addMonths(5)->endOfMonth();
        $name = $startOfSeason->year . "-" . $endOfSeason->year . " Season";
        return [
            'name' => $name,
            'start_date' => $startOfSeason->format('Y-m-d'),
            'end_date' => $endOfSeason->format('Y-m-d'),
            'top_scores_used' => 9,
        ];
    }
}
