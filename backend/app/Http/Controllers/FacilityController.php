<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities.
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Allow head admins to see all facilities
            if ($user->role_id === \App\AdminRoles::HEAD_ADMIN) {
                $facilities = Facility::with(['category', 'subcategory', 'status', 'department', 'room', 'images'])
                    ->orderBy('facility_name')
                    ->get();
            } else {
                $facilities = Facility::whereIn('department_id', $user->departments->pluck('department_id'))
                    ->with(['category', 'subcategory', 'status', 'department', 'room', 'images'])
                    ->orderBy('facility_name')
                    ->get();
            }

            $formatted = $facilities->map(fn ($item) => $this->formatFacility($item));

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            \Log::error('Error fetching facilities data', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch facilities data', 'error' => $e->getMessage()], 500);
        }
    }
    public function publicIndex(): JsonResponse
{
    try {
        $facilities = Facility::with(['category', 'subcategory', 'status', 'department', 'room', 'images'])
            ->orderBy('facility_name')
            ->get();

        $formatted = $facilities->map(fn ($item) => $this->formatFacility($item));

        return response()->json(['data' => $formatted]);
    } catch (\Exception $e) {
        \Log::error('Error fetching public facilities', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to fetch facilities data', 'error' => $e->getMessage()], 500);
    }
}


    /**
     * Store newly created facility in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'facility_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'location_note' => 'required|string|max:200',
            'capacity' => 'required|integer|min:1',
            'category_id' => 'required|exists:facility_categories,category_id',
            'subcategory_id' => 'nullable|exists:facility_subcategories,subcategory_id',
            'room_id' => 'nullable|exists:room_details,room_id',
            'department_id' => 'required|exists:departments,department_id',
            'is_indoors' => 'required|in:Indoors,Outdoors',
            'rental_fee' => 'required|numeric|min:0',
            'company_fee' => 'required|numeric|min:0',
            'status_id' => 'required|exists:availability_statuses,status_id',
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
            'room_id' => $data['room_id'] ?? null,
            'department_id' => $data['department_id'],
            'is_indoors' => $data['is_indoors'],
            'rental_fee' => $data['rental_fee'],
            'company_fee' => $data['company_fee'],
            'status_id' => $data['status_id'],
            'created_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Facility created successfully',
            'data' => $this->formatFacility($facility),
        ], 201);
    }

    /**
     * Display the specified facility.
     */
    public function show(Facility $facility): JsonResponse
    {
        return response()->json([
            'data' => $this->formatFacility($facility),
        ]);
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, Facility $facility): JsonResponse
    {
        $data = $request->validate([
            'facility_name' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:250',
            'location_note' => 'sometimes|string|max:200',
            'capacity' => 'sometimes|integer|min:1',
            'category_id' => 'sometimes|exists:facility_categories,category_id',
            'subcategory_id' => 'nullable|exists:facility_subcategories,subcategory_id',
            'room_id' => 'nullable|exists:room_details,room_id',
            'department_id' => 'sometimes|exists:departments,department_id',
            'is_indoors' => 'sometimes|in:Indoors,Outdoors',
            'rental_fee' => 'sometimes|numeric|min:0',
            'company_fee' => 'sometimes|numeric|min:0',
            'status_id' => 'sometimes|exists:availability_statuses,status_id',
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
            'room_id' => $data['room_id'] ?? $facility->room_id,
            'department_id' => $data['department_id'] ?? $facility->department_id,
            'is_indoors' => $data['is_indoors'] ?? $facility->is_indoors,
            'rental_fee' => $data['rental_fee'] ?? $facility->rental_fee,
            'company_fee' => $data['company_fee'] ?? $facility->company_fee,
            'status_id' => $data['status_id'] ?? $facility->status_id,
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Facility updated successfully',
            'data' => $this->formatFacility($facility),
        ]);
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Facility $facility): JsonResponse
    {
        $user = auth()->user();

        if (!$user->departments->pluck('id')->contains($facility->department_id)) {
            return response()->json(['message' => 'You do not manage this facility.'], 403);
        }

        $facility->images()->delete();
        $facility->delete();

        return response()->json(['message' => 'Facility deleted successfully']);
    }

    /**
     * Upload an image for the facility
     */
    public function uploadImage(Request $request, $facilityId): JsonResponse
    {
        $uploadResponse = Cloudinary::upload($image->getRealPath(), [
            'upload_preset' => 'facility-photos'
        ]);
        
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        $facility = Facility::findOrFail($facilityId);

        $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
        $imageUrl = $uploadedFileUrl;

        $imageType = $request->input('type_id') ??
            ($facility->images()->count() == 0 ? 1 : 2);

        $facility->images()->create([
            'image_url' => $imageUrl,
            'type_id' => $imageType,
            'cloudinary_public_id' => $publicId,
            'description' => $request->input('description'),
            'sort_order' => $facility->images()->count() + 1
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'type' => $imageType == 1 ? 'primary' : 'additional'
        ]);
    }

    /**
     * Bulk upload images for the facility
     */
    public function uploadMultipleImages(Request $request, $facilityId): JsonResponse
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        $facility = Facility::findOrFail($facilityId);
        $currentImageCount = $facility->images()->count();
        $typeId = $request->input('type_id');

        foreach ($request->file('images') as $index => $image) {
            $uploadedFileUrl = Cloudinary::upload($image->getRealPath())->getSecurePath();
            $imageUrl = $uploadedFileUrl;

            $imageType = $typeId ?? (($currentImageCount + $index) == 0 ? 1 : 2);

            $facility->images()->create([
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
     * Delete a facility image
     */
    public function deleteImage($facilityId, $imageId): JsonResponse
    {
        $facility = Facility::findOrFail($facilityId);
        $image = $facility->images()->findOrFail($imageId);

        foreach ($facility->images as $image) {
            if ($image->cloudinary_public_id) {
                Cloudinary::destroy($image->cloudinary_public_id);
            }
        }
        $facility->images()->delete();
        $facility->delete();

        $path = str_replace('/storage', 'public', parse_url($image->image_url, PHP_URL_PATH));
        Storage::delete($path);

        $image->delete();

        if ($image->cloudinary_public_id) {
            Cloudinary::destroy($image->cloudinary_public_id);
        }  

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

    /**
     * Reorder facility images
     */
    public function reorderImages(Request $request, $facilityId): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:facility_images,image_id'
        ]);

        $facility = Facility::findOrFail($facilityId);

        foreach ($request->input('order') as $position => $imageId) {
            FacilityImage::where('image_id', $imageId)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['message' => 'Images reordered successfully']);
    }

    private function formatFacility($facility): array
    {
        $facility->load(['category', 'subcategory', 'status', 'department', 'room', 'images']);

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
            'room' => $facility->room ? [
                'room_id' => $facility->room_id,
                'room_name' => $facility->room->room_name,
            ] : null,
            'is_indoors' => $facility->is_indoors,
            'rental_fee' => $facility->rental_fee,
            'company_fee' => $facility->company_fee,
            'status' => [
                'status_id' => $facility->status_id,
                'status_name' => $facility->status->status_name,
                'color_code' => $facility->status->color_code,
            ],
            'department' => [
                'department_id' => $facility->department_id,
                'department_name' => $facility->department->department_name,
            ],
            'images' => $facility->images,
            'created_at' => $facility->created_at,
            'updated_at' => $facility->updated_at,
        ];
    }
}