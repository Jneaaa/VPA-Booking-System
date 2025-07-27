<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitionForm;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\EquipmentItem;
use App\Models\RequestedFacility;
use App\Models\RequestedEquipment;
use App\Models\Admin;
use Carbon\Carbon;
use App\Models\EquipmentImage;
use App\Models\FacilityAmenity;
use Illuminate\Support\Str;

class RequisitionTestSeeder extends Seeder
{
    public function run(): void
    {

        // Create equipment
        if (Equipment::count() === 0) {
            $equipment = Equipment::factory()->count(10)->create();

            foreach ($equipment as $item) {
                EquipmentImage::factory()->create([
                    'equipment_id' => $item->equipment_id,
                    'image_type' => 'Primary',
                    'sort_order' => 0,
                ]);

                EquipmentImage::factory()->count(rand(0, 2))->create([
                    'equipment_id' => $item->equipment_id,
                    'image_type' => 'Secondary',
                ]);

                EquipmentItem::factory()->count(rand(1, 5))->create([
                    'equipment_id' => $item->equipment_id,
                    'created_by' => Admin::inRandomOrder()->value('admin_id'),
                    'condition_id' => rand(1, 3),
                ]);
            }
        }

        // Create requisition forms
        for ($i = 0; $i < 15; $i++) {

            // Pick a facility for this requisition
            $facility = Facility::inRandomOrder()->first();

            // Create requisition form with additional fields
            $requisition = RequisitionForm::factory()->create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->safeEmail(),
                'school_id' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
                'organization_name' => fake()->optional()->company(),
                'contact_number' => fake()->optional()->regexify('[0-9]{10,15}'),
                'status_id' => rand(1, 10),
                'purpose_id' => rand(1, 10),
                'start_date' => Carbon::today()->addDays(rand(-30, 30)),
                'end_date' => Carbon::today()->addDays(rand(1, 7)),
                'start_time' => Carbon::createFromTime(rand(8, 18), 0, 0),
                'end_time' => Carbon::createFromTime(rand(19, 23), 0, 0),
                'access_code' => Str::random(10),
                'num_participants' => rand(10, 100),
                'additional_requests' => fake()->optional()->sentence(),
                'formal_letter_url' => fake()->url(),
                'formal_letter_public_id' => Str::uuid(),
                'facility_layout_url' => fake()->url(),
                'facility_layout_public_id' => Str::uuid(),
                'late_penalty_fee' => null,
                'is_late' => false,
                'returned_at' => null,
                'is_finalized' => false,
                'finalized_at' => null,
                'finalized_by' => null,
                'official_receipt_no' => null,
                'official_receipt_url' => null,
                'official_receipt_public_id' => null,
                'tentative_fee' => fake()->randomFloat(2, 1000, 5000),
                'approved_fee' => null,
                'is_closed' => false,
                'closed_at' => null,
                'closed_by' => null,
                'endorser' => null,
                'date_endorsed' => null,
                'calendar_title' => 'Rental Request',
                'calendar_description' => 'Rental request for facility usage',
            ]);

            // Attach facility
            RequestedFacility::create([
                'request_id' => $requisition->request_id,
                'facility_id' => $facility->facility_id,
                'is_waived' => fake()->boolean(20),
            ]);

            for ($j = 0; $j < rand(1, 3); $j++) {
                FacilityAmenity::factory()->create([
                    'facility_id' => $facility->facility_id,
                ]);
            }

            // Attach equipment
            $selectedEquipment = Equipment::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($selectedEquipment as $item) {
                RequestedEquipment::create([
                    'request_id' => $requisition->request_id,
                    'equipment_id' => $item->equipment_id,
                    'quantity' => fake()->numberBetween(1, min(5, $item->total_quantity)),
                    'is_waived' => fake()->boolean(20),
                ]);
            }
        }
    }
}