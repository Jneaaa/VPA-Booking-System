<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormStatus;

class FormStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Pending Approval',   'color_code' => '#707485ff'],
            ['status_name' => 'Awaiting Payment',   'color_code' => '#1c5b8fff'], 
            ['status_name' => 'Scheduled',          'color_code' => '#1e7941ff'], 
            ['status_name' => 'Ongoing',            'color_code' => '#ac7a0fff'], 
            ['status_name' => 'Late',               'color_code' => '#8f2a2aff'], 
            ['status_name' => 'Returned',           'color_code' => '#3e5568ff'], 
            ['status_name' => 'Late Return',        'color_code' => '#3e5568ff'], 
            ['status_name' => 'Completed',          'color_code' => '#3e5568ff'], 
            ['status_name' => 'Rejected',           'color_code' => '#3e5568ff'], 
            ['status_name' => 'Cancelled',          'color_code' => '#3e5568ff'], 
        ];

        foreach ($statuses as $status) {
            FormStatus::firstOrCreate(
                ['status_name' => $status['status_name']],
                ['color_code' => $status['color_code']]
            );
        }
    }
}
