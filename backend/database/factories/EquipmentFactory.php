<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipment_name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'brand' => $this->faker->company(),
            'storage_location' => $this->faker->word(),
            'category_id' => 1, // or random existing category
            'total_quantity' => rand(1, 10),
            'rental_fee' => $this->faker->randomFloat(2, 100, 500),
            'company_fee' => $this->faker->randomFloat(2, 10, 100),
            'type_id' => 1, // corresponds to rate_types
            'status_id' => 1, // availability_statuses
            'department_id' => 1, // departments
            'minimum_hour' => rand(1, 8),
            'created_by' => 1, // make sure there's a valid admin_id
            'updated_by' => null,
        ];
    }
}
