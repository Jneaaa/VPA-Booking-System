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

        $startDate = $this->faker->dateTimeBetween('now', '+1 week');
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 weeks');
        $startTime = $this->faker->time('H:i'); // Exclude seconds
        $endTime = $this->faker->time('H:i', strtotime($startTime) + 3600); // Ensure at least 1 hour difference
        
        return [
            'access_code' => strtoupper(Str::random(10)),
            'num_participants' => $this->faker->numberBetween(5, 100),
            'purpose_id' => \App\Models\RequisitionPurpose::inRandomOrder()->value('purpose_id') ?? 1,
            
            // Additional requests with 50% chance
            'additional_requests' => $this->faker->optional(0.5)->sentence(),
            
            // Formal letter details
            'formal_letter_url' => $this->faker->url(),
            'formal_letter_public_id' => $this->faker->uuid(),
            
            // Facility layout details
            'facility_layout_url' => $this->faker->url(),
            'facility_layout_public_id' => $this->faker->uuid(),
            
            'status_id' => \App\Models\FormStatus::inRandomOrder()->value('status_id') ?? 1,
            
            // Booking schedule
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            
            // Late returns
            'is_late' => false,
            'late_penalty_fee' => null,
            'returned_at' => null,
            
            // Finalization
            'is_finalized' => false,
            'finalized_at' => null,
            'finalized_by' => null,
            
            // Official receipt details
            'official_receipt_no' => $this->faker->optional()->regexify('[A-Z0-9]{10}'),
            'official_receipt_url' => $this->faker->optional()->url(),
            'official_receipt_public_id' => $this->faker->optional()->uuid(),
            
            // Fee details
            'tentative_fee' => $this->faker->optional()->randomFloat(2, 100, 1000),
            'approved_fee' => $this->faker->optional()->randomFloat(2, 100, 1000),
            
            // Close form
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
            
            // Endorsement
            'endorser' => $this->faker->optional()->name(),
            'date_endorsed' => $this->faker->optional()->date(),
            
            // Calendar details
            'calendar_title' => 'Rental Request',
            'calendar_description' => 'Rental request for facility usage',
            
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