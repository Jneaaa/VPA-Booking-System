<?php

namespace Database\Factories;

use App\Models\RequestedFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestedFacility>
 */
class RequestedFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = RequestedFacility::class;

    public function definition(): array
    {
        return [
            'facility_id' => \App\Models\Facility::inRandomOrder()->value('facility_id'),
            'is_waived' => $this->faker->boolean(20),
            // Remove any request_id generation from here
        ];
    }
}