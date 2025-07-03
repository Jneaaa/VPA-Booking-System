<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RequisitionForm;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\EquipmentItem;
use App\Models\RequestedFacility;
use App\Models\RequestedEquipment;
use App\Models\CalendarEvent;
use App\Models\Admin;
use Carbon\Carbon;
use App\Models\FacilityEquipment;
use App\Models\EquipmentImage;
use App\Models\FacilityImage;
use App\Models\FacilityDetail;
use App\Models\LookupTables\FacilitySubcategory;
use App\Models\FacilityAmenity;

class RequisitionTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        if (Admin::count() === 0) {
            Admin::factory()->count(3)->create();
        }

        // Create regular users
        if (User::count() === 0) {
            User::factory()->count(10)->create();
        }

        // Get subcategories that require room details
        $detailSubcategories = FacilitySubcategory::whereIn('category_id', [2, 3]) // Indoor Facilities, Residencies
            ->pluck('subcategory_id')
            ->toArray();

        // Create facilities
        if (Facility::count() === 0) {
            $facilities = Facility::factory()->count(10)->create();

            foreach ($facilities as $facility) {
                FacilityImage::factory()->create([
                    'facility_id' => $facility->facility_id,
                    'image_type' => 'Primary',
                    'sort_order' => 0,
                ]);

                FacilityImage::factory()->count(rand(0, 2))->create([
                    'facility_id' => $facility->facility_id,
                    'image_type' => 'Secondary',
                ]);
            }
        }

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

        // Create facility-equipment relationships
        FacilityEquipment::factory()->count(20)->create();

        // Create requisition forms
        for ($i = 0; $i < 15; $i++) {
            $user = User::inRandomOrder()->first();

            // Pick a facility for this requisition
            $facility = Facility::inRandomOrder()->first();

            // Conditionally create room details
            $detailId = null;
            if (in_array($facility->subcategory_id, $detailSubcategories)) {
                $detail = FacilityDetail::factory()->create([
                    'subcategory_id' => $facility->subcategory_id,
                    'room_number' => $this->generateRoomNumber($facility->subcategory_id),
                    'floor_level' => rand(1, 5),
                    'building_name' => $this->generateBuildingName($facility->subcategory_id),
                    'building_code' => strtoupper(fake()->lexify('??')),
                ]);
                $detailId = $detail->detail_id;
            }

            // Create requisition form with detail_id only if applicable
            $requisition = RequisitionForm::factory()->create([
                'user_id' => $user->user_id,
                'status_id' => rand(1, 10),
                'purpose_id' => rand(1, 10),
                'start_date' => Carbon::today()->addDays(rand(-30, 30)),
                'end_date' => Carbon::today()->addDays(rand(1, 7)),
                'is_finalized' => fake()->boolean(70),
                'detail_id' => $detailId,
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

            // Calendar event if Active or Ongoing
            if (in_array($requisition->status_id, [4, 5]) &&
                ($requisition->start_date > now() || ($requisition->start_date <= now() && $requisition->end_date >= now()))) {
                CalendarEvent::create([
                    'request_id' => $requisition->request_id,
                    'event_title' => 'Reservation: ' . $user->name,
                    'eventDesc' => 'Status: ' . $requisition->status->status_name,
                    'created_by' => Admin::inRandomOrder()->value('admin_id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function generateRoomNumber($subcategoryId): string
    {
        $prefixes = [
            4 => 'CR',
            5 => 'CNF',
            6 => 'CL',
            7 => 'SL',
            8 => 'DR',
            9 => 'GR',
        ];

        $prefix = $prefixes[$subcategoryId] ?? 'RM';
        return $prefix . fake()->numerify('###');
    }

    private function generateBuildingName($subcategoryId): string
    {
        $buildings = [
            4 => 'Academic Building',
            5 => 'Administration Building',
            6 => 'Computer Center',
            7 => 'Science Complex',
            8 => 'Dormitory Building',
            9 => 'Guest House',
        ];

        return $buildings[$subcategoryId] ?? 'Building ' . fake()->word();
    }
}
