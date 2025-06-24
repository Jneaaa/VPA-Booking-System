<?php

namespace App;

use App\Models\Facility;
use App\Models\FacilityDetail;
use App\Models\FacilityEquipment;
use App\Models\FacilityAmenity;
use App\Models\FacilityImage;
use Illuminate\Support\Facades\DB;

class FacilityService
{
    public function getAllFacilities()
    {
        return Facility::with(['category', 'subcategory', 'department', 'status', 'details', 'equipment.equipment', 'amenities.amenity', 'images'])
            ->orderBy('facility_name')
            ->get();
    }

    public function createFacility(array $data): Facility
    {
        return DB::transaction(function () use ($data) {
            $facility = Facility::create([
                'facility_name' => $data['facility_name'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'] ?? null,
                'location_note' => $data['location_note'] ?? null,
                'capacity' => $data['capacity'],
                'department_id' => $data['department_id'],
                'is_indoors' => $data['is_indoors'],
                'rental_fee' => $data['rental_fee'],
                'company_fee' => $data['company_fee'],
                'status_id' => $data['status_id'],
                'created_by' => auth()->id(),
            ]);

            // Create facility details if provided
            if (isset($data['details'])) {
                $facility->details()->create([
                    'room_name' => $data['details']['room_name'] ?? null,
                    'building_code' => $data['details']['building_code'] ?? null,
                    'room_number' => $data['details']['room_number'] ?? null,
                    'floor_level' => $data['details']['floor_level'] ?? null,
                ]);
            }

            // Attach equipment if provided
            if (isset($data['equipment'])) {
                foreach ($data['equipment'] as $equipment) {
                    $facility->equipment()->create([
                        'equipment_id' => $equipment['equipment_id'],
                    ]);
                }
            }

            // Attach amenities if provided
            if (isset($data['amenities'])) {
                foreach ($data['amenities'] as $amenity) {
                    $facility->amenities()->create([
                        'amenity_id' => $amenity['amenity_id'],
                        'quantity' => $amenity['quantity'],
                        'fee' => $amenity['fee'] ?? 0,
                    ]);
                }
            }

            // Create facility images if provided
            if (isset($data['images'])) {
                foreach ($data['images'] as $imageData) {
                    $facility->images()->create([
                        'image_url' => $imageData['image_url'],
                        'description' => $imageData['description'] ?? null,
                        'sort_order' => $imageData['sort_order'] ?? 0,
                        'image_type' => $imageData['image_type'],
                    ]);
                }
            }

            return $facility->load('details', 'equipment', 'amenities', 'images');
        });
    }

    public function updateFacility(Facility $facility, array $data): Facility
    {
        return DB::transaction(function () use ($facility, $data) {
            $facility->update([
                'facility_name' => $data['facility_name'] ?? $facility->facility_name,
                'description' => $data['description'] ?? $facility->description,
                'category_id' => $data['category_id'] ?? $facility->category_id,
                'subcategory_id' => $data['subcategory_id'] ?? $facility->subcategory_id,
                'location_note' => $data['location_note'] ?? $facility->location_note,
                'capacity' => $data['capacity'] ?? $facility->capacity,
                'department_id' => $data['department_id'] ?? $facility->department_id,
                'is_indoors' => $data['is_indoors'] ?? $facility->is_indoors,
                'rental_fee' => $data['rental_fee'] ?? $facility->rental_fee,
                'company_fee' => $data['company_fee'] ?? $facility->company_fee,
                'status_id' => $data['status_id'] ?? $facility->status_id,
                'updated_by' => auth()->id(),
            ]);

            // Update or create facility details
            if (isset($data['details'])) {
                if ($facility->details) {
                    $facility->details->update([
                        'room_name' => $data['details']['room_name'] ?? $facility->details->room_name,
                        'building_code' => $data['details']['building_code'] ?? $facility->details->building_code,
                        'room_number' => $data['details']['room_number'] ?? $facility->details->room_number,
                        'floor_level' => $data['details']['floor_level'] ?? $facility->details->floor_level,
                    ]);
                } else {
                    $facility->details()->create([
                        'room_name' => $data['details']['room_name'] ?? null,
                        'building_code' => $data['details']['building_code'] ?? null,
                        'room_number' => $data['details']['room_number'] ?? null,
                        'floor_level' => $data['details']['floor_level'] ?? null,
                    ]);
                }
            }

            // Sync equipment if provided
            if (isset($data['equipment'])) {
                $this->syncFacilityEquipment($facility, $data['equipment']);
            }

            // Sync amenities if provided
            if (isset($data['amenities'])) {
                $this->syncFacilityAmenities($facility, $data['amenities']);
            }

            // Sync images if provided
            if (isset($data['images'])) {
                $this->syncFacilityImages($facility, $data['images']);
            }

            return $facility->fresh()->load('details', 'equipment', 'amenities', 'images');
        });
    }

    protected function syncFacilityEquipment(Facility $facility, array $equipment): void
    {
        $currentEquipmentIds = $facility->equipment->pluck('equipment_id')->toArray();
        $newEquipmentIds = [];

        foreach ($equipment as $equipmentData) {
            $newEquipmentIds[] = $equipmentData['equipment_id'];
            
            // Update or create equipment association
            $facility->equipment()->updateOrCreate(
                ['equipment_id' => $equipmentData['equipment_id']],
                ['equipment_id' => $equipmentData['equipment_id']]
            );
        }

        // Remove equipment not in the new list
        $equipmentToRemove = array_diff($currentEquipmentIds, $newEquipmentIds);
        if (!empty($equipmentToRemove)) {
            $facility->equipment()->whereIn('equipment_id', $equipmentToRemove)->delete();
        }
    }

    protected function syncFacilityAmenities(Facility $facility, array $amenities): void
    {
        $currentAmenityIds = $facility->amenities->pluck('amenity_id')->toArray();
        $newAmenityIds = [];

        foreach ($amenities as $amenityData) {
            $newAmenityIds[] = $amenityData['amenity_id'];
            
            // Update or create amenity association
            $facility->amenities()->updateOrCreate(
                ['amenity_id' => $amenityData['amenity_id']],
                [
                    'quantity' => $amenityData['quantity'],
                    'fee' => $amenityData['fee'] ?? 0,
                ]
            );
        }

        // Remove amenities not in the new list
        $amenitiesToRemove = array_diff($currentAmenityIds, $newAmenityIds);
        if (!empty($amenitiesToRemove)) {
            $facility->amenities()->whereIn('amenity_id', $amenitiesToRemove)->delete();
        }
    }

    protected function syncFacilityImages(Facility $facility, array $images): void
    {
        $currentImageIds = $facility->images->pluck('facility_photo_id')->toArray();
        $newImageIds = [];

        foreach ($images as $imageData) {
            if (isset($imageData['facility_photo_id'])) {
                // Update existing image
                $image = $facility->images()->find($imageData['facility_photo_id']);
                if ($image) {
                    $image->update([
                        'image_url' => $imageData['image_url'] ?? $image->image_url,
                        'description' => $imageData['description'] ?? $image->description,
                        'sort_order' => $imageData['sort_order'] ?? $image->sort_order,
                        'image_type' => $imageData['image_type'] ?? $image->image_type,
                    ]);
                    $newImageIds[] = $image->facility_photo_id;
                }
            } else {
                // Create new image
                $newImage = $facility->images()->create([
                    'image_url' => $imageData['image_url'],
                    'description' => $imageData['description'] ?? null,
                    'sort_order' => $imageData['sort_order'] ?? 0,
                    'image_type' => $imageData['image_type'],
                ]);
                $newImageIds[] = $newImage->facility_photo_id;
            }
        }

        // Delete images not present in the new data
        $imagesToDelete = array_diff($currentImageIds, $newImageIds);
        if (!empty($imagesToDelete)) {
            $facility->images()->whereIn('facility_photo_id', $imagesToDelete)->delete();
        }
    }

    public function deleteFacility(Facility $facility): void
    {
        DB::transaction(function () use ($facility) {
            $facility->details()->delete();
            $facility->equipment()->delete();
            $facility->amenities()->delete();
            $facility->images()->delete();
            $facility->delete();
        });
    }
}