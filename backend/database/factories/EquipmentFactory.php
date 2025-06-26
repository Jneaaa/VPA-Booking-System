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
            'equipment_name'     => $this->faker->unique()->words(2, true), // e.g. "Projector A"
            'description'        => $this->faker->optional()->sentence(),
            'brand'              => $this->faker->optional()->company,
            'storage_location'   => $this->faker->randomElement(['Main Room', 'AV Room', 'Warehouse', 'Room 101']),
            'category_id'        => rand(1, 3), // replace with actual seeded category IDs
            'type_id'            => rand(1, 3), // replace with real rate_type IDs
            'status_id'          => rand(1, 3), // replace with real status IDs
            'department_id'      => rand(1, 3), // replace with real department IDs
            'total_quantity'     => $this->faker->numberBetween(1, 10),
            'minimum_hour'       => $this->faker->numberBetween(1, 4),
            'rental_fee'         => $this->faker->randomFloat(2, 100, 1000),
            'company_fee'        => $this->faker->randomFloat(2, 50, 500),
            'created_by'         => 1, // or use Admin::inRandomOrder()->value('admin_id') later
            'updated_by'         => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ];
    }
}
