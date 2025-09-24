<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Admin;
use App\Models\LookupTables\AdminRole;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AdminController extends Controller
{
    // Add this new method
    public function adminRoles()
    {
        $roles = AdminRole::select('role_id', 'role_title')->get();
        \Log::debug('Fetched admin roles:', $roles->toArray());
        return response()->json($roles);
    }

    // Assign department to admin
    public function assignDepartment(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,department_id',
            'is_primary' => 'sometimes|boolean'
        ]);

        $departmentId = $validated['department_id'];
        $isPrimary = $validated['is_primary'] ?? false;

        // Explicitly load department (forces use of Department model)
        $department = Department::findOrFail($departmentId);

        // Check if admin already has a different primary department
        if ($isPrimary) {
            $alreadyPrimary = $admin->departments()
                ->wherePivot('is_primary', true)
                ->where('department_id', '!=', $departmentId)
                ->exists();

            if ($alreadyPrimary) {
                return response()->json([
                    'message' => 'This admin already has a primary department.'
                ], 409);
            }
        }

        // Assign department to admin
        $admin->departments()->syncWithoutDetaching([
            $department->department_id => ['is_primary' => $isPrimary]
        ]);

        return response()->json(['message' => 'Department assigned successfully']);
    }

    // Get admin's departments with department names and primary flag
    public function getAdminDepartments(Admin $admin)
    {
        $departments = $admin->departments()
            ->select('departments.*', 'admin_departments.is_primary')
            ->get();

        return response()->json($departments);
    }

    // Get all admin information
    public function getAllAdmins()
    {
        $currentAdminId = auth()->id();
        $admins = Admin::with(['role', 'departments'])
            ->where('admin_id', '!=', $currentAdminId)  // Changed to explicit inequality check
            ->get();
        return response()->json($admins);
    }

    // Get information of a single admin
    public function getAdminInfo(Admin $admin)
    {
        $admin->load('departments'); // Ensure departments are included
        return response()->json($admin);
    }

    // Add a new method to create admin records
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:admins,email|max:150',
            'contact_number' => 'nullable|string|max:20',
            'role_id' => 'required|exists:admin_roles,role_id',
            'school_id' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|max:50',
            'department_ids' => 'nullable|array', // Add this line
            'department_ids.*' => 'exists:departments,department_id', // Add this line
            'photo_url' => 'nullable|string',
            'photo_public_id' => 'nullable|string',
            'wallpaper_url' => 'nullable|string',
            'wallpaper_public_id' => 'nullable|string',
        ]);

        // Set default photo if not provided
        $validated['photo_url'] = $validated['photo_url'] ?? 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png';
        $validated['photo_public_id'] = $validated['photo_public_id'] ?? 'ksdmh4mmpxdtjogdgjmm';

        // Hash the password
        $validated['hashed_password'] = bcrypt($validated['password']);
        unset($validated['password']); // Remove the plain password

        // Extract department_ids before creating admin
        $departmentIds = $validated['department_ids'] ?? null;
        unset($validated['department_ids']);

        // Start transaction for data consistency
        \DB::beginTransaction();
        try {
            // Create new admin
            $admin = Admin::create($validated);

            // Handle department assignments
            if ($admin->role_id == 1) {
                // Head Admin gets all departments automatically
                $this->assignAllDepartmentsToAdmin($admin);
                \Log::info("Assigned all departments to new Head Admin: {$admin->admin_id}");
            } elseif ($departmentIds !== null && !empty($departmentIds)) {
                // For non-Head Admin roles, assign selected departments
                $this->assignSelectedDepartmentsToAdmin($admin, $departmentIds);
                \Log::info("Assigned selected departments to new admin: {$admin->admin_id}", [
                    'departments' => $departmentIds
                ]);
            }

            \DB::commit();

            return response()->json([
                'message' => 'Admin created successfully',
                'admin' => $admin->load('departments')
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating admin: ' . $e->getMessage(), [
                'request_data' => $request->except('password'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to create admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign selected departments to an admin with the first one as primary
     */
    private function assignSelectedDepartmentsToAdmin(Admin $admin, array $departmentIds)
    {
        try {
            $departmentData = [];
            $firstDepartment = true;

            foreach ($departmentIds as $deptId) {
                $departmentData[$deptId] = [
                    'is_primary' => $firstDepartment,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $firstDepartment = false;
            }

            $admin->departments()->sync($departmentData);
            \Log::info("Successfully assigned " . count($departmentIds) . " departments to admin: {$admin->admin_id}");

        } catch (\Exception $e) {
            \Log::error("Failed to assign departments to admin {$admin->admin_id}: " . $e->getMessage());
            throw $e; // Re-throw to handle in the main method
        }
    }

    // Delete an admin
    public function deleteAdmin(Admin $admin)
    {
        try {
            $admin->departments()->detach(); // Remove department associations
            $admin->delete();

            return response()->json([
                'message' => 'Admin deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'email' => 'required|email|max:150|unique:admins,email,' . $admin->admin_id . ',admin_id',
            'contact_number' => 'nullable|string|max:20',
            'role_id' => 'required|exists:admin_roles,role_id',
            'school_id' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|max:50',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,department_id'
        ]);

        // Store old role for comparison
        $oldRoleId = $admin->role_id;
        $newRoleId = $validated['role_id'];

        // Update password only if provided
        if (!empty($validated['password'])) {
            $validated['hashed_password'] = bcrypt($validated['password']);
            unset($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Remove department_ids from validated data before updating admin
        $departmentIds = $validated['department_ids'] ?? null;
        unset($validated['department_ids']);

        // Start transaction for data consistency
        \DB::beginTransaction();
        try {
            // Update admin basic info
            $admin->update($validated);

            // Handle department assignments
            if ($newRoleId == 1) {
                // Head Admin gets all departments automatically
                $this->assignAllDepartmentsToAdmin($admin);
                \Log::info("Assigned all departments to Head Admin: {$admin->admin_id}");
            } elseif ($departmentIds !== null) {
                // For non-Head Admin roles, assign selected departments
                $this->assignSelectedDepartmentsToAdmin($admin, $departmentIds);
                \Log::info("Updated departments for admin {$admin->admin_id}", [
                    'assigned_departments' => $departmentIds
                ]);
            }
            // Handle role change logic
            if ($oldRoleId != 1 && $newRoleId == 1) {
                $this->assignAllDepartmentsToAdmin($admin);
                \Log::info("Assigned all departments to admin {$admin->admin_id} after role change to Head Admin");
            } elseif ($oldRoleId == 1 && $newRoleId != 1) {
                // When changing from Head Admin to another role, remove all departments
                // unless specific departments were provided
                if ($departmentIds === null || empty($departmentIds)) {
                    $admin->departments()->detach();
                    \Log::info("Removed all departments from admin {$admin->admin_id} after role change from Head Admin");
                }
            }

            \DB::commit();

            return response()->json([
                'message' => 'Admin updated successfully',
                'admin' => $admin->load([
                    'departments' => function ($query) {
                        $query->select('departments.*', 'admin_departments.is_primary');
                    }
                ])
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating admin profile: ' . $e->getMessage(), [
                'admin_id' => $admin->admin_id,
                'request_data' => $request->except('password'),
                'trace' => $e->getTraceAsString() // Added for better debugging
            ]);

            return response()->json([
                'message' => 'Failed to update admin profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign all departments to an admin with the first one as primary
     */
    private function assignAllDepartmentsToAdmin(Admin $admin)
    {
        try {
            $departments = Department::all();

            if ($departments->isEmpty()) {
                \Log::warning("No departments found to assign to Head Admin: {$admin->admin_id}");
                return;
            }

            $departmentData = [];
            $firstDepartment = true;

            foreach ($departments as $department) {
                $departmentData[$department->department_id] = [
                    'is_primary' => $firstDepartment,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $firstDepartment = false;
            }

            $admin->departments()->sync($departmentData);
            \Log::info("Successfully assigned {$departments->count()} departments to Head Admin: {$admin->admin_id}");

        } catch (\Exception $e) {
            \Log::error("Failed to assign all departments to Head Admin {$admin->admin_id}: " . $e->getMessage());
        }
    }

    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required_without:wallpaper|image|max:2048',
                'wallpaper' => 'required_without:photo|image|max:5120',
                'type' => 'required|in:photo,wallpaper'
            ]);

            $admin = $request->user();
            $type = $request->type;
            $file = $request->file($type); // Either 'photo' or 'wallpaper' file

            if (!$file) {
                throw new \Exception('No file provided');
            }

            // Define config based on type
            $config = [
                'photo' => [
                    'folder' => 'admin-photos',
                    'transformation' => ['width' => 400, 'height' => 400, 'crop' => 'fill', 'gravity' => 'face'],
                    'url_field' => 'photo_url',
                    'public_id_field' => 'photo_public_id'
                ],
                'wallpaper' => [
                    'folder' => 'admin-wallpapers',
                    'transformation' => ['width' => 1920, 'height' => 400, 'crop' => 'fill'],
                    'url_field' => 'wallpaper_url',
                    'public_id_field' => 'wallpaper_public_id'
                ]
            ][$type];

            // Delete old image if exists and not default
            $oldPublicId = $admin->{$config['public_id_field']};
            $defaultIds = ['ksdmh4mmpxdtjogdgjmm', 'verzp7lqedwsfn3hz8xf'];

            if ($oldPublicId && !in_array($oldPublicId, $defaultIds)) {
                try {
                    Cloudinary::destroy($oldPublicId);
                } catch (\Exception $e) {
                    \Log::warning("Failed to delete old image: {$oldPublicId}");
                }
            }

            // Upload new image
            $result = Cloudinary::upload($file->getRealPath(), [
                'folder' => $config['folder'],
                'transformation' => $config['transformation']
            ]);

            // Update admin record with new image info
            $updateData = [
                $config['url_field'] => $result->getSecurePath(),
                $config['public_id_field'] => $result->getPublicId()
            ];

            $admin->update($updateData);

            return response()->json([
                'message' => ucfirst($type) . ' updated successfully',
                $type . '_url' => $result->getSecurePath(),
                $type . '_public_id' => $result->getPublicId()
            ]);

        } catch (\Exception $e) {
            \Log::error('Photo upload error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update ' . ($request->type ?? 'image'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updatePhotoRecords(Request $request)
    {
        try {
            $validated = $request->validate([
                'photo_url' => 'nullable|string',
                'photo_public_id' => 'nullable|string',
                'wallpaper_url' => 'nullable|string',
                'wallpaper_public_id' => 'nullable|string',
                'type' => 'required|in:photo,wallpaper'
            ]);

            $admin = $request->user();
            $type = $validated['type'];

            $updateData = [];
            if ($type === 'photo') {
                $updateData['photo_url'] = $validated['photo_url'];
                $updateData['photo_public_id'] = $validated['photo_public_id'];
            } else {
                $updateData['wallpaper_url'] = $validated['wallpaper_url'];
                $updateData['wallpaper_public_id'] = $validated['wallpaper_public_id'];
            }

            $admin->update($updateData);

            \Log::info("Admin {$admin->admin_id} updated {$type} records", $updateData);

            return response()->json([
                'message' => ucfirst($type) . ' updated successfully',
                'admin' => $admin->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating photo records: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update records',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteCloudinaryImage(Request $request)
    {
        try {
            $validated = $request->validate([
                'public_id' => 'required|string',
                'type' => 'required|in:photo,wallpaper'
            ]);

            $publicId = $validated['public_id'];
            $type = $validated['type'];

            // Skip deletion for default images
            $defaultIds = ['ksdmh4mmpxdtjogdgjmm', 'verzp7lqedwsfn3hz8xf'];
            if (in_array($publicId, $defaultIds)) {
                return response()->json([
                    'message' => 'Default image preserved',
                    'deleted' => false
                ]);
            }

            \Log::info("Attempting to delete {$type} from Cloudinary", [
                'admin_id' => $request->user()->admin_id,
                'public_id' => $publicId
            ]);

            // Use Cloudinary API directly
            $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
            $api = $cloudinary->adminApi();

            // For simple image deletion, use the upload API destroy method
            $result = $cloudinary->uploadApi()->destroy($publicId, [
                'invalidate' => true
            ]);

            \Log::info("Cloudinary deletion result for {$publicId}:", ['result' => $result]);

            if ($result->getArrayCopy()['result'] === 'ok') {
                return response()->json([
                    'message' => 'Image deleted successfully from Cloudinary',
                    'deleted' => true,
                    'result' => $result->getArrayCopy()
                ]);
            } else {
                throw new \Exception('Cloudinary deletion failed');
            }

        } catch (\Exception $e) {
            \Log::error('Error deleting Cloudinary image: ' . $e->getMessage(), [
                'public_id' => $validated['public_id'] ?? 'unknown',
                'type' => $validated['type'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to delete image from Cloudinary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}