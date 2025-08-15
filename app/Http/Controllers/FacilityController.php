<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class FacilityController extends Controller
{
    // ----- Indexes ----- //

    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Head admins can view all facilities
            if ($user->role?->role_title === 'Head Admin') {
                $facilities = Facility::with(['category', 'subcategory', 'status', 'department', 'images'])
                    ->orderBy('facility_name')
                    ->get();
            } else {
                $facilities = Facility::whereIn('department_id', $user->departments->pluck('department_id'))
                    ->with(['category', 'subcategory', 'status', 'department', 'images'])
                    ->orderBy('facility_name')
                    ->get();
            }

            $formatted = $facilities->map(fn ($item) => $this->formatFacility($item));

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            \Log::error('Error fetching facilities data', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to fetch facilities data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function publicIndex(): JsonResponse
    {
        try {
            $facilities = Facility::with(['category', 'subcategory', 'status', 'department', 'images'])
                ->orderBy('facility_name')
                ->get();

            $formatted = $facilities->map(fn ($item) => $this->formatPublicFacility($item));

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            \Log::error('Error fetching public facilities', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to fetch facilities data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ----- Store Facility ----- //

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'facility_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'location_note' => 'required|string|max:200',
            'capacity' => 'required|integer|min:1',
            'category_id' => 'required|exists:facility_categories,category_id',
            'subcategory_id' => 'nullable|exists:facility_subcategories,subcategory_id',
            'department_id' => 'required|exists:departments,department_id',
            'location_type' => 'required|in:Indoors,Outdoors',
            'internal_fee' => 'required|numeric|min:0',
            'external_fee' => 'required|numeric|min:0',
            'rate_type' => 'required|in:Per Hour,Per Event',
            'status_id' => 'required|exists:availability_statuses,status_id',
            'maximum_rental_hour' => 'nullable|integer',
            'parent_facility_id' => 'nullable|exists:facilities,facility_id',
            'room_code' => 'nullable|string|max:50',
            'floor_level' => 'nullable|integer|min:1',
            'building_code' => 'nullable|string|max:20',
            'total_levels' => 'nullable|integer|min:1',
            'total_rooms' => 'nullable|integer|min:1',
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

        $facility = Facility::create([
            'facility_name' => $data['facility_name'],
            'description' => $data['description'] ?? null,
            'location_note' => $data['location_note'],
            'capacity' => $data['capacity'],
            'category_id' => $data['category_id'],
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'department_id' => $data['department_id'],
            'location_type' => $data['location_type'],
            'internal_fee' => $data['internal_fee'],
            'external_fee' => $data['external_fee'],
            'rate_type' => $data['rate_type'],
            'status_id' => $data['status_id'],
            'maximum_rental_hour' => $data['maximum_rental_hour'],
            'parent_facility_id' => $data['parent_facility_id'] ?? null,
            'room_code' => $data['room_code'] ?? null,
            'floor_level' => $data['floor_level'] ?? null,
            'building_code' => $data['building_code'] ?? null,
            'total_levels' => $data['total_levels'] ?? null,
            'total_rooms' => $data['total_rooms'] ?? null,
            'created_by' => $user->admin_id
        ]);

        // Optional: Handle images if provided
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $facility->images()->create($image);
            }
        }

        return response()->json([
            'message' => 'Facility created successfully',
            'data' => $this->formatFacility($facility->fresh())
        ], 201);
    }

    // ----- Display Facility ----- //

    public function show(Facility $facility): JsonResponse
    {
        $facility->load(['category', 'subcategory', 'status', 'department', 'images']);

        return response()->json([
            'data' => $this->formatFacility($facility),
        ]);
    }

    // ----- Update Facility ----- //

    public function update(Request $request, Facility $facility): JsonResponse
    {
        $data = $request->validate([
            'facility_name' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:250',
            'location_note' => 'sometimes|string|max:200',
            'capacity' => 'sometimes|integer|min:1',
            'category_id' => 'sometimes|exists:facility_categories,category_id',
            'subcategory_id' => 'nullable|exists:facility_subcategories,subcategory_id',
            'department_id' => 'sometimes|exists:departments,department_id',
            'location_type' => 'sometimes|in:Indoors,Outdoors',
            'internal_fee' => 'sometimes|numeric|min:0',
            'external_fee' => 'sometimes|numeric|min:0',
            'rate_type' => 'sometimes|in:Per Hour,Per Event',
            'status_id' => 'sometimes|exists:availability_statuses,status_id',
            'maximum_rental_hour' => 'nullable|integer',
            'parent_facility_id' => 'nullable|exists:facilities,facility_id',
            'room_code' => 'nullable|string|max:50',
            'floor_level' => 'nullable|integer|min:1',
            'building_code' => 'nullable|string|max:20',
            'total_levels' => 'nullable|integer|min:1',
            'total_rooms' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        $facility->update([
            'facility_name' => $data['facility_name'] ?? $facility->facility_name,
            'description' => $data['description'] ?? $facility->description,
            'location_note' => $data['location_note'] ?? $facility->location_note,
            'capacity' => $data['capacity'] ?? $facility->capacity,
            'category_id' => $data['category_id'] ?? $facility->category_id,
            'subcategory_id' => $data['subcategory_id'] ?? $facility->subcategory_id,
            'department_id' => $data['department_id'] ?? $facility->department_id,
            'location_type' => $data['location_type'] ?? $facility->location_type,
            'internal_fee' => $data['internal_fee'] ?? $facility->internal_fee,
            'external_fee' => $data['external_fee'] ?? $facility->external_fee,
            'rate_type' => $data['rate_type'] ?? $facility->rate_type,
            'status_id' => $data['status_id'] ?? $facility->status_id,
            'maximum_rental_hour' => $data['maximum_rental_hour'] ?? $facility->maximum_rental_hour,
            'parent_facility_id' => $data['parent_facility_id'] ?? $facility->parent_facility_id,
            'room_code' => $data['room_code'] ?? $facility->room_code,
            'floor_level' => $data['floor_level'] ?? $facility->floor_level,
            'building_code' => $data['building_code'] ?? $facility->building_code,
            'total_levels' => $data['total_levels'] ?? $facility->total_levels,
            'total_rooms' => $data['total_rooms'] ?? $facility->total_rooms,
            'updated_by' => $user->admin_id,
        ]);

        return response()->json([
            'message' => 'Facility updated successfully',
            'data' => $this->formatFacility($facility->fresh()),
        ]);
    }

    // ----- Remove Facility ----- //

    public function destroy(Facility $facility): JsonResponse
    {
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        // Soft delete tracking
        $facility->update([
            'deleted_by' => $user->admin_id,
        ]);

        // Remove related records
        $facility->images()->delete();

        // Delete facility record
        $facility->delete();

        return response()->json(['message' => 'Facility deleted successfully']);
    }

    // ----- Upload Facility Images ----- //

    public function uploadImage(Request $request, $facilityId): JsonResponse
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        $facility = Facility::findOrFail($facilityId);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        $uploaded = Cloudinary::upload(
            $request->file('image')->getRealPath(),
            ['upload_preset' => 'facility-photos']
        );

        $imageUrl = $uploaded->getSecurePath();
        $publicId = $uploaded->getPublicId();

        $imageType = $validated['type_id'] ??
            ($facility->images()->count() == 0 ? 1 : 2);

        $facility->images()->create([
            'image_url' => $imageUrl,
            'type_id' => $imageType,
            'cloudinary_public_id' => $publicId,
            'description' => $validated['description'] ?? null,
            'sort_order' => $facility->images()->count() + 1
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'type' => $imageType == 1 ? 'primary' : 'additional'
        ]);
    }

    // ----- Upload Images In Bulk ----- //

    public function uploadMultipleImages(Request $request, $facilityId): JsonResponse
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id',
        ]);

        $facility = Facility::findOrFail($facilityId);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        $currentImageCount = $facility->images()->count();
        $typeId = $validated['type_id'] ?? null;

        foreach ($validated['images'] as $index => $image) {
            $upload = Cloudinary::upload(
                $image->getRealPath(),
                ['upload_preset' => 'facility-photos']
            );

            $imageUrl = $upload->getSecurePath();
            $publicId = $upload->getPublicId();

            $imageType = $typeId ?? (($currentImageCount + $index) === 0 ? 1 : 2);

            $facility->images()->create([
                'image_url' => $imageUrl,
                'type_id' => $imageType,
                'cloudinary_public_id' => $publicId,
                'description' => $validated['description'] ?? null,
                'sort_order' => $currentImageCount + $index + 1,
            ]);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }

    // ----- Delete Facility Images ----- //

    public function deleteImage($facilityId, $imageId): JsonResponse
    {
        $facility = Facility::findOrFail($facilityId);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        $image = $facility->images()->findOrFail($imageId);

        if ($image->cloudinary_public_id) {
            Cloudinary::destroy($image->cloudinary_public_id);
        }

        $path = str_replace('/storage', 'public', parse_url($image->image_url, PHP_URL_PATH));
        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        $image->delete();
        $this->reorderImageRecords($facility);

        return response()->json(['message' => 'Image deleted successfully']);
    }

    private function reorderImageRecords(Facility $facility): void
    {
        $images = $facility->images()->orderBy('sort_order')->get();
        foreach ($images as $index => $image) {
            $image->update(['sort_order' => $index + 1]);
        }
    }

    // ----- Reorder Images ----- //

    public function reorderImages(Request $request, $facilityId): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:facility_images,image_id'
        ]);

        $facility = Facility::findOrFail($facilityId);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        foreach ($request->input('order') as $position => $imageId) {
            $facility->images()->where('image_id', $imageId)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['message' => 'Images reordered successfully']);
    }

    // ----- Formatting ----- //

    private function formatFacility($facility): array
    {
        $facility->load(['category', 'subcategory', 'status', 'department', 'images']);

        return [
            'facility_id' => $facility->facility_id,
            'facility_name' => $facility->facility_name,
            'description' => $facility->description,
            'location_note' => $facility->location_note,
            'capacity' => $facility->capacity,
            'category' => [
                'category_id' => $facility->category_id,
                'category_name' => $facility->category->category_name,
            ],
            'subcategory' => $facility->subcategory ? [
                'subcategory_id' => $facility->subcategory_id,
                'subcategory_name' => $facility->subcategory->subcategory_name,
            ] : null,
            'department' => [
                'department_id' => $facility->department_id,
                'department_name' => $facility->department->department_name,
            ],
            'location_type' => $facility->location_type,
            'internal_fee' => $facility->internal_fee,
            'external_fee' => $facility->external_fee,
            'rate_type' => $facility->rate_type,
            'status' => [
                'status_id' => $facility->status_id,
                'status_name' => $facility->status->status_name,
                'color_code' => $facility->status->color_code,
            ],
            'maximum_rental_hour' => $facility->maximum_rental_hour,
            'parent_facility_id' => $facility->parent_facility_id,
            'room_code' => $facility->room_code,
            'floor_level' => $facility->floor_level,
            'building_code' => $facility->building_code,
            'total_levels' => $facility->total_levels,
            'total_rooms' => $facility->total_rooms,
            'images' => $facility->images,
            'created_at' => $facility->created_at,
            'updated_at' => $facility->updated_at,
        ];
    }

    private function formatPublicFacility($facility): array
    {
        $facility->load(['category', 'subcategory', 'status', 'department', 'images']);

        return [
            'facility_id' => $facility->facility_id,
            'facility_name' => $facility->facility_name,
            'description' => $facility->description,
            'location_note' => $facility->location_note,
            'capacity' => $facility->capacity,
            'category' => [
                'category_id' => $facility->category_id,
                'category_name' => $facility->category->category_name,
            ],
            'subcategory' => $facility->subcategory ? [
                'subcategory_id' => $facility->subcategory_id,
                'subcategory_name' => $facility->subcategory->subcategory_name,
            ] : null,
            'department' => [
                'department_id' => $facility->department_id,
                'department_name' => $facility->department->department_name,
            ],
            'location_type' => $facility->location_type,
            'internal_fee' => $facility->internal_fee,
            'external_fee' => $facility->external_fee,
            'rate_type' => $facility->rate_type,
            'status' => [
                'status_id' => $facility->status_id,
                'status_name' => $facility->status->status_name,
                'color_code' => $facility->status->color_code,
            ],
            'images' => $facility->images,
        ];
    }
}