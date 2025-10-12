<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Department;
use App\Models\LookupTables\AvailabilityStatus;
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
            'external_fee' => $facility->external_fee,
            'rate_type' => $facility->rate_type,
            'status' => [
                'status_id' => $facility->status_id,
                'status_name' => $facility->status->status_name,
                'color_code' => $facility->status->color_code,
            ],
            'parent_facility_id' => $facility->parent_facility_id,
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
    // Just get departments and statuses first
    $departments = Department::all();
    $statuses = AvailabilityStatus::all();
    
    // Return with empty categories for now
    $categories = [];
    $subcategories = [];
    
    return view('admin.add-facility', compact('categories', 'subcategories', 'departments', 'statuses'));
}

    // ----- Store - Save new facility ----- //
public function store(Request $request)
{
    try {
        $data = $request->validate([
            'facility_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'location_note' => 'nullable|string|max:200',
            'capacity' => 'required|integer|min:1',
            'category_id' => 'required|exists:facility_categories,category_id',
            'subcategory_id' => 'nullable|exists:facility_subcategories,subcategory_id',
            'department_id' => 'required|exists:departments,department_id',
            'location_type' => 'required|in:Indoors,Outdoors',
            'external_fee' => 'required|numeric|min:0',
            'rate_type' => 'required|in:Per Hour,Per Event',
            'status_id' => 'required|exists:availability_statuses,status_id',
            'parent_facility_id' => 'nullable|exists:facilities,facility_id',
            'floor_level' => 'nullable|integer|min:1',
            'building_code' => 'nullable|string|max:20',
            'total_levels' => 'nullable|integer|min:1',
            'total_rooms' => 'nullable|integer|min:1',
            'created_by' => 'required|exists:admins,admin_id'
        ]);

        $user = auth()->user();

        $facility = Facility::create([
            'facility_name' => $data['facility_name'],
            'description' => $data['description'],
            'location_note' => $data['location_note'],
            'capacity' => $data['capacity'],
            'category_id' => $data['category_id'],
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'department_id' => $data['department_id'],
            'location_type' => $data['location_type'],
            'external_fee' => $data['external_fee'],
            'rate_type' => $data['rate_type'],
            'status_id' => $data['status_id'],
            'parent_facility_id' => $data['parent_facility_id'] ?? null,
            'floor_level' => $data['floor_level'] ?? null,
            'building_code' => $data['building_code'] ?? null,
            'total_levels' => $data['total_levels'] ?? null,
            'total_rooms' => $data['total_rooms'] ?? null,
            'created_by' => $user->admin_id
        ]);

        \Log::info('Facility created successfully', [
            'facility_id' => $facility->facility_id,
            'created_by' => $user->admin_id
        ]);

        return response()->json([
            'message' => 'Facility created successfully!',
            'data' => [
                'facility_id' => $facility->facility_id,
                'facility_name' => $facility->facility_name
            ]
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error creating facility', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->check() ? auth()->user()->admin_id : 'unknown',
            'request_data' => $request->except(['password', 'token']) // Exclude sensitive data
        ]);
        
        return response()->json([
            'message' => 'Failed to create facility',
            'error' => $e->getMessage()
        ], 500);
    }
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
        'location_note' => 'nullable|string|max:200',
        'capacity' => 'required|integer|min:1',
        'category_id' => 'required|exists:facility_categories,category_id',
        'subcategory_id' => 'nullable|exists:facility_subcategories,subcategory_id',
        'department_id' => 'required|exists:departments,department_id',
        'location_type' => 'required|in:Indoors,Outdoors',
        'external_fee' => 'required|numeric|min:0',
        'rate_type' => 'required|in:Per Hour,Per Event',
        'status_id' => 'required|exists:availability_statuses,status_id',
        'parent_facility_id' => 'nullable|exists:facilities,facility_id',
        'floor_level' => 'nullable|integer|min:1',
        'building_code' => 'nullable|string|max:20',
        'total_levels' => 'nullable|integer|min:1',
        'total_rooms' => 'nullable|integer|min:1',
    ]);

    $user = auth()->user();

    $facility->update([
        'facility_name' => $data['facility_name'],
        'description' => $data['description'],
        'location_note' => $data['location_note'],
        'capacity' => $data['capacity'],
        'category_id' => $data['category_id'],
        'subcategory_id' => $data['subcategory_id'] ?? null,
        'department_id' => $data['department_id'],
        'location_type' => $data['location_type'],
        'external_fee' => $data['external_fee'],
        'rate_type' => $data['rate_type'],
        'status_id' => $data['status_id'],
        'parent_facility_id' => $data['parent_facility_id'] ?? null,
        'floor_level' => $data['floor_level'] ?? null,
        'building_code' => $data['building_code'] ?? null,
        'total_levels' => $data['total_levels'] ?? null,
        'total_rooms' => $data['total_rooms'] ?? null,
        'updated_by' => $user->admin_id,
    ]);

    // Return JSON response for API calls
    if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json([
            'message' => 'Facility updated successfully!',
            'data' => $facility
        ]);
    }

    // Return redirect for web requests
    return redirect()->route('admin.manage-facilities')
        ->with('success', 'Facility updated successfully!');
}

    // ----- Destroy - Delete facility ----- //

public function destroy($facilityId)
{
    $facility = Facility::with(['images'])->findOrFail($facilityId);
    
    $user = auth()->user();

    // Track who deleted it
    $facility->update([
        'deleted_by' => $user->admin_id,
    ]);

    \Log::info('Starting facility deletion', [
        'facility_id' => $facilityId,
        'image_count' => $facility->images->count(),
        'all_images' => $facility->images->pluck('cloudinary_public_id')
    ]);

    // Delete images from Cloudinary and DB
    foreach ($facility->images as $image) {
        \Log::info('Processing image for deletion', [
            'image_id' => $image->image_id,
            'cloudinary_public_id' => $image->cloudinary_public_id
        ]);

        // Only delete from Cloudinary if it's a real Cloudinary image (not the default placeholder)
        if (!empty($image->cloudinary_public_id) && $image->cloudinary_public_id !== 'oxvsxogzu9koqhctnf7s') {
            try {
                \Log::info('Attempting Cloudinary deletion', [
                    'public_id' => $image->cloudinary_public_id
                ]);
                
                // Use the public ID AS-IS from the database (it already includes the full path)
                $result = Cloudinary::uploadApi()->destroy($image->cloudinary_public_id);
                
                \Log::info('Cloudinary deletion successful', [
                    'public_id' => $image->cloudinary_public_id,
                    'result' => $result['result']
                ]);
                
            } catch (\Exception $e) {
                \Log::error("Failed to delete Cloudinary image", [
                    'public_id' => $image->cloudinary_public_id,
                    'error' => $e->getMessage(),
                    'facility_id' => $facilityId
                ]);
            }
        } else {
            \Log::info('Skipping Cloudinary deletion - default placeholder or empty public_id', [
                'public_id' => $image->cloudinary_public_id
            ]);
        }

        // Delete image record from DB
        $image->delete();
        \Log::info('Database image record deleted', ['image_id' => $image->image_id]);
    }

    // Delete the facility
    $facility->delete();

    \Log::info('Facility deletion completed', ['facility_id' => $facilityId]);

    return response()->json([
        'message' => 'Facility and associated Cloudinary images deleted successfully!',
        'facility_id' => $facilityId
    ], 200);
}

public function testCloudinaryDelete(Request $request)
{
    $publicId = $request->query('public_id');
    
    if (!$publicId) {
        return response()->json([
            'success' => false,
            'error' => 'No public_id provided'
        ], 400);
    }

    try {
        \Log::info('Testing Cloudinary deletion', ['public_id' => $publicId]);
        
        // Use the correct Cloudinary destruction method with error handling
        $result = Cloudinary::uploadApi()->destroy($publicId);
        
        \Log::info('Cloudinary test deletion response', [
            'public_id' => $publicId,
            'result' => $result,
            'result_type' => gettype($result)
        ]);
        
        // Check if result is valid
        if (!$result || !is_array($result)) {
            throw new \Exception('Invalid response from Cloudinary: ' . json_encode($result));
        }
        
        // Check the actual result status
        $success = isset($result['result']) && $result['result'] === 'ok';
        
        return response()->json([
            'success' => $success,
            'result' => $result,
            'public_id_used' => $publicId,
            'message' => $success ? 'Deletion successful' : 'Deletion may have failed'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Cloudinary test deletion failed', [
            'public_id' => $publicId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'public_id_used' => $publicId
        ], 500);
    }
}

public function testCloudinaryConnection()
{
    try {
        // Test basic Cloudinary connectivity
        $ping = Cloudinary::uploadApi()->ping();
        
        \Log::info('Cloudinary ping test', ['result' => $ping]);
        
        return response()->json([
            'success' => true,
            'ping_result' => $ping,
            'message' => 'Cloudinary connection successful'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Cloudinary connection test failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Cloudinary connection failed - check credentials'
        ], 500);
    }
}

public function checkCloudinaryConfig()
{
    try {
        // Check if Cloudinary configuration is loaded
        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');
        
        \Log::info('Cloudinary Configuration Check', [
            'cloud_name' => $cloudName ? 'SET' : 'MISSING',
            'api_key' => $apiKey ? 'SET' : 'MISSING', 
            'api_secret' => $apiSecret ? 'SET' : 'MISSING',
        ]);
        
        return response()->json([
            'cloud_name_set' => !empty($cloudName),
            'api_key_set' => !empty($apiKey),
            'api_secret_set' => !empty($apiSecret),
            'cloudinary_url_set' => !empty(env('CLOUDINARY_URL')),
            'config' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey ? '***' . substr($apiKey, -4) : null,
                'api_secret' => $apiSecret ? '***' . substr($apiSecret, -4) : null,
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
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
public function uploadImage(Request $request, $facilityId): JsonResponse
{
    // Validate the uploaded image and data
    $validated = $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'nullable|string|max:255',
        'type_id' => 'sometimes|exists:image_types,type_id'
    ]);

    // Find the facility
    $facility = Facility::findOrFail($facilityId);


    // Upload to Cloudinary
    $uploaded = Cloudinary::upload(
        $request->file('image')->getRealPath(),
        ['upload_preset' => 'facility-photos']
    );

    $imageUrl = $uploaded->getSecurePath();
    $publicId = $uploaded->getPublicId();

    // Determine image type if not provided
    $imageType = $validated['type_id'] ?? ($facility->images()->count() == 0 ? 1 : 2);

    // Create the image record
    $facility->images()->create([
        'image_url' => $imageUrl,
        'type_id' => $imageType,
        'cloudinary_public_id' => $publicId,
        'description' => $validated['description'],
        'sort_order' => $facility->images()->count() + 1
    ]);

    return response()->json([
        'message' => 'Image uploaded successfully',
        'type_id' => $imageType,
        'image_url' => $imageUrl,
        'public_id' => $publicId
    ]);
}

public function saveImageReference(Request $request, $facilityId): JsonResponse
{
    try {
        $validated = $request->validate([
            'image_url' => 'required|url',
            'cloudinary_public_id' => 'required|string',
            'description' => 'nullable|string|max:255'
        ]);

        $facility = Facility::findOrFail($facilityId);

        // Determine image type (first image = primary, others = secondary)
        $imageType = $facility->images()->count() == 0 ? 'Primary' : 'Secondary';

        // Create the image record
        $image = $facility->images()->create([
            'image_url' => $validated['image_url'],
            'image_type' => $imageType,
            'cloudinary_public_id' => $validated['cloudinary_public_id'],
            'description' => $validated['description'] ?? 'Facility photo',
            'sort_order' => $facility->images()->count() + 1
        ]);

        \Log::info('Image reference saved successfully', [
            'facility' => $facilityId,
            'image_id' => $image->image_id,
            'public_id' => $validated['cloudinary_public_id']
        ]);

        return response()->json([
            'message' => 'Image reference saved successfully',
            'image_id' => $image->image_id,
            'type' => $imageType
        ]);

    } catch (\Exception $e) {
        \Log::error('Error saving image reference', [
            'error' => $e->getMessage(),
            'facilityId' => $facilityId,
            'request_data' => $request->all()
        ]);

        return response()->json([
            'message' => 'Failed to save image reference: ' . $e->getMessage()
        ], 500);
    }
}

    // ----- Delete Facility Image ----- //
public function deleteImage($facilityId, $imageId): JsonResponse
{
    \Log::info('Attempting to delete facility image', [
        'facilityId' => $facilityId,
        'imageId' => $imageId
    ]);

    try {
        // Find the facility
        $facility = Facility::findOrFail($facilityId);

        // Find the image
        $image = $facility->images()->findOrFail($imageId);

        // Store public ID before deletion
        $publicId = $image->cloudinary_public_id;

        // Delete DB record first
        $image->delete();

        // Delete from Cloudinary if public ID exists (skip default placeholder)
        if ($publicId && $publicId !== 'oxvsxogzu9koqhctnf7s') {
            try {
                Cloudinary::destroy($publicId);
            } catch (\Exception $cloudinaryError) {
                \Log::error('Cloudinary delete failed but continuing', [
                    'error' => $cloudinaryError->getMessage(),
                    'public_id' => $publicId
                ]);
                // Continue even if Cloudinary delete fails
            }
        }

        // Reorder remaining images
        $this->reorderImageRecords($facility);

        return response()->json(['message' => 'Image deleted successfully']);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['message' => 'Image or facility not found'], 404);
    } catch (\Exception $e) {
        \Log::error('Error in deleteImage', [
            'error' => $e->getMessage(),
            'facilityId' => $facilityId,
            'imageId' => $imageId
        ]);
        return response()->json([
            'message' => 'Failed to delete image: ' . $e->getMessage()
        ], 500);
    }
}

    private function reorderImageRecords(Facility $facility): void
    {
        $images = $facility->images()->orderBy('sort_order')->get();
        foreach ($images as $index => $image) {
            $image->update(['sort_order' => $index + 1]);
        }
    }


}