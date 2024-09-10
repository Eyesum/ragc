<?php

namespace Database\Factories;

use App\Enums\RelationshipType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmergencyContact>
 */
class EmergencyContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'relationship' => $this->faker->randomElement(array_column(RelationshipType::cases(), 'value')),
            'address_line1' => null,
            'address_line2' => null,
            'city' => null,
            'county' => null,
            'postcode' => null,
            'contact_number' => $this->faker->phoneNumber(),
        ];
    }
}
