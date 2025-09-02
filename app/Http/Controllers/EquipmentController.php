<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentItem;
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
            'images.*.image_type' => 'required|exists:image_types,image_type',
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

    public function show($id)
    {
        try {
            $equipment = Equipment::with([
                'category',
                'status',
                'department',
                'images'
            ])->findOrFail($id);

            return response()->json([
                'data' => $equipment
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching equipment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch equipment',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // ----- Update Equipment ----- //

    public function update(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'storage_location' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,category_id',
            'total_quantity' => 'required|integer|min:0',
            'internal_fee' => 'required|numeric|min:0',
            'external_fee' => 'required|numeric|min:0',
            'rate_type' => 'required|in:Per Hour,Per Event',
            'status_id' => 'required|exists:availability_statuses,status_id',
            'department_id' => 'required|exists:departments,department_id',
            'maximum_rental_hour' => 'required|integer|min:1',
        ]);

        $equipment = Equipment::findOrFail($id);
        
        // Check if user has permission to update this equipment
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json([
                'message' => 'You do not manage this equipment.'
            ], 403);
        }

        $equipment->update($validated);

        return response()->json([
            'message' => 'Equipment updated successfully',
            'data' => $equipment
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating equipment: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to update equipment',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function edit(Request $request)
    {
        $equipmentId = $request->query('id');

        if (!$equipmentId) {
            return redirect('/admin/manage-equipment')->with('error', 'No equipment ID provided');
        }

        return view('admin.edit-equipment', ['equipmentId' => $equipmentId]);
    }

    public function saveImageReference(Request $request, $equipmentId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'image_url' => 'required|url',
                'cloudinary_public_id' => 'required|string',
                'description' => 'nullable|string|max:255',
                'image_type' => 'sometimes|in:Primary,Secondary'
            ]);

            $equipment = Equipment::findOrFail($equipmentId);


            // Determine image type
            $imageType = $validated['image_type'] ?? ($equipment->images()->count() == 0 ? 'Primary' : 'Secondary');

            // Create the image record
            $equipment->images()->create([
                'image_url' => $validated['image_url'],
                'image_type' => $imageType,
                'cloudinary_public_id' => $validated['cloudinary_public_id'],
                'description' => $validated['description'] ?? null,
                'sort_order' => $equipment->images()->count() + 1
            ]);

            return response()->json([
                'message' => 'Image reference saved successfully',
                'type' => $imageType
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving image reference', [
                'error' => $e->getMessage(),
                'equipmentId' => $equipmentId
            ]);

            return response()->json([
                'message' => 'Failed to save image reference: ' . $e->getMessage()
            ], 500);
        }
    }

    // ----- Upload Equipment Images ----- //

    public function uploadImage(Request $request, $equipmentId): JsonResponse
    {
        // Validate the uploaded image and data
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'image_type' => 'sometimes|in:Primary,Secondary' // Change validation to match enum values
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

        // Determine image type if not provided - use enum string values
        $imageType = $validated['image_type'] ??
            ($equipment->images()->count() == 0 ? 'Primary' : 'Secondary'); // Use string values

        // Create the image record
        $equipment->images()->create([
            'image_url' => $imageUrl,
            'image_type' => $imageType, // Change to match database column name
            'cloudinary_public_id' => $publicId,
            'description' => $validated['description'] ?? null,
            'sort_order' => $equipment->images()->count() + 1
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'type' => $imageType,
            'image_url' => $imageUrl,
            'public_id' => $publicId
        ]);
    }

    // ----- Upload Images In Bulk ----- //
    public function uploadMultipleImages(Request $request, $equipmentId): JsonResponse
    {
        // Validate the images and optional image_type
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jjpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'image_type' => 'sometimes|in:Primary,Secondary', // Change validation
        ]);

        // Find the equipment
        $equipment = Equipment::findOrFail($equipmentId);

        // Authorization: ensure the admin manages this equipment's department
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $currentImageCount = $equipment->images()->count();
        $typeId = $validated['image_type'] ?? null;

        foreach ($validated['images'] as $index => $image) {
            // Upload to Cloudinary
            $upload = Cloudinary::upload(
                $image->getRealPath(),
                ['upload_preset' => 'equipment-photos']
            );

            $imageUrl = $upload->getSecurePath();
            $publicId = $upload->getPublicId();

            // Decide image type (first image = primary if none specified)
            $imageType = $typeId ?? (($currentImageCount + $index) === 0 ? 'Primary' : 'Secondary');

            // Create image record
            $equipment->images()->create([
                'image_url' => $imageUrl,
                'image_type' => $imageType, // Change to match database column name
                'cloudinary_public_id' => $publicId,
                'description' => $validated['description'] ?? null,
                'sort_order' => $currentImageCount + $index + 1,
            ]);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }


    // ----- Delete Equipment Images ----- //

    public function deleteCloudinaryImage(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'public_id' => 'required|string'
            ]);

            // Simple Cloudinary delete - no equipment/auth checks needed
            $result = Cloudinary::destroy($validated['public_id']);

            return response()->json([
                'message' => 'Image deleted from Cloudinary',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('Cloudinary delete error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete image'], 500);
        }
    }

   public function deleteImage($equipmentId, $imageId): JsonResponse
{
    \Log::info('Attempting to delete image', [
        'equipmentId' => $equipmentId,
        'imageId' => $imageId
    ]);

    try {
        // Find the equipment
        $equipment = Equipment::findOrFail($equipmentId);
        \Log::info('Equipment found', ['equipment_id' => $equipment->equipment_id]);

        // Check authorization
        $user = auth()->user();
        \Log::info('User info', [
            'user_id' => $user->id,
            'user_departments' => $user->departments->pluck('department_id'),
            'equipment_department' => $equipment->department_id
        ]);
        
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            \Log::warning('Unauthorized delete attempt', [
                'user_departments' => $user->departments->pluck('department_id'),
                'equipment_department' => $equipment->department_id
            ]);
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        // Find the image
        $image = $equipment->images()->findOrFail($imageId);
        \Log::info('Image found', [
            'image_id' => $image->image_id,
            'cloudinary_public_id' => $image->cloudinary_public_id,
            'image_url' => $image->image_url
        ]);

        // Store public ID before deletion
        $publicId = $image->cloudinary_public_id;

        // Delete DB record first
        \Log::info('Deleting database record');
        $image->delete();
        \Log::info('Database record deleted');

        // Delete from Cloudinary if public ID exists (do this after DB deletion)
        if ($publicId && $publicId !== 'oxvsxogzu9koqhctnf7s') { // Skip default placeholder
            \Log::info('Attempting Cloudinary delete', [
                'public_id' => $publicId
            ]);
            
            try {
                Cloudinary::destroy($publicId);
                \Log::info('Cloudinary delete successful');
            } catch (\Exception $cloudinaryError) {
                \Log::error('Cloudinary delete failed but continuing', [
                    'error' => $cloudinaryError->getMessage(),
                    'public_id' => $publicId
                ]);
                // Continue even if Cloudinary delete fails
            }
        }

        // Reorder remaining images
        $this->reorderImageRecords($equipment);
        \Log::info('Images reordered');

        return response()->json(['message' => 'Image deleted successfully']);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error('Model not found in deleteImage', [
            'error' => $e->getMessage(),
            'equipmentId' => $equipmentId,
            'imageId' => $imageId
        ]);
        return response()->json(['message' => 'Image or equipment not found'], 404);

    } catch (\Exception $e) {
        \Log::error('Error in deleteImage', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'equipmentId' => $equipmentId,
            'imageId' => $imageId
        ]);
        return response()->json([
            'message' => 'Failed to delete image: ' . $e->getMessage()
        ], 500);
    }
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

    


    // ---------  EQUIPMENT ITEMS MANAGEMENT ---------- //

public function getItems($equipmentId): JsonResponse
{
    try {
        $equipment = Equipment::with(['items.condition'])->findOrFail($equipmentId);
        
        return response()->json([
            'data' => $equipment->items
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching equipment items', [
            'error' => $e->getMessage(),
            'equipmentId' => $equipmentId
        ]);
        
        return response()->json([
            'message' => 'Failed to fetch equipment items',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function storeItem(Request $request, $equipmentId): JsonResponse
{
    try {
        $validated = $request->validate([
            'item_name' => 'required|string|max:50',
            'condition_id' => 'required|exists:conditions,condition_id',
            'barcode_number' => 'nullable|string|max:20',
            'item_notes' => 'nullable|string|max:100',
            'image_url' => 'nullable|url',
            'cloudinary_public_id' => 'nullable|string'
        ]);

        $equipment = Equipment::findOrFail($equipmentId);
        
        // Authorization check
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $item = $equipment->items()->create([
            'item_name' => $validated['item_name'],
            'condition_id' => $validated['condition_id'],
            'barcode_number' => $validated['barcode_number'] ?? null,
            'item_notes' => $validated['item_notes'] ?? 'No notes provided for this asset.',
            'image_url' => $validated['image_url'] ?? 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp',
            'cloudinary_public_id' => $validated['cloudinary_public_id'] ?? 'oxvsxogzu9koqhctnf7s',
            'status_id' => 1, // Default to available
            'created_by' => $user->admin_id
        ]);

        return response()->json([
            'message' => 'Item added successfully',
            'data' => $item->load('condition')
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error adding equipment item', [
            'error' => $e->getMessage(),
            'equipmentId' => $equipmentId
        ]);
        
        return response()->json([
            'message' => 'Failed to add item',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function updateItem(Request $request, $equipmentId, $itemId): JsonResponse
{
    try {
        $validated = $request->validate([
            'item_name' => 'required|string|max:50',
            'condition_id' => 'required|exists:conditions,condition_id',
            'barcode_number' => 'nullable|string|max:20',
            'item_notes' => 'nullable|string|max:100',
            'image_url' => 'nullable|url',
            'cloudinary_public_id' => 'nullable|string'
        ]);

        $equipment = Equipment::findOrFail($equipmentId);
        
        // Authorization check
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $item = EquipmentItem::where('equipment_id', $equipmentId)
            ->where('item_id', $itemId)
            ->firstOrFail();

        $item->update([
            'item_name' => $validated['item_name'],
            'condition_id' => $validated['condition_id'],
            'barcode_number' => $validated['barcode_number'] ?? null,
            'item_notes' => $validated['item_notes'] ?? $item->item_notes,
            'image_url' => $validated['image_url'] ?? $item->image_url,
            'cloudinary_public_id' => $validated['cloudinary_public_id'] ?? $item->cloudinary_public_id,
            'updated_by' => $user->admin_id
        ]);

        return response()->json([
            'message' => 'Item updated successfully',
            'data' => $item->fresh()->load('condition')
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating equipment item', [
            'error' => $e->getMessage(),
            'equipmentId' => $equipmentId,
            'itemId' => $itemId
        ]);
        
        return response()->json([
            'message' => 'Failed to update item',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function deleteItem($equipmentId, $itemId): JsonResponse
{
    try {
        $equipment = Equipment::findOrFail($equipmentId);
        
        // Authorization check
        $user = auth()->user();
        if (!$user->departments->pluck('department_id')->contains($equipment->department_id)) {
            return response()->json(['message' => 'You do not manage this equipment.'], 403);
        }

        $item = EquipmentItem::where('equipment_id', $equipmentId)
            ->where('item_id', $itemId)
            ->firstOrFail();

        // Soft delete tracking
        $item->update([
            'deleted_by' => $user->admin_id,
        ]);

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);

    } catch (\Exception $e) {
        \Log::error('Error deleting equipment item', [
            'error' => $e->getMessage(),
            'equipmentId' => $equipmentId,
            'itemId' => $itemId
        ]);
        
        return response()->json([
            'message' => 'Failed to delete item',
            'error' => $e->getMessage()
        ], 500);
    }
}
    


}