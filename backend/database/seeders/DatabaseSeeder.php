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
            ActionTypesSeeder::class,
            AdminRolesSeeder::class,
            AvailabilityStatusesSeeder::class,
            ConditionsSeeder::class,
            DepartmentsSeeder::class,
            EquipmentCategoriesSeeder::class,
            ImageTypesSeeder::class,
            RateTypesSeeder::class,
            AdminsSeeder::class,
            EquipmentSeeder::class,
            EquipmentItemsSeeder::class,
            // Add other seeders here if needed
        ]);
    }
}
