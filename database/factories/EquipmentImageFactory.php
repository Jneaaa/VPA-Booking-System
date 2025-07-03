<?php

namespace Database\Factories;

use App\Models\EquipmentImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentImage>
 */
class EquipmentImageFactory extends Factory
{
    protected $model = EquipmentImage::class;

    public function definition(): array
    {
        return [
            'equipment_id' => $this->faker->numberBetween(1, 10),
            'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp',
            'cloudinary_public_id' => 'oxvsxogzu9koqhctnf7s',
            'description' => 'A short description of the image here.',
            'sort_order' => $this->faker->numberBetween(0, 10),
            'image_type' => $this->faker->randomElement(['Primary', 'Secondary']),
        ];
    }
}