<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitionPurpose;

class RequisitionPurposeSeeder extends Seeder
{
    public function run(): void
    {
        $purposes = [
            'Facility Rental',
            'Equipment Rental',
            'Class/Seminar/Conference',
            'University Program/Activity',
            'CPU Organization Led Activity',
            'Student-Organized Activity',
            'Alumni-Organized Activity',
            'Alumni - Class Reunion',
            'Alumni - Personal Events',
            'External Event',
        ];

        foreach ($purposes as $name) {
            RequisitionPurpose::firstOrCreate(['purpose_name' => $name]);
        }
    }
}

