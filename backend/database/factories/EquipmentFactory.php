<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'equipment_name'       => $this->faker->unique()->words(2, true), // e.g., "Stage Lights"
            'description'          => $this->faker->optional()->text(100),
            'brand'                => $this->faker->optional()->company(),
            'storage_location'     => $this->faker->randomElement(['Main Room', 'AV Room', 'Warehouse', 'Room 101']),
            'category_id'          => rand(1, 3), // Match seeded categories
            'total_quantity'       => $this->faker->numberBetween(1, 10),
            'rental_fee'           => $this->faker->randomFloat(2, 100, 1000),
            'company_fee'          => $this->faker->randomFloat(2, 50, 500),
            'rate_type'            => $this->faker->randomElement(['Per Hour', 'Per Show/Event']),
            'status_id'            => rand(1, 3),
            'department_id'        => rand(1, 3),
            'maximum_rental_hour'  => $this->faker->numberBetween(1, 8),
            'created_by'           => 1, // You can randomize or use Admin::inRandomOrder()->value('admin_id')
            'updated_by'           => null,
            'deleted_by'           => null,
            'last_booked_at'       => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'created_at'           => now(),
            'updated_at'           => now(),
        ];
    }
}
