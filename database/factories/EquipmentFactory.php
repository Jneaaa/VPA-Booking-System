<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected static $counter = 1;
    protected $model = Equipment::class;

    public function definition(): array
    {
        $totalQuantity = $this->faker->numberBetween(1, 10);
        return [
            'equipment_name'       => 'Equipment #' . self::$counter++,
            'description'          => 'Insert a short description of the equipment here.',
            'brand'                => $this->faker->randomElement(['Brand name #1', 'Brand name #2', 'Brand name #3', 'Brand name #4']),
            'storage_location'     => $this->faker->randomElement(['Main Room', 'AV Room', 'Warehouse', 'Room 101']),
            'category_id'          => rand(1, 3), // Match seeded categories
            'total_quantity'       => $totalQuantity,
            'external_fee'         => $this->faker->randomFloat(2, 100, 1000),
            'rate_type'            => $this->faker->randomElement(['Per Hour', 'Per Event']),
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
