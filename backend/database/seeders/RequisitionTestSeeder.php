<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RequisitionForm;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\RequestedFacility;
use App\Models\RequestedEquipment;
use App\Models\CalendarEvent;
use App\Models\Admin;

class RequisitionTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create users, facilities, and equipment first
        $users = User::factory()->count(10)->create();
        $facilities = Facility::factory()->count(10)->create();
        
        // Create equipment with valid rate_type
        $equipment = Equipment::factory()->count(10)->create([
            'rate_type' => fake()->randomElement(['Per Hour', 'Per Show/Event']),
        ]);

        // Create requisition forms
        $requisitions = RequisitionForm::factory()->count(15)->create();

        // Create related records
        foreach ($requisitions as $form) {
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
