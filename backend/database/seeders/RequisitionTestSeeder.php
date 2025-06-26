<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RequisitionForm;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\RequestedFacility;
use App\Models\RequestedEquipment;
use App\Models\RequisitionPurpose;
use App\Models\FormStatusCode;
use App\Models\CalendarEvent;
use App\Models\Admin;


class RequisitionTestSeeder extends Seeder
{
   // RequisitionTestSeeder.php
public function run(): void
{
    // Seed lookup tables first
    if (RequisitionPurpose::count() == 0) {
        $this->call(RequisitionPurposesSeeder::class);
    }

    // Create users, facilities, and equipment first
    $users = User::factory()->count(10)->create();
    $facilities = Facility::factory()->count(10)->create();
    $equipment = Equipment::factory()->count(10)->create();

    // Create requisition forms
    $requisitions = RequisitionForm::factory()->count(15)->create();

    // Now create related records with explicit request_ids
    foreach ($requisitions as $form) {
        // Ensure the form has been saved and has an ID
        if ($form->request_id) {
            RequestedFacility::factory()->count(3)->create([
                'request_id' => $form->request_id,
            ]);

            RequestedEquipment::factory()->count(rand(1, 3))->create([
                'request_id' => $form->request_id,
                'equipment_id' => Equipment::inRandomOrder()->value('equipment_id'),
            ]);

            if (rand(0, 1)) {
                CalendarEvent::factory()->create([
                    'request_id' => $form->request_id,
                    'created_by' => Admin::inRandomOrder()->value('admin_id'),
                ]);
            }
        }
    }
}
}