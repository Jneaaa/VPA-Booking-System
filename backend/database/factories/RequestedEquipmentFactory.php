<?php

namespace Database\Factories;

use App\Models\RequestedEquipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestedEquipment>
 */
class RequestedEquipmentFactory extends Factory
{

    protected $model = RequestedEquipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipment_id' => \App\Models\Equipment::inRandomOrder()->value('equipment_id'),
            'is_waived' => $this->faker->boolean(20),
        ];
    }
}

