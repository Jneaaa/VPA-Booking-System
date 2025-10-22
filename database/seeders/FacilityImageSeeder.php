<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilityImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('facility_images')->insert([
            [
                'facility_id' => 1,
                'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761022004/chi9l3pmql5rqbjuuite.jpg',
                'cloudinary_public_id' => 'chi9l3pmql5rqbjuuite',
                'description' => 'Primary image for Rose Memorial Auditorium',
                'sort_order' => 1,
                'image_type' => 'Primary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 2,
                'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761022003/ktjpuy8hrxcuaj3e3yps.jpg',
                'cloudinary_public_id' => 'ktjpuy8hrxcuaj3e3yps',
                'description' => 'Primary image for OV Building',
                'sort_order' => 1,
                'image_type' => 'Primary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 3,
                'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761022004/tltunooxn00siepok7f9.jpg',
                'cloudinary_public_id' => 'tltunooxn00siepok7f9',
                'description' => 'Primary image for University Church',
                'sort_order' => 1,
                'image_type' => 'Primary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 4,
                'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761022003/xkjjd6xqloyibeqn9k6g.jpg',
                'cloudinary_public_id' => 'xkjjd6xqloyibeqn9k6g',
                'description' => 'Primary image for UG Building',
                'sort_order' => 1,
                'image_type' => 'Primary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 5,
                'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761022003/dw9zbgwf49migcp3lwzv.jpg',
                'cloudinary_public_id' => 'dw9zbgwf49migcp3lwzv',
                'description' => 'Primary image for University Church',
                'sort_order' => 1,
                'image_type' => 'Primary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 6,
                'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761022002/lrwsepkao8aedajy6k1s.jpg',
                'cloudinary_public_id' => 'lrwsepkao8aedajy6k1s',
                'description' => 'Primary image for Mary Thomas Building',
                'sort_order' => 1,
                'image_type' => 'Primary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
