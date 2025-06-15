<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RateTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rate_types')->insert([
            [
                'type_name' => 'Per Hour',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Per Show',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Per Event',
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
