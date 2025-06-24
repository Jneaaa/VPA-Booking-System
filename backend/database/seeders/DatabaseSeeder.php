<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ActionTypeSeeder::class,
            AdminRoleSeeder::class,
            AvailabilityStatusSeeder::class,
            ConditionSeeder::class,
            DepartmentSeeder::class,
            EquipmentCategorySeeder::class,
            ImageTypeSeeder::class,
            RateTypeSeeder::class,
            AdminSeeder::class,
            EquipmentSeeder::class,
            EquipmentItemSeeder::class,
            EquipmentImageSeeder::class,
            // Add other seeders here if needed
        ]);
    }
}
