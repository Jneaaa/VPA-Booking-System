<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FacilityEquipment;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FacilityEquipment>
 */
class FacilityEquipmentFactory extends Factory
{

    protected $model = FacilityEquipment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facility_id' => $this->faker->numberBetween(1, 10),
            'equipment_id' => $this->faker->numberBetween(1, 3),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }

    /**
     * Configure the model factory to ensure unique facility-equipment pairs.
     */
    public function configure()
    {
        return $this->afterCreating(function (FacilityEquipment $facilityEquipment) {
            // Ensure no duplicate facility-equipment pairs
            FacilityEquipment::where('facility_id', $facilityEquipment->facility_id)
                ->where('equipment_id', $facilityEquipment->equipment_id)
                ->where('facility_equipment_id', '!=', $facilityEquipment->facility_equipment_id)
                ->delete();
        });
    }
}
