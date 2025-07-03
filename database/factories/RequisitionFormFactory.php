<?php

namespace Database\Factories;

use App\Models\RequisitionForm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RequisitionFormFactory extends Factory
{
    protected $model = RequisitionForm::class;

    public function definition(): array
    {
        // Get a random facility subcategory that requires details
        $detailSubcategories = [4,5,6,7,8,9]; // IDs for subcategories that need details
        
        return [
            'user_id' => \App\Models\User::factory(),
            'access_code' => strtoupper(Str::random(10)),
            'num_participants' => $this->faker->numberBetween(5, 100),
            'purpose_id' => \App\Models\RequisitionPurpose::inRandomOrder()->value('purpose_id') ?? 1,
            
            // Other purpose with 30% chance
            'other_purpose' => $this->faker->optional(0.3, null)->passthrough('Insert another purpose explanation here.'),
            
            // Additional requests with 50% chance
            'additional_requests' => $this->faker->optional(0.5)->sentence(),
            
            'status_id' => \App\Models\FormStatusCode::inRandomOrder()->value('status_id') ?? 1,
            
            // Room details - will be set conditionally in the seeder
            'detail_id' => null,
            
            // Booking schedule
            'start_date' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks')->format('Y-m-d'),
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
            
            // Late returns
            'is_late' => false,
            'late_penalty_fee' => null,
            'returned_at' => null,
            
            // Finalization
            'is_finalized' => false,
            'finalized_at' => null,
            'finalized_by' => null,
            
            // Close form
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
            
            // Endorsement
            'endorser' => $this->faker->optional()->name(),
            'date_endorsed' => $this->faker->optional()->date(),
            
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to optionally add room details
     */
    public function configure()
    {
        return $this->afterCreating(function (RequisitionForm $requisition) {
            // The actual detail_id assignment is handled in the seeder
            // to ensure proper facility-subcategory relationship
        });
    }
}