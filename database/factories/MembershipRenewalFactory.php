<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipRenewal>
 */
class MembershipRenewalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::now()->subMonths(rand(1, 10));
        return [
            'membership_id' => 1,
            'start_date' => $startDate->format('Y-m-d'),
            'renewal_date' => $startDate->addYear()->format('Y-m-d'),
            'reminder_date' => $startDate->addMonths(11)->format('Y-m-d'),
            'paid_date' => $startDate->format('Y-m-d'),
        ];
    }
}
