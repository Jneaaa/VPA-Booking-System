<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class EquipmentController extends Controller
{
    /**
     * Display a listing of equipment.
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Allow head admins to see all equipment
            if ($user->role_id === \App\AdminRoles::HEAD_ADMIN) {
                $equipment = Equipment::with(['category', 'status', 'department', 'type', 'items', 'images'])
                    ->orderBy('equipment_name')
                    ->get();
            } else {
                $equipment = Equipment::whereIn('department_id', $user->departments->pluck('id'))
                    ->with(['category', 'status', 'department', 'type', 'items', 'images'])
                    ->orderBy('equipment_name')
                    ->get();
            }

            $formatted = $equipment->map(fn ($item) => $this->formatEquipment($item));

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            \Log::error('Error fetching equipment data', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch equipment data', 'error' => $e->getMessage()], 500);
        }
    }

    public function publicIndex(): JsonResponse
{
    try {
        $equipment = Equipment::with(['category', 'rateType', 'status', 'items.condition'])
            ->orderBy('equipment_name')
            ->get();

        $formatted = $equipment->map(fn ($item) => $this->formatEquipment($item));

        return response()->json(['data' => $formatted]);
    } catch (\Exception $e) {
        \Log::error('Error fetching public equipment', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to fetch equipment data', 'error' => $e->getMessage()], 500);
    }
}


    /**
     * Store newly created equipment in storage.
     */
    public function store(Request $request): JsonResponse
    {

        $data = $request->validate([
            'equipment_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:80',
            'storage_location' => 'required|string|max:50',
            'category_id' => 'required|exists:equipment_categories,category_id',
            'total_quantity' => 'required|integer|min:1',
            'rental_fee' => 'required|numeric|min:0',
            'company_fee' => 'required|numeric|min:0',
            'type_id' => 'required|exists:rate_types,type_id',
            'status_id' => 'required|exists:availability_statuses,status_id',
            'department_id' => 'required|exists:departments,department_id',
            'minimum_hour' => 'required|integer|min:1',
        
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

        if (!$user->departments->pluck('id')->contains($data['department_id'])) {
            return response()->json(['message' => 'You do not manage this department.'], 403);
        }

        $equipment = Equipment::create([
            'equipment_name' => $data['equipment_name'],
            'description' => $data['description'] ?? null,
            'brand' => $data['brand'] ?? null,
            'storage_location' => $data['storage_location'],
            'category_id' => $data['category_id'],
            'total_quantity' => $data['total_quantity'],
            'rental_fee' => $data['rental_fee'],
            'company_fee' => $data['company_fee'],
            'type_id' => $data['type_id'],
            'status_id' => $data['status_id'],
            'department_id' => $data['department_id'],
            'minimum_hour' => $data['minimum_hour'],
            'created_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Equipment created successfully',
            'data' => $this->formatEquipment($equipment),
        ], 201);

    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment): JsonResponse
    {
        return response()->json([
            'data' => $this->formatEquipment($equipment),
        ]);
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(Request $request, Equipment $equipment): JsonResponse
    {
        $data = $request->validate([
            'equipment_name' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:80',
            'storage_location' => 'sometimes|string|max:50',
            'category_id' => 'sometimes|exists:equipment_categories,category_id',
            'total_quantity' => 'sometimes|integer|min:1',
            'rental_fee' => 'sometimes|numeric|min:0',
            'company_fee' => 'sometimes|numeric|min:0',
            'type_id' => 'sometimes|exists:rate_types,type_id',
            'status_id' => 'sometimes|exists:availability_statuses,status_id',
            'department_id' => 'sometimes|exists:departments,department_id',
            'minimum_hour' => 'sometimes|integer|min:1',
        ]);

        $user = auth()->user();

        if (!$user->departments->pluck('id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $equipment->update([
            'equipment_name' => $data['equipment_name'] ?? $equipment->equipment_name,
            'description' => $data['description'] ?? $equipment->description,
            'brand' => $data['brand'] ?? $equipment->brand,
            'storage_location' => $data['storage_location'] ?? $equipment->storage_location,
            'category_id' => $data['category_id'] ?? $equipment->category_id,
            'total_quantity' => $data['total_quantity'] ?? $equipment->total_quantity,
            'rental_fee' => $data['rental_fee'] ?? $equipment->rental_fee,
            'company_fee' => $data['company_fee'] ?? $equipment->company_fee,
            'type_id' => $data['type_id'] ?? $equipment->type_id,
            'status_id' => $data['status_id'] ?? $equipment->status_id,
            'department_id' => $data['department_id'] ?? $equipment->department_id,
            'minimum_hour' => $data['minimum_hour'] ?? $equipment->minimum_hour,
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Equipment updated successfully',
            'data' => $this->formatEquipment($equipment),
        ]);
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment): JsonResponse
    {
        $user = auth()->user();

        if (!$user->departments->pluck('id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $equipment->items()->delete();
        $equipment->images()->delete();
        $equipment->delete();

        return response()->json(['message' => 'Equipment deleted successfully']);

    }

    /**
     * Upload an image for the equipment
     */
    public function uploadImage(Request $request, $equipmentId): JsonResponse
    {
        $uploadResponse = Cloudinary::upload($image->getRealPath(), [
            'upload_preset' => 'equipment-photos'
        ]);
        
        // Validate the uploaded image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        // Find the equipment
        $equipment = Equipment::findOrFail($equipmentId);

        // Handle file upload (store in storage/app/public/equipment-images)
        // Upload to Cloudinary and get secure URL
        $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
        $imageUrl = $uploadedFileUrl;

        // Determine image type if not provided
        $imageType = $request->input('type_id') ??
            ($equipment->images()->count() == 0 ? 1 : 2); // First = primary, rest = additional

        // Create the equipment image record
        $equipment->images()->create([
            'image_url' => $imageUrl,
            'type_id' => $imageType,
            'cloudinary_public_id' => $publicId,
            'description' => $request->input('description'),
            'sort_order' => $equipment->images()->count() + 1
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'type' => $imageType == 1 ? 'primary' : 'additional'
        ]);
    }

    /**
     * Bulk upload images for the equipment
     */
    public function uploadMultipleImages(Request $request, $equipmentId): JsonResponse
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        $equipment = Equipment::findOrFail($equipmentId);
        $currentImageCount = $equipment->images()->count();
        $typeId = $request->input('type_id');

        foreach ($request->file('images') as $index => $image) {
            $uploadedFileUrl = Cloudinary::upload($image->getRealPath())->getSecurePath();
            $imageUrl = $uploadedFileUrl;

            // Use provided type_id if available, otherwise determine automatically
            $imageType = $typeId ?? (($currentImageCount + $index) == 0 ? 1 : 2);

            $equipment->images()->create([
                'image_url' => $imageUrl,
                'type_id' => $imageType,
                'cloudinary_public_id' => $publicId,
                'description' => $request->input('description'),
                'sort_order' => $currentImageCount + $index + 1
            ]);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }

    /**
     * Delete an equipment image
     */
    public function deleteImage($equipmentId, $imageId): JsonResponse
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $image = $equipment->images()->findOrFail($imageId);

        // Delete all images from cloudinary if equipment is deleted
        foreach ($equipment->images as $image) {
            if ($image->cloudinary_public_id) {
                Cloudinary::destroy($image->cloudinary_public_id);
            }
        }
        $equipment->images()->delete();
        $equipment->items()->delete();
        $equipment->delete();

        // Delete the file from storage
        $path = str_replace('/storage', 'public', parse_url($image->image_url, PHP_URL_PATH));
        Storage::delete($path);

        // Delete the record
        $image->delete();

        if ($image->cloudinary_public_id) {
            Cloudinary::destroy($image->cloudinary_public_id);
        }  

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

    /**
     * Reorder equipment images
     */
    public function reorderImages(Request $request, $equipmentId): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:equipment_images,image_id'
        ]);

        $equipment = Equipment::findOrFail($equipmentId);

        foreach ($request->input('order') as $position => $imageId) {
            EquipmentImage::where('image_id', $imageId)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['message' => 'Images reordered successfully']);
    }

    private function formatEquipment($equipment): array
    {
        $equipment->load(['category', 'status', 'department', 'type', 'items', 'images']);

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
            'available_quantity' => $equipment->available_quantity,
            'rental_fee' => $equipment->rental_fee,
            'company_fee' => $equipment->company_fee,
            'rate_type' => [
                'type_id' => $equipment->type_id,
                'type_name' => optional($equipment->type)->type_name,
            ],
            'status' => [
                'status_id' => $equipment->status_id,
                'status_name' => $equipment->status->status_name,
                'color_code' => $equipment->status->color_code,
            ],
            'department' => [
                'department_id' => $equipment->department_id,
                'department_name' => $equipment->department->department_name,
            ],
            'minimum_hour' => $equipment->minimum_hour,
            'items' => $equipment->items,
            'images' => $equipment->images,
            'created_at' => $equipment->created_at,
            'updated_at' => $equipment->updated_at,
        ];
    }
}