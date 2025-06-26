<?php

namespace Database\Factories;

use App\Models\RequisitionForm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequisitionForm>
 */
class RequisitionFormFactory extends Factory
{

    protected $model = RequisitionForm::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'access_code' => strtoupper(Str::random(10)),
            'num_participants' => $this->faker->numberBetween(5, 100),

            'purpose_id' => \App\Models\RequisitionPurpose::inRandomOrder()->value('purpose_id'),
            'other_purpose' => $this->faker->optional()->sentence(),
            'additional_requests' => $this->faker->optional()->sentence(),

            'status_id' => \App\Models\FormStatus::inRandomOrder()->value('status_id'),


            'start_date' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks')->format('Y-m-d'),
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),

            'is_late' => false,
            'late_penalty_fee' => null,
            'returned_at' => null,

            'is_finalized' => false,
            'finalized_at' => null,
            'finalized_by' => null,

            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,

            'endorser' => $this->faker->optional()->name(),
            'date_endorsed' => $this->faker->optional()->date(),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
