<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class EquipmentController extends Controller
{

    // ----- Indexes ----- //

    public function publicIndex(): JsonResponse
    {
        try {
            $equipment = Equipment::with([
                'category',
                'status',
                'department',
                'items' => function ($query) {
                    $query->where('status_id', '!=', 5); // Exclude hidden items
                },
                'items.condition',
                'images'
            ])->orderBy('equipment_name')->get();

            $formatted = $equipment->map(function ($item) {
                // Calculate available quantity (status=1 AND condition in [1,2,3])
                $availableCount = $item->items
                    ->filter(function ($item) {
                        return $item->status_id == 1 && in_array($item->condition_id, [1, 2, 3]);
                    })
                    ->count();

                // Calculate total quantity (all non-hidden items)
                $totalCount = $item->items->count();

                return array_merge(
                    $this->formatPublicEquipment($item),
                    [
                        'images' => $item->images,
                        'available_quantity' => $availableCount,
                        'total_quantity' => $totalCount
                    ]
                );
            });

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            \Log::error('Error fetching public equipment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to fetch equipment data',
                'error' => $e->getMessage()
            ], 500);
        }
    }







    // ----- EQUIPMENT MANAGEMENT SECTION ----- //







    // ----- Store Equipment ----- //

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'equipment_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:80',
            'storage_location' => 'required|string|max:50',
            'category_id' => 'required|exists:equipment_categories,category_id',
            'total_quantity' => 'required|integer|min:1',
            'internal_fee' => 'required|numeric|min:0',
            'external_fee' => 'required|numeric|min:0',
            'rate_type' => 'required|in:Per Hour,Per Event',
            'status_id' => 'required|exists:availability_statuses,status_id',
            'department_id' => 'required|exists:departments,department_id',
            'maximum_rental_hour' => 'nullable|integer',

            'items' => 'sometimes|array',
            'items.*.item_name' => 'sometimes|string|max:100',
            'items.*.condition_id' => 'required|exists:conditions,condition_id',
            'items.*.barcode_number' => 'nullable|string|max:100',
            'items.*.item_notes' => 'nullable|string',

            'images' => 'sometimes|array',
            'images.*.image_url' => 'required|url|max:500',
            'images.*.description' => 'nullable|string',
            'images.*.sort_order' => 'sometimes|integer',
            'images.*.type_id' => 'required|exists:image_types,type_id',
        ]);

        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($data['department_id'])) {
            return response()->json(['message' => 'You do not manage this department.'], 403);
        }

        $equipment = Equipment::create([
            'equipment_name' => $data['equipment_name'],
            'description' => $data['description'] ?? null,
            'brand' => $data['brand'] ?? null,
            'storage_location' => $data['storage_location'],
            'category_id' => $data['category_id'],
            'total_quantity' => $data['total_quantity'],
            'internal_fee' => $data['internal_fee'],
            'external_fee' => $data['external_fee'],
            'rate_type' => $data['rate_type'],
            'status_id' => $data['status_id'],
            'department_id' => $data['department_id'],
            'maximum_rental_hour' => $data['maximum_rental_hour'],
            'created_by' => $user->admin_id
        ]);

        // Optional: Handle items and images if provided
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $equipment->items()->create($item);
            }
        }

        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $equipment->images()->create($image);
            }
        }

        return response()->json([
            'message' => 'Equipment created successfully',
            'data' => $this->formatEquipment($equipment->fresh()) // refresh with relations
        ], 201);
    }


    // ----- Display Equipment ----- //

    public function show(Equipment $equipment): JsonResponse
    {
        $equipment->load(['category', 'status', 'department', 'items.condition', 'images']);

        return response()->json([
            'data' => $this->formatEquipment($equipment),
        ]);
    }


    // ----- Update Equipment ----- //

    public function update(Request $request, Equipment $equipment): JsonResponse
    {
        $data = $request->validate([
            'equipment_name' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:80',
            'storage_location' => 'sometimes|string|max:50',
            'category_id' => 'sometimes|exists:equipment_categories,category_id',
            'total_quantity' => 'sometimes|integer|min:1',
            'internal_fee' => 'sometimes|numeric|min:0',
            'external_fee' => 'sometimes|numeric|min:0',
            'rate_type' => 'sometimes|in:Per Hour,Per Event',
            'status_id' => 'sometimes|exists:availability_statuses,status_id',
            'department_id' => 'sometimes|exists:departments,department_id',
            'maximum_rental_hour' => 'nullable|integer',
        ]);

        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $equipment->update([
            'equipment_name' => $data['equipment_name'] ?? $equipment->equipment_name,
            'description' => $data['description'] ?? $equipment->description,
            'brand' => $data['brand'] ?? $equipment->brand,
            'storage_location' => $data['storage_location'] ?? $equipment->storage_location,
            'category_id' => $data['category_id'] ?? $equipment->category_id,
            'total_quantity' => $data['total_quantity'] ?? $equipment->total_quantity,
            'internal_fee' => $data['internal_fee'] ?? $equipment->internal_fee,
            'external_fee' => $data['external_fee'] ?? $equipment->external_fee,
            'rate_type' => $data['rate_type'] ?? $equipment->rate_type,
            'status_id' => $data['status_id'] ?? $equipment->status_id,
            'department_id' => $data['department_id'] ?? $equipment->department_id,
            'maximum_rental_hour' => $data['maximum_rental_hour'] ?? $equipment->maximum_rental_hour,
            'updated_by' => $user->admin_id,
        ]);

        return response()->json([
            'message' => 'Equipment updated successfully',
            'data' => $this->formatEquipment($equipment->fresh()),
        ]);
    }


    // ----- Remove Equipment ----- //
    public function destroy(Equipment $equipment): JsonResponse
    {
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        // Soft delete tracking
        $equipment->update([
            'deleted_by' => $user->id,
        ]);

        // Remove related records
        $equipment->items()->delete();
        $equipment->images()->delete();

        // Delete equipment record
        $equipment->delete();

        return response()->json(['message' => 'Equipment deleted successfully']);
    }

    // ----- Upload Equipment Images ----- //

    public function uploadImage(Request $request, $equipmentId): JsonResponse
    {
        // Validate the uploaded image and data
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        // Find the equipment
        $equipment = Equipment::findOrFail($equipmentId);

        // Check if the user is authorized to upload for this equipment
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        // Upload to Cloudinary
        $uploaded = Cloudinary::upload(
            $request->file('image')->getRealPath(),
            ['upload_preset' => 'equipment-photos']
        );

        $imageUrl = $uploaded->getSecurePath();
        $publicId = $uploaded->getPublicId();

        // Determine image type if not provided
        $imageType = $validated['type_id'] ??
            ($equipment->images()->count() == 0 ? 1 : 2); // First image = primary

        // Create the image record
        $equipment->images()->create([
            'image_url' => $imageUrl,
            'type_id' => $imageType,
            'cloudinary_public_id' => $publicId,
            'description' => $validated['description'] ?? null,
            'sort_order' => $equipment->images()->count() + 1
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'type' => $imageType == 1 ? 'primary' : 'additional'
        ]);
    }


    // ----- Upload Images In Bulk ----- //
    public function uploadMultipleImages(Request $request, $equipmentId): JsonResponse
    {
        // Validate the images and optional type_id
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id',
        ]);

        // Find the equipment
        $equipment = Equipment::findOrFail($equipmentId);

        // Authorization: ensure the admin manages this equipment's department
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $currentImageCount = $equipment->images()->count();
        $typeId = $validated['type_id'] ?? null;

        foreach ($validated['images'] as $index => $image) {
            // Upload to Cloudinary
            $upload = Cloudinary::upload(
                $image->getRealPath(),
                ['upload_preset' => 'equipment-photos']
            );

            $imageUrl = $upload->getSecurePath();
            $publicId = $upload->getPublicId();

            // Decide image type (first image = primary if none specified)
            $imageType = $typeId ?? (($currentImageCount + $index) === 0 ? 1 : 2);

            // Create image record
            $equipment->images()->create([
                'image_url' => $imageUrl,
                'type_id' => $imageType,
                'cloudinary_public_id' => $publicId,
                'description' => $validated['description'] ?? null,
                'sort_order' => $currentImageCount + $index + 1,
            ]);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }


    // ----- Delete Equipment Images ----- //

    public function deleteImage($equipmentId, $imageId): JsonResponse
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $user = auth()->user();

        // Authorization check
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $image = $equipment->images()->findOrFail($imageId);

        // Delete the image from Cloudinary if it exists
        if ($image->cloudinary_public_id) {
            Cloudinary::destroy($image->cloudinary_public_id);
        }

        // Delete local copy from storage if applicable
        $path = str_replace('/storage', 'public', parse_url($image->image_url, PHP_URL_PATH));
        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        // Delete DB record
        $image->delete();

        // Reorder remaining images
        $this->reorderImageRecords($equipment);

        return response()->json(['message' => 'Image deleted successfully']);
    }


    private function reorderImageRecords(Equipment $equipment): void
    {
        $images = $equipment->images()->orderBy('sort_order')->get();
        foreach ($images as $index => $image) {
            $image->update(['sort_order' => $index + 1]);
        }
    }

    // ----- Reorder Images ----- //

    public function reorderImages(Request $request, $equipmentId): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:equipment_images,image_id'
        ]);

        $equipment = Equipment::findOrFail($equipmentId);
        $user = auth()->user();

        // Authorization check
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        foreach ($request->input('order') as $position => $imageId) {
            $equipment->images()->where('image_id', $imageId)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['message' => 'Images reordered successfully']);
    }


    // ----- Formatting ----- //

    private function formatEquipment($equipment): array
    {
        $equipment->load(['category', 'status', 'department', 'items', 'images']);

        return [
            'equipment_id' => $equipment->equipment_id,
            'equipment_name' => $equipment->equipment_name,
            'description' => $equipment->description,
            'brand' => $equipment->brand,
            'storage_location' => $equipment->storage_location,
            'category' => [
                'category_id' => $equipment->category_id,
                'category_name' => $equipment->category->category_name,
            ],
            'total_quantity' => $equipment->total_quantity,
            'internal_fee' => $equipment->internal_fee,
            'external_fee' => $equipment->external_fee,
            'rate_type' => $equipment->rate_type,
            'status' => [
                'status_id' => $equipment->status_id,
                'status_name' => $equipment->status->status_name,
                'color_code' => $equipment->status->color_code,
            ],
            'department' => [
                'department_id' => $equipment->department_id,
                'department_name' => $equipment->department->department_name,
            ],
            'maximum_rental_hour' => $equipment->maximum_rental_hour,
            'items' => $equipment->items,
            'images' => $equipment->images,
            'created_at' => $equipment->created_at,
            'updated_at' => $equipment->updated_at,
        ];
    }

    private function formatPublicEquipment($equipment): array
    {
        $equipment->load(['category', 'status', 'department', 'items.condition', 'images']);

        return [
            'equipment_id' => $equipment->equipment_id,
            'equipment_name' => $equipment->equipment_name,
            'description' => $equipment->description,
            'brand' => $equipment->brand,
            'storage_location' => $equipment->storage_location,
            'category' => [
                'category_id' => $equipment->category_id,
                'category_name' => $equipment->category->category_name,
            ],
            'total_quantity' => $equipment->total_quantity,
            'internal_fee' => $equipment->internal_fee,
            'external_fee' => $equipment->external_fee,
            'rate_type' => $equipment->rate_type,
            'status' => [
                'status_id' => $equipment->status_id,
                'status_name' => $equipment->status->status_name,
                'color_code' => $equipment->status->color_code,
            ],
            'department' => [
                'department_id' => $equipment->department_id,
                'department_name' => $equipment->department->department_name,
            ],
            'items' => $equipment->items,
            'images' => $equipment->images,
        ];
    }


}