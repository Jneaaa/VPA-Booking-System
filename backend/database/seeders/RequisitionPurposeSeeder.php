<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RequisitionPurpose;

class RequisitionPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purposes = [
            'Facility Rental',
            'Equipment Rental',
            'Subject Requirement - Class, Seminar, Conference',
            'University Program/Activity',
            'CPU Organization Led Activity',
            'Student-Organized Activity',
            'Alumni-Organized Events',
            'Alumni - Class Reunion',
            'Alumni - Personal Events',
            'External Event',
        ];

        foreach ($purposes as $name) {
            RequisitionPurpose::firstOrCreate(['purpose_name' => $name]);
        }
    }
}
