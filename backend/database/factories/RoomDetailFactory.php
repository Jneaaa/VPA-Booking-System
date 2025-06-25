<?php

namespace Database\Factories;

use App\Models\RoomDetail;
use App\Models\FacilitySubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomDetail>
 */
class RoomDetailFactory extends Factory
{

    protected $model = RoomDetail::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subcategory_id' => FacilitySubcategory::inRandomOrder()->value('subcategory_id'),
            'room_name' => $this->faker->words(2, true), // e.g., "Lecture Room"
            'building_name' => $this->faker->optional()->company . ' Building',
            'building_code' => strtoupper($this->faker->lexify('???')),
            'room_number' => strtoupper($this->faker->bothify('R##')),
            'floor_level' => $this->faker->numberBetween(1, 10),
        ];
    }
}
