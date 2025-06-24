<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('equipment_images')->insert([
            [
                'equipment_id'         => 1,
                'image_url'            => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750747769/rh0tmkdsb7rmb0ndzojv.jpg',
                'cloudinary_public_id' => 'rh0tmkdsb7rmb0ndzojv',
                'type_id'              => 1,
                'sort_order'           => 0,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'equipment_id'         => 2,
                'image_url'            => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750748063/kdtz4lxvk89ut7rkngqm.jpg',
                'cloudinary_public_id' => 'kdtz4lxvk89ut7rkngqm',
                'type_id'              => 1,
                'sort_order'           => 0,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'equipment_id'         => 3,
                'image_url'            => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750748093/ubr7cunwo6rirq54ayxp.png',
                'cloudinary_public_id' => 'ubr7cunwo6rirq54ayxp',
                'type_id'              => 1,
                'sort_order'           => 0,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);
    }
}