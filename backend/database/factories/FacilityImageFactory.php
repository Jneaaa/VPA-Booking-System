<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FacilityImage>
 */
class FacilityImageFactory extends Factory
{

    protected $model = FacilityImage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facility_id' => Facility::inRandomOrder()->value('facility_id') ?? Facility::factory(),
            'type_id' => 1, // Assuming 1 is 'primary' in image_types
            'sort_order' => 1, // You can override this in seeder to increment per facility
            'cloudinary_public_id' => 'rh0tmkdsb7rmb0ndzojv',
            'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp',
        ];
    }
}
