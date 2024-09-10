<?php

namespace Database\Factories;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JuniorMember>
 */
class JuniorMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $member = Member::all()->random();

        return [
            'member_id' => $member->id,
            'membership_number' => $this->getUniqueMembershipId(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $member->last_name,
            'date_of_birth' => $this->getChildDateOfBirth(),
        ];
    }

    /**
     * @return int|null
     */
    private function getUniqueMembershipId(): ?int
    {
        $membershipNumbers = Member::pluck('membership_number')->toArray();

        $maxTries = 50;
        $tryCount = 1;
        while (($tryCount <= $maxTries)) {
            $uniqueMembershipNumber = $this->faker->unique()->randomNumber(3, false);
            if (!in_array($uniqueMembershipNumber, $membershipNumbers)) {
                return $uniqueMembershipNumber;
            }
            $tryCount++;
        }

        return null;
    }

    /**
     * @return string
     */
    private function getChildDateOfBirth(): string
    {
        $startDate = Carbon::now()->subYears(18)->format('Y-m-d');
        $endDate = Carbon::now()->subYears(10)->format('Y-m-d');

        return $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d');
    }
}
