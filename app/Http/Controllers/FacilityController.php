<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\LookupTables\FacilityCategory;
use App\Models\Department;
use App\Models\LookupTables\AvailabilityStatus;
use App\Models\LookupTables\FacilitySubcategory;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;



class FacilityController extends Controller
{
    // ----- Index - Show all facilities ----- //
// ----- Index - Show all facilities ----- //
    public function publicIndex(): JsonResponse
    {
        try {
            $facilities = Facility::with([
                'category',
                'subcategory',
                'status',
                'department',
                'images'
            ])->orderBy('facility_name')->get();

            $formatted = $facilities->map(function ($facility) {
                return $this->formatPublicFacility($facility);
            });

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            \Log::error('Error fetching public facilities', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to fetch facilities data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ----- Formatting ----- //
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
            'maximum_rental_hour' => $facility->maximum_rental_hour,
            'parent_facility_id' => $facility->parent_facility_id,
            'room_code' => $facility->room_code,
            'floor_level' => $facility->floor_level,
            'building_code' => $facility->building_code,
            'total_levels' => $facility->total_levels,
            'total_rooms' => $facility->total_rooms,
            'images' => $facility->images,
        ];
    }

    // ----- Create - Show add facility form ----- //
    public function create()
    {
        $categories = FacilityCategory::all();
        $subcategories = FacilitySubcategory::all();
        $departments = Department::all();
        $statuses = AvailabilityStatus::all();

        return view('admin.add-facility', compact('categories', 'subcategories', 'departments', 'statuses'));
    }

    // ----- Store - Save new facility ----- //
    public function store(Request $request)
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
        ]);

        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($data['department_id'])) {
            return redirect()->back()->with('error', 'You do not manage this department.');
        }

        Facility::create([
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

        return redirect()->route('admin.manage-facilities')
            ->with('success', 'Facility created successfully!');
    }


    // ----- Edit - Show edit form ----- //
    public function edit(Request $request)
    {
        $facilityId = $request->query('id');

        if (!$facilityId) {
            return redirect('/admin/manage-facilities')->with('error', 'No facility ID provided');
        }

        return view('admin.edit-facility', ['facilityId' => $facilityId]);
    }

    // ----- Update - Save facility changes ----- //
    public function update(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

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
        ]);

        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return redirect()->back()->with('error', 'You do not manage this facility.');
        }

        $facility->update([
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
            'updated_by' => $user->admin_id,
        ]);

        return redirect()->route('admin.manage-facilities')
            ->with('success', 'Facility updated successfully!');
    }

    // ----- Destroy - Delete facility ----- //
    public function destroy($id)
    {
        $facility = Facility::findOrFail($id);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return redirect()->back()->with('error', 'You do not manage this facility.');
        }

        // Soft delete tracking
        $facility->update([
            'deleted_by' => $user->admin_id,
        ]);

        // Remove related records
        $facility->images()->delete();

        // Delete facility record
        $facility->delete();

        return redirect()->route('admin.manage-facilities')
            ->with('success', 'Facility deleted successfully!');
    }

    // ----- Show - View single facility (optional) ----- //
    public function show($id)
    {
        try {
            $facility = Facility::with([
                'category',
                'subcategory',
                'status',
                'department',
                'images'
            ])->findOrFail($id);

            return response()->json([
                'data' => $facility
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching facility: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ----- Upload Facility Image ----- //
    public function uploadImage(Request $request, $facilityId)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:255',
            'type_id' => 'sometimes|exists:image_types,type_id'
        ]);

        $facility = Facility::findOrFail($facilityId);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return redirect()->back()->with('error', 'You do not manage this facility.');
        }

        $uploaded = Cloudinary::upload(
            $request->file('image')->getRealPath(),
            ['upload_preset' => 'facility-photos']
        );

        $imageUrl = $uploaded->getSecurePath();
        $publicId = $uploaded->getPublicId();

        $imageType = $validated['type_id'] ?? ($facility->images()->count() == 0 ? 1 : 2);

        $facility->images()->create([
            'image_url' => $imageUrl,
            'type_id' => $imageType,
            'cloudinary_public_id' => $publicId,
            'description' => $validated['description'] ?? null,
            'sort_order' => $facility->images()->count() + 1
        ]);

        return redirect()->back()->with('success', 'Image uploaded successfully!');
    }

    // ----- Delete Facility Image ----- //
    public function deleteImage($facilityId, $imageId)
    {
        $facility = Facility::findOrFail($facilityId);
        $user = auth()->user();

        if (!$user->departments->pluck('department_id')->contains($facility->department_id)) {
            return redirect()->back()->with('error', 'You do not manage this facility.');
        }

        $image = $facility->images()->findOrFail($imageId);

        if ($image->cloudinary_public_id) {
            Cloudinary::destroy($image->cloudinary_public_id);
        }

        $image->delete();
        $this->reorderImageRecords($facility);

        return redirect()->back()->with('success', 'Image deleted successfully!');
    }

    private function reorderImageRecords(Facility $facility): void
    {
        $images = $facility->images()->orderBy('sort_order')->get();
        foreach ($images as $index => $image) {
            $image->update(['sort_order' => $index + 1]);
        }
    }
}