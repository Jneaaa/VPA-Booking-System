<?php

namespace Database\Factories;

use App\Models\EquipmentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentItem>
 */
class EquipmentItemFactory extends Factory
{
    protected $model = EquipmentItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipment_id' => null, // Will be set when creating
            'item_name' => $this->faker->words(2, true),
            'image_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp',
            'cloudinary_public_id' => 'oxvsxogzu9koqhctnf7s',
            'condition_id' => rand(1, 3), // Assuming condition IDs 1-3 exist
            'barcode_number' => $this->faker->unique()->bothify('??#########'),
            'item_notes' => $this->faker->optional()->sentence,
            'created_by' => null, // Will be set when creating
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}
