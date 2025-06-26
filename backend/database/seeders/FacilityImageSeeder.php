<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\FacilityImage;

class FacilityImageSeeder extends Seeder
{
    public function run(): void
    {
        Facility::factory()->count(10)->create()->each(function ($facility) {
            foreach (range(1, 2) as $i) {
                FacilityImage::factory()->create([
                    'facility_id' => $facility->facility_id,
                    'type_id' => $i === 1 ? 1 : 2,
                    'sort_order' => $i,
                ]);
            }
        });
    }
}