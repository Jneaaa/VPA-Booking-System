<?php

namespace Database\Factories;

use App\Models\RequestedEquipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestedEquipmentFactory extends Factory
{
    protected $model = RequestedEquipment::class;

    public function definition(): array
    {
        return [
            'request_id' => \App\Models\RequisitionForm::inRandomOrder()->value('request_id'),
            'equipment_id' => \App\Models\Equipment::inRandomOrder()->value('equipment_id'),
            'is_waived' => $this->faker->boolean(20),
        ];
    }

}
