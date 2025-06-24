<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'facility_id' => $this->facility_id,
            'facility_name' => $this->facility_name,
            'description' => $this->description,
            'category' => [
                'category_id' => $this->category_id,
                'category_name' => $this->category->category_name,
            ],
            'subcategory' => $this->whenLoaded('subcategory', function () {
                return $this->subcategory ? [
                    'subcategory_id' => $this->subcategory_id,
                    'subcategory_name' => $this->subcategory->subcategory_name,
                ] : null;
            }),
            'details' => $this->whenLoaded('details', function () {
                return $this->details ? [
                    'room_name' => $this->details->room_name,
                    'building_code' => $this->details->building_code,
                    'room_number' => $this->details->room_number,
                    'floor_level' => $this->details->floor_level,
                ] : null;
            }),
            'location_note' => $this->location_note,
            'capacity' => $this->capacity,
            'department' => [
                'department_id' => $this->department_id,
                'department_name' => $this->department->department_name,
            ],
            'is_indoors' => $this->is_indoors,
            'rental_fee' => $this->rental_fee,
            'company_fee' => $this->company_fee,
            'status' => [
                'status_id' => $this->status_id,
                'status_name' => $this->status->status_name,
                'color_code' => $this->status->color_code,
            ],
            'equipment' => FacilityEquipmentResource::collection($this->whenLoaded('equipment')),
            'amenities' => FacilityAmenityResource::collection($this->whenLoaded('amenities')),
            'images' => FacilityImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_booked_at' => $this->last_booked_at,
        ];
    }
}