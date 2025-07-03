<?php

namespace Database\Factories;

use App\Models\RequestedFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestedFacilityFactory extends Factory
{
    protected $model = RequestedFacility::class;

    public function definition(): array
    {
        return [
            'request_id' => \App\Models\RequisitionForm::inRandomOrder()->value('request_id'),
            'facility_id' => \App\Models\Facility::inRandomOrder()->value('facility_id'),
            'is_waived' => $this->faker->boolean(20),
        ];
    }

    
}