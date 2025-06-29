<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\FacilityAmenity;
use App\Models\FacilityDetail;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Step 1: Create facilities and store them
    $facilities = Facility::factory()->count(10)->create();

    // Step 2: Loop over each saved facility
    foreach ($facilities as $facility) {
        // Extra check: force save if needed
        if (!$facility->exists || !$facility->facility_id) {
            $facility->save();
        }

        if ($facility->category_id === 4) {
            $detail = FacilityDetail::where('category_id', $facility->category_id)->inRandomOrder()->first();
            if ($detail) {
                $facility->detail_id = $detail->detail_id;
                $facility->save();
            }
        }

        // Final debug check
        if (!$facility->facility_id) {
            dump('⚠️ NULL facility ID:', $facility);
            continue; // skip to next
        }

        // Create 1–3 amenities
        for ($i = 0; $i < rand(1, 3); $i++) {
            FacilityAmenity::factory()->create([
                'facility_id' => $facility->facility_id,
            ]);
        }
    }
}
}
