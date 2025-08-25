<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'photo_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751050880/ppcqunrpfuyf2fqos4y9.jpg',
                'photo_public_id' => 'ppcqunrpfuyf2fqos4y9',
                'wallpaper_url' => '',
                'wallpaper_public_id' => '',
                'first_name' => 'Hannah',
                'last_name' => 'Escosar',
                'middle_name' => 'O.',
                'role_id' => '1',
                'school_id' => '2289-09',
                'email' => 'hannahescosar@gmail.com',
                'contact_number' => '77777777',
                'hashed_password' => Hash::make('hisoka123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'photo_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751050882/wsqs9eysc0jbrvydsssr.jpg',
                'photo_public_id' => 'wsqs9eysc0jbrvydsssr',
                'wallpaper_url' => '',
                'wallpaper_public_id' => '',
                'first_name' => 'Janea',
                'last_name' => 'Geresola',
                'middle_name' => '',
                'role_id' => '4',
                'school_id' => '2249-12',
                'email' => 'janea@gmail.com',
                'contact_number' => '',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'photo_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751050879/rbt2ftf4ovwcj7awyugd.jpg',
                'photo_public_id' => 'rbt2ftf4ovwcj7awyugd',
                'wallpaper_url' => '',
                'wallpaper_public_id' => '',
                'first_name' => 'Chin',
                'last_name' => 'Dasal',
                'middle_name' => '',
                'role_id' => '1',
                'school_id' => '4153-12',
                'email' => 'chin@gmail.com',
                'contact_number' => '',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
         ]);
    }
}
