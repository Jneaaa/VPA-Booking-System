<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Category;
use App\Models\Department;
use App\Models\Status;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class FacilityController extends Controller
{
    // ----- Index - Show all facilities ----- //
public function index(Request $request) // Add Request parameter
{
    \Log::info('FacilityController index method called'); 
    try {
        $user = auth()->user();
        
        // Get filter parameters
        $status = $request->input('status', 'all');
        $department = $request->input('department', 'all');
        $category = $request->input('category', 'all');
        $search = $request->input('search', '');
        
        // Base query
        $query = Facility::with(['category', 'subcategory', 'status', 'department', 'images']);
        
        // Apply filters based on user role
        if ($user->role?->role_title !== 'Head Admin') {
            $userDepartments = $user->departments->pluck('department_id');
            if ($userDepartments->isNotEmpty()) {
                $query->whereIn('department_id', $userDepartments);
            } else {
                // If user has no departments, return empty results
                $query->where('department_id', 0); // Force no results
            }
        }
        
        // Apply filters
        if ($status !== 'all') {
            $query->where('status_id', $status);
        }
        
        if ($department !== 'all') {
            $query->where('department_id', $department);
        }
        
        if ($category !== 'all') {
            $query->where('category_id', $category);
        }
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_note', 'like', "%{$search}%");
            });
        }
        
        // Get the facilities - THIS WAS MISSING
        $facilities = $query->paginate(10); // or ->get() if you don't want pagination
        
        // Get filter options - Use correct model classes
        $statuses = Status::all(); // Changed from AvailabilityStatus
        $departments = Department::all();
        $categories = Category::all(); // Changed from FacilityCategory
        
        // Debug: Check if variables are set
         \Log::debug('Variables being passed to view:');
        \Log::debug('Facilities count: ' . $facilities->count());
        \Log::debug('Statuses count: ' . $statuses->count());
        \Log::debug('Departments count: ' . $departments->count());
        \Log::debug('Categories count: ' . $categories->count());
        
         return view('admin.manage-facilities', compact('facilities', 'statuses', 'departments', 'categories'));
        
    } catch (\Exception $e) {
        \Log::error('Error loading manage facilities page: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return redirect()->route('admin.dashboard')->with('error', 'Failed to load facilities management page');
    }
}

    // ----- Create - Show add facility form ----- //
    public function create()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $departments = Department::all();
        $statuses = Status::all();

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
    public function edit($id)
    {
        $facility = Facility::with(['category', 'subcategory', 'status', 'department', 'images'])
            ->findOrFail($id);
            
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $departments = Department::all();
        $statuses = Status::all();

        return view('admin.edit-facility', compact('facility', 'categories', 'subcategories', 'departments', 'statuses'));
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
        $facility = Facility::with(['category', 'subcategory', 'status', 'department', 'images'])
            ->findOrFail($id);

        return view('admin.view-facility', compact('facility'));
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
    }} 