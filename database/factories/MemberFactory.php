<?php

namespace Database\Factories;

use App\Enums\IdType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create User for Member first
        $user = User::factory()->create();

        $address = $this->faker->streetAddress();
        $addressArray = explode(PHP_EOL, $address);

        return [
            'user_id' => $user->id,
            'membership_number' => $this->faker->unique()->randomNumber(3, false),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->getAdultDateOfBirth(),
            'contact_number' => $this->faker->phoneNumber(),
            'address_line1' => $addressArray[0],
            'address_line2' => $addressArray[1] ?? null,
            'city' => $this->faker->city(),
            'county' => $this->faker->city(),
            'postcode' => $this->faker->postcode(),
            'id_type_seen' => $this->faker->randomElement(array_column(IdType::cases(), 'value')),
        ];
    }

    /**
     * @return string
     */
    private function getAdultDateOfBirth(): string
    {
        $endDate = Carbon::now()->subYears(21)->format('Y-m-d');
        $startDate = Carbon::now()->subYears(70)->format('Y-m-d');

        return $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d');
    }
}
