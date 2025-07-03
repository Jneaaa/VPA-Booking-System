<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormStatusCode;

class FormStatusCodeSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Pending Approval',   'color_code' => '#FFA500'], // Orange
            ['status_name' => 'In Review',          'color_code' => '#00BFFF'], // Deep Sky Blue
            ['status_name' => 'Awaiting Payment',   'color_code' => '#FF69B4'], // Hot Pink
            ['status_name' => 'Scheduled',          'color_code' => '#9370DB'], // Medium Purple
            ['status_name' => 'Ongoing',            'color_code' => '#1E90FF'], // Dodger Blue
            ['status_name' => 'Returned',           'color_code' => '#20B2AA'], // Light Sea Green
            ['status_name' => 'Late Return',        'color_code' => '#DC143C'], // Crimson
            ['status_name' => 'Completed',          'color_code' => '#32CD32'], // Lime Green
            ['status_name' => 'Rejected',           'color_code' => '#B22222'], // Firebrick
            ['status_name' => 'Cancelled',          'color_code' => '#A9A9A9'], // Dark Gray
        ];

        foreach ($statuses as $status) {
            FormStatusCode::firstOrCreate(
                ['status_name' => $status['status_name']],
                ['color_code' => $status['color_code']]
            );
        }
    }
}
