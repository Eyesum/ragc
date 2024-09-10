<?php

namespace Database\Factories;

use App\Enums\MembershipStatus;
use App\Models\Member;
use App\Models\MembershipType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $member = Member::all()->random();
        $membershipType = MembershipType::where('name', 'LIKE', 'Adult %')->get()->random();
        return [
            'member_id' => $member->id,
            'member_type' => $member::class,
            'membership_type_id' => $membershipType->id,
            'joined_date' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'status' => MembershipStatus::ACTIVE->value
        ];
    }
}
