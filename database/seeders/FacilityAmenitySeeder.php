<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilityAmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('facility_amenities')->insert([
            // Mary Thomas Building (MT)
            [
                'facility_id' => 1,
                'amenity_name' => 'Projector',
                'amenity_fee' => 50.00,
                'quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 1,
                'amenity_name' => 'Whiteboard',
                'amenity_fee' => 0.00,
                'quantity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 1,
                'amenity_name' => 'PA System',
                'amenity_fee' => 100.00,
                'quantity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mary Thomas Computer Lab (MTCL)
            [
                'facility_id' => 2,
                'amenity_name' => 'Computer Workstation',
                'amenity_fee' => 0.00,
                'quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 2,
                'amenity_name' => 'Projector',
                'amenity_fee' => 25.00,
                'quantity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 2,
                'amenity_name' => 'Printer',
                'amenity_fee' => 10.00,
                'quantity' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Old Valentine Building (OV)
            [
                'facility_id' => 3,
                'amenity_name' => 'Lecture Hall Seating',
                'amenity_fee' => 0.00,
                'quantity' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 3,
                'amenity_name' => 'Podium',
                'amenity_fee' => 0.00,
                'quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Rose Memorial Auditorium (RMA)
            [
                'facility_id' => 4,
                'amenity_name' => 'Stage Lighting',
                'amenity_fee' => 150.00,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 4,
                'amenity_name' => 'Sound System',
                'amenity_fee' => 200.00,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 4,
                'amenity_name' => 'Dressing Rooms',
                'amenity_fee' => 0.00,
                'quantity' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // University Church
            [
                'facility_id' => 5,
                'amenity_name' => 'Pipe Organ',
                'amenity_fee' => 100.00,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 5,
                'amenity_name' => 'Pews',
                'amenity_fee' => 0.00,
                'quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // University Swimming Pool
            [
                'facility_id' => 6,
                'amenity_name' => 'Pool Equipment',
                'amenity_fee' => 50.00,
                'quantity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 6,
                'amenity_name' => 'Locker Rooms',
                'amenity_fee' => 0.00,
                'quantity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // University Gym
            [
                'facility_id' => 7,
                'amenity_name' => 'Treadmill',
                'amenity_fee' => 0.00,
                'quantity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 7,
                'amenity_name' => 'Weight Set',
                'amenity_fee' => 0.00,
                'quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 7,
                'amenity_name' => 'Exercise Bikes',
                'amenity_fee' => 0.00,
                'quantity' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Promenade Park
            [
                'facility_id' => 8,
                'amenity_name' => 'Picnic Tables',
                'amenity_fee' => 0.00,
                'quantity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 8,
                'amenity_name' => 'Outdoor Grill',
                'amenity_fee' => 50.00,
                'quantity' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Halfmoon Drive
            [
                'facility_id' => 9,
                'amenity_name' => 'Parking Spaces',
                'amenity_fee' => 0.00,
                'quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mary Thomas Computer Lab #1
            [
                'facility_id' => 10,
                'amenity_name' => 'Computer Workstation',
                'amenity_fee' => 0.00,
                'quantity' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 10,
                'amenity_name' => 'Interactive Whiteboard',
                'amenity_fee' => 30.00,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}