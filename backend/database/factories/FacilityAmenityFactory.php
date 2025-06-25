<?php

namespace Database\Factories;

use App\Models\FacilityAmenity;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FacilityAmenity>
 */
class FacilityAmenityFactory extends Factory
{
    protected $model = FacilityAmenity::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'facility_id' => null, // ✅ make this explicit so Laravel respects overrides
        'amenity_name' => $this->faker->randomElement([
            'Projector', 'Whiteboard', 'Sound System', 'Lighting Rig', 'Stage Platform',
            'Seating', 'Cooling Fan', 'AV Booth', 'Lectern', 'WiFi Router'
        ]),
        'amenity_fee' => $this->faker->optional()->randomFloat(2, 50, 1000),
        'quantity' => $this->faker->numberBetween(1, 5),
    ];
}
}
